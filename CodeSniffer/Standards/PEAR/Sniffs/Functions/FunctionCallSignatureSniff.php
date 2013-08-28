<?php
/**
 * PEAR_Sniffs_Functions_FunctionCallSignatureSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * PEAR_Sniffs_Functions_FunctionCallSignatureSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PEAR_Sniffs_Functions_FunctionCallSignatureSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    public $indent = 4;

    /**
     * If TRUE, multiple arguments can be defined per line in a multi-line call.
     *
     * @var bool
     */
    public $allowMultipleArguments = true;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the next non-empty token.
        $openBracket = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        if (isset($tokens[$openBracket]['parenthesis_closer']) === false) {
            // Not a function call.
            return;
        }

        // Find the previous non-empty token.
        $search   = PHP_CodeSniffer_Tokens::$emptyTokens;
        $search[] = T_BITWISE_AND;
        $previous = $phpcsFile->findPrevious($search, ($stackPtr - 1), null, true);
        if ($tokens[$previous]['code'] === T_FUNCTION) {
            // It's a function definition, not a function call.
            return;
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        if (($stackPtr + 1) !== $openBracket) {
            // Checking this: $value = my_function[*](...).
            $error = 'Space before opening parenthesis of function call prohibited';
            $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeOpenBracket');
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), null, true);
        if ($tokens[$next]['code'] === T_SEMICOLON) {
            if (in_array($tokens[($closeBracket + 1)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                $error = 'Space after closing parenthesis of function call prohibited';
                $phpcsFile->addError($error, $closeBracket, 'SpaceAfterCloseBracket');
            }
        }

        // Check if this is a single line or multi-line function call.
        if ($tokens[$openBracket]['line'] === $tokens[$closeBracket]['line']) {
            $this->processSingleLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        } else {
            $this->processMultiLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        }

    }//end process()


    /**
     * Processes single-line calls.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                  $stackPtr    The position of the current token
     *                                          in the stack passed in $tokens.
     * @param int                  $openBracket The position of the opening bracket
     *                                          in the stack passed in $tokens.
     * @param array                $tokens      The stack of tokens that make up
     *                                          the file.
     *
     * @return void
     */
    public function processSingleLineCall(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
            // Checking this: $value = my_function([*]...).
            $error = 'Space after opening parenthesis of function call prohibited';
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterOpenBracket');
        }

        $closer = $tokens[$openBracket]['parenthesis_closer'];

        if ($tokens[($closer - 1)]['code'] === T_WHITESPACE) {
            // Checking this: $value = my_function(...[*]).
            $between = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), null, true);

            // Only throw an error if there is some content between the parenthesis.
            // i.e., Checking for this: $value = my_function().
            // If there is no content, then we would have thrown an error in the
            // previous IF statement because it would look like this:
            // $value = my_function( ).
            if ($between !== $closer) {
                $error = 'Space before closing parenthesis of function call prohibited';
                $phpcsFile->addError($error, $closer, 'SpaceBeforeCloseBracket');
            }
        }

    }//end processSingleLineCall()


    /**
     * Processes multi-line calls.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                  $stackPtr    The position of the current token
     *                                          in the stack passed in $tokens.
     * @param int                  $openBracket The position of the openning bracket
     *                                          in the stack passed in $tokens.
     * @param array                $tokens      The stack of tokens that make up
     *                                          the file.
     *
     * @return void
     */
    public function processMultiLineCall(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        // We need to work out how far indented the function
        // call itself is, so we can work out how far to
        // indent the arguments.
        $functionIndent = 0;
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                $i++;
                break;
            }
        }

        if ($tokens[$i]['code'] === T_WHITESPACE) {
            $functionIndent = strlen($tokens[$i]['content']);
        }

        if ($tokens[($openBracket + 1)]['content'] !== $phpcsFile->eolChar) {
            $error = 'Opening parenthesis of a multi-line function call must be the last content on the line';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ContentAfterOpenBracket');
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->addContent(
                    $openBracket,
                    $phpcsFile->eolChar.str_repeat(' ', ($functionIndent + $this->indent))
                );
            }
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $prev         = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBracket - 1), null, true);
        if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
            $error = 'Closing parenthesis of a multi-line function call must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'CloseBracketLine');
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->addContentBefore(
                    $closeBracket,
                    $phpcsFile->eolChar.str_repeat(' ', ($functionIndent + $this->indent))
                );
            }
        }

        // Each line between the parenthesis should be indented n spaces.
        $lastLine = $tokens[$openBracket]['line'];
        $exact    = true;
        $exactEnd = null;
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            if ($i === $exactEnd) {
                $exact = true;
            }

            if ($tokens[$i]['line'] !== $lastLine) {
                $lastLine = $tokens[$i]['line'];

                // Ignore heredoc indentation.
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$heredocTokens) === true) {
                    continue;
                }

                // Ignore multi-line string indentation.
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens) === true) {
                    if ($tokens[$i]['code'] === $tokens[($i - 1)]['code']) {
                        continue;
                    }
                }

                // We changed lines, so this should be a whitespace indent token, but first make
                // sure it isn't a blank line because we don't need to check indent unless there
                // is actually some code to indent.
                if ($tokens[$i]['code'] === T_WHITESPACE) {
                    $nextCode = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), ($closeBracket + 1), true);
                    if ($tokens[$nextCode]['line'] !== $lastLine) {
                        if ($exact === true) {
                            $error = 'Empty lines are not allowed in multi-line function calls';
                            $fix   = $phpcsFile->addFixableError($error, $i, 'EmptyLine');
                            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }
                        }

                        continue;
                    }
                } else {
                    $nextCode = $i;
                }

                // Check if the next line contains an object operator, if so rely on
                // the ObjectOperatorIndentSniff to test the indent.
                if ($tokens[$nextCode]['type'] === 'T_OBJECT_OPERATOR') {
                    continue;
                }

                if ($nextCode === $closeBracket) {
                    // Closing brace needs to be indented to the same level
                    // as the function call.
                    $expectedIndent = $functionIndent;
                } else {
                    $expectedIndent = ($functionIndent + $this->indent);
                }

                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    // Just check if it is a multi-line block comment. If so, we can
                    // calculate the indent from the whitespace before the content.
                    if ($tokens[$i]['code'] === T_COMMENT
                        && $tokens[($i - 1)]['code'] === T_COMMENT
                    ) {
                        $trimmed     = ltrim($tokens[$i]['content']);
                        $foundIndent = (strlen($tokens[$i]['content']) - strlen($trimmed));
                    } else {
                        $foundIndent = 0;
                    }
                } else {
                    $foundIndent = strlen($tokens[$i]['content']);
                }

                if ($foundIndent < $expectedIndent
                    || ($exact === true
                    && $expectedIndent !== $foundIndent)
                ) {
                    $error = 'Multi-line function call not indented correctly; expected %s spaces but found %s';
                    $data  = array(
                              $expectedIndent,
                              $foundIndent,
                             );

                    $fix = $phpcsFile->addFixableError($error, $i, 'Indent', $data);
                    if ($fix === true && $phpcsFile->fixer->enabled === true) {
                        $padding = str_repeat(' ', $expectedIndent);
                        if ($foundIndent === 0) {
                            $phpcsFile->fixer->addContentBefore($i, $padding);
                        } else {
                            $phpcsFile->fixer->replaceToken($i, $padding);
                        }
                    }
                }//end if
            }//end if

            // Turn off exact indent matching for some structures that typically
            // define their own indentation rules.
            if ($exact === true) {
                if ($tokens[$i]['code'] === T_CLOSURE) {
                    $exact    = false;
                    $exactEnd = $tokens[$i]['scope_closer'];
                } else if ($tokens[$i]['code'] === T_OPEN_SHORT_ARRAY) {
                    $exact    = false;
                    $exactEnd = $tokens[$i]['bracket_closer'];
                } else if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                    $exact    = false;
                    $exactEnd = $tokens[$i]['parenthesis_closer'];
                }
            } else {
                continue;
            }

            if ($this->allowMultipleArguments === false && $tokens[$i]['code'] === T_COMMA) {
                // Comma has to be the last token on the line.
                $next = $phpcsFile->findNext(array(T_WHITESPACE, T_COMMENT), ($i + 1), $closeBracket, true);
                if ($next !== false
                    && $tokens[$i]['line'] === $tokens[$next]['line']
                ) {
                    $error = 'Only one argument is allowed per line in a multi-line function call';
                    $phpcsFile->addError($error, $next, 'MultipleArguments');
                }
            }
        }//end for

    }//end processMultiLineCall()


}//end class
?>
