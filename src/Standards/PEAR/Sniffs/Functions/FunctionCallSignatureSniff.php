<?php
/**
 * Ensures function calls are formatted correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class FunctionCallSignatureSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;

    /**
     * If TRUE, multiple arguments can be defined per line in a multi-line call.
     *
     * @var boolean
     */
    public $allowMultipleArguments = true;

    /**
     * How many spaces should follow the opening bracket.
     *
     * @var integer
     */
    public $requiredSpacesAfterOpen = 0;

    /**
     * How many spaces should precede the closing bracket.
     *
     * @var integer
     */
    public $requiredSpacesBeforeClose = 0;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $tokens = Tokens::$functionNameTokens;

        $tokens[] = T_VARIABLE;
        $tokens[] = T_CLOSE_CURLY_BRACKET;
        $tokens[] = T_CLOSE_SQUARE_BRACKET;
        $tokens[] = T_CLOSE_PARENTHESIS;

        return $tokens;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->requiredSpacesAfterOpen   = (int) $this->requiredSpacesAfterOpen;
        $this->requiredSpacesBeforeClose = (int) $this->requiredSpacesBeforeClose;
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_CLOSE_CURLY_BRACKET
            && isset($tokens[$stackPtr]['scope_condition']) === true
        ) {
            // Not a function call.
            return;
        }

        // Find the next non-empty token.
        $openBracket = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        if (isset($tokens[$openBracket]['parenthesis_closer']) === false) {
            // Not a function call.
            return;
        }

        // Find the previous non-empty token.
        $search   = Tokens::$emptyTokens;
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
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeOpenBracket');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($stackPtr + 1); $i < $openBracket; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                // Modify the bracket as well to ensure a conflict if the bracket
                // has been changed in some way by another sniff.
                $phpcsFile->fixer->replaceToken($openBracket, '(');
                $phpcsFile->fixer->endChangeset();
            }
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), null, true);
        if ($tokens[$next]['code'] === T_SEMICOLON) {
            if (isset(Tokens::$emptyTokens[$tokens[($closeBracket + 1)]['code']]) === true) {
                $error = 'Space after closing parenthesis of function call prohibited';
                $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'SpaceAfterCloseBracket');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($closeBracket + 1); $i < $next; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    // Modify the bracket as well to ensure a conflict if the bracket
                    // has been changed in some way by another sniff.
                    $phpcsFile->fixer->replaceToken($closeBracket, ')');
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }

        // Check if this is a single line or multi-line function call.
        if ($this->isMultiLineCall($phpcsFile, $stackPtr, $openBracket, $tokens) === true) {
            $this->processMultiLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        } else {
            $this->processSingleLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        }

    }//end process()


    /**
     * Determine if this is a multi-line function call.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $stackPtr    The position of the current token
     *                                                 in the stack passed in $tokens.
     * @param int                         $openBracket The position of the opening bracket
     *                                                 in the stack passed in $tokens.
     * @param array                       $tokens      The stack of tokens that make up
     *                                                 the file.
     *
     * @return bool
     */
    public function isMultiLineCall(File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        if ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']) {
            return true;
        }

        return false;

    }//end isMultiLineCall()


    /**
     * Processes single-line calls.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $stackPtr    The position of the current token
     *                                                 in the stack passed in $tokens.
     * @param int                         $openBracket The position of the opening bracket
     *                                                 in the stack passed in $tokens.
     * @param array                       $tokens      The stack of tokens that make up
     *                                                 the file.
     *
     * @return void
     */
    public function processSingleLineCall(File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        $closer = $tokens[$openBracket]['parenthesis_closer'];
        if ($openBracket === ($closer - 1)) {
            return;
        }

        // If the function call has no arguments or comments, enforce 0 spaces.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), $closer, true);
        if ($next === false) {
            $requiredSpacesAfterOpen   = 0;
            $requiredSpacesBeforeClose = 0;
        } else {
            $requiredSpacesAfterOpen   = $this->requiredSpacesAfterOpen;
            $requiredSpacesBeforeClose = $this->requiredSpacesBeforeClose;
        }

        if ($requiredSpacesAfterOpen === 0 && $tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
            // Checking this: $value = my_function([*]...).
            $error = 'Space after opening parenthesis of function call prohibited';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterOpenBracket');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($openBracket + 1), '');
            }
        } else if ($requiredSpacesAfterOpen > 0) {
            $spaceAfterOpen = 0;
            if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
                $spaceAfterOpen = $tokens[($openBracket + 1)]['length'];
            }

            if ($spaceAfterOpen !== $requiredSpacesAfterOpen) {
                $error = 'Expected %s spaces after opening parenthesis; %s found';
                $data  = [
                    $requiredSpacesAfterOpen,
                    $spaceAfterOpen,
                ];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterOpenBracket', $data);
                if ($fix === true) {
                    $padding = str_repeat(' ', $requiredSpacesAfterOpen);
                    if ($spaceAfterOpen === 0) {
                        $phpcsFile->fixer->addContent($openBracket, $padding);
                    } else {
                        $phpcsFile->fixer->replaceToken(($openBracket + 1), $padding);
                    }
                }
            }
        }//end if

        // Checking this: $value = my_function(...[*]).
        $spaceBeforeClose = 0;
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($closer - 1), $openBracket, true);
        if ($tokens[$prev]['code'] === T_END_HEREDOC || $tokens[$prev]['code'] === T_END_NOWDOC) {
            // Need a newline after these tokens, so ignore this rule.
            return;
        }

        if ($tokens[$prev]['line'] !== $tokens[$closer]['line']) {
            $spaceBeforeClose = 'newline';
        } else if ($tokens[($closer - 1)]['code'] === T_WHITESPACE) {
            $spaceBeforeClose = $tokens[($closer - 1)]['length'];
        }

        if ($spaceBeforeClose !== $requiredSpacesBeforeClose) {
            $error = 'Expected %s spaces before closing parenthesis; %s found';
            $data  = [
                $requiredSpacesBeforeClose,
                $spaceBeforeClose,
            ];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeCloseBracket', $data);
            if ($fix === true) {
                $padding = str_repeat(' ', $requiredSpacesBeforeClose);

                if ($spaceBeforeClose === 0) {
                    $phpcsFile->fixer->addContentBefore($closer, $padding);
                } else if ($spaceBeforeClose === 'newline') {
                    $phpcsFile->fixer->beginChangeset();

                    $closingContent = ')';

                    $next = $phpcsFile->findNext(T_WHITESPACE, ($closer + 1), null, true);
                    if ($tokens[$next]['code'] === T_SEMICOLON) {
                        $closingContent .= ';';
                        for ($i = ($closer + 1); $i <= $next; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                    }

                    // We want to jump over any whitespace or inline comment and
                    // move the closing parenthesis after any other token.
                    $prev = ($closer - 1);
                    while (isset(Tokens::$emptyTokens[$tokens[$prev]['code']]) === true) {
                        if (($tokens[$prev]['code'] === T_COMMENT)
                            && (strpos($tokens[$prev]['content'], '*/') !== false)
                        ) {
                            break;
                        }

                        $prev--;
                    }

                    $phpcsFile->fixer->addContent($prev, $padding.$closingContent);

                    $prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, ($closer - 1), null, true);
                    for ($i = ($prevNonWhitespace + 1); $i <= $closer; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                } else {
                    $phpcsFile->fixer->replaceToken(($closer - 1), $padding);
                }//end if
            }//end if
        }//end if

    }//end processSingleLineCall()


    /**
     * Processes multi-line calls.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $stackPtr    The position of the current token
     *                                                 in the stack passed in $tokens.
     * @param int                         $openBracket The position of the opening bracket
     *                                                 in the stack passed in $tokens.
     * @param array                       $tokens      The stack of tokens that make up
     *                                                 the file.
     *
     * @return void
     */
    public function processMultiLineCall(File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        // We need to work out how far indented the function
        // call itself is, so we can work out how far to
        // indent the arguments.
        $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $stackPtr, true);
        if ($tokens[$first]['code'] === T_CONSTANT_ENCAPSED_STRING
            && $tokens[($first - 1)]['code'] === T_CONSTANT_ENCAPSED_STRING
        ) {
            // We are in a multi-line string, so find the start and use
            // the indent from there.
            $prev  = $phpcsFile->findPrevious(T_CONSTANT_ENCAPSED_STRING, ($first - 2), null, true);
            $first = $phpcsFile->findFirstOnLine(Tokens::$emptyTokens, $prev, true);
            if ($first === false) {
                $first = ($prev + 1);
            }
        }

        $foundFunctionIndent = 0;
        if ($first !== false) {
            if ($tokens[$first]['code'] === T_INLINE_HTML
                || ($tokens[$first]['code'] === T_CONSTANT_ENCAPSED_STRING
                && $tokens[($first - 1)]['code'] === T_CONSTANT_ENCAPSED_STRING)
            ) {
                $trimmed = ltrim($tokens[$first]['content']);
                if ($trimmed === '') {
                    $foundFunctionIndent = strlen($tokens[$first]['content']);
                } else {
                    $foundFunctionIndent = (strlen($tokens[$first]['content']) - strlen($trimmed));
                }
            } else {
                $foundFunctionIndent = ($tokens[$first]['column'] - 1);
            }
        }

        // Make sure the function indent is divisible by the indent size.
        // We round down here because this accounts for times when the
        // surrounding code is indented a little too far in, and not correctly
        // at a tab stop. Without this, the function will be indented a further
        // $indent spaces to the right.
        $functionIndent = (int) (floor($foundFunctionIndent / $this->indent) * $this->indent);
        $adjustment     = 0;

        if ($foundFunctionIndent !== $functionIndent) {
            $error = 'Opening statement of multi-line function call not indented correctly; expected %s spaces but found %s';
            $data  = [
                $functionIndent,
                $foundFunctionIndent,
            ];

            $fix = $phpcsFile->addFixableError($error, $first, 'OpeningIndent', $data);
            if ($fix === true) {
                $adjustment = ($functionIndent - $foundFunctionIndent);
                $padding    = str_repeat(' ', $functionIndent);
                if ($foundFunctionIndent === 0) {
                    $phpcsFile->fixer->addContentBefore($first, $padding);
                } else {
                    $phpcsFile->fixer->replaceToken(($first - 1), $padding);
                }
            }
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($openBracket + 1), null, true);
        if ($tokens[$next]['line'] === $tokens[$openBracket]['line']) {
            $error = 'Opening parenthesis of a multi-line function call must be the last content on the line';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ContentAfterOpenBracket');
            if ($fix === true) {
                $phpcsFile->fixer->addContent(
                    $openBracket,
                    $phpcsFile->eolChar.str_repeat(' ', ($foundFunctionIndent + $this->indent))
                );
            }
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $prev         = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBracket - 1), null, true);
        if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
            $error = 'Closing parenthesis of a multi-line function call must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'CloseBracketLine');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore(
                    $closeBracket,
                    $phpcsFile->eolChar.str_repeat(' ', ($foundFunctionIndent + $this->indent))
                );
            }
        }

        // Each line between the parenthesis should be indented n spaces.
        $lastLine = ($tokens[$openBracket]['line'] - 1);
        $argStart = null;
        $argEnd   = null;

        // Start processing at the first argument.
        $i = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), null, true);

        if ($tokens[$i]['line'] > ($tokens[$openBracket]['line'] + 1)) {
            $error = 'The first argument in a multi-line function call must be on the line after the opening parenthesis';
            $fix   = $phpcsFile->addFixableError($error, $i, 'FirstArgumentPosition');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($x = ($openBracket + 1); $x < $i; $x++) {
                    if ($tokens[$x]['line'] === $tokens[$openBracket]['line']) {
                        continue;
                    }

                    if ($tokens[$x]['line'] === $tokens[$i]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($x, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }//end if

        $i = $phpcsFile->findNext(Tokens::$emptyTokens, ($openBracket + 1), null, true);

        if ($tokens[($i - 1)]['code'] === T_WHITESPACE
            && $tokens[($i - 1)]['line'] === $tokens[$i]['line']
        ) {
            // Make sure we check the indent.
            $i--;
        }

        for ($i; $i < $closeBracket; $i++) {
            if ($i > $argStart && $i < $argEnd) {
                $inArg = true;
            } else {
                $inArg = false;
            }

            if ($tokens[$i]['line'] !== $lastLine) {
                $lastLine = $tokens[$i]['line'];

                // Ignore heredoc indentation.
                if (isset(Tokens::$heredocTokens[$tokens[$i]['code']]) === true) {
                    continue;
                }

                // Ignore multi-line string indentation.
                if (isset(Tokens::$stringTokens[$tokens[$i]['code']]) === true
                    && $tokens[$i]['code'] === $tokens[($i - 1)]['code']
                ) {
                    continue;
                }

                // Ignore inline HTML.
                if ($tokens[$i]['code'] === T_INLINE_HTML) {
                    continue;
                }

                if ($tokens[$i]['line'] !== $tokens[$openBracket]['line']) {
                    // We changed lines, so this should be a whitespace indent token, but first make
                    // sure it isn't a blank line because we don't need to check indent unless there
                    // is actually some code to indent.
                    if ($tokens[$i]['code'] === T_WHITESPACE) {
                        $nextCode = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), ($closeBracket + 1), true);
                        if ($tokens[$nextCode]['line'] !== $lastLine) {
                            if ($inArg === false) {
                                $error = 'Empty lines are not allowed in multi-line function calls';
                                $fix   = $phpcsFile->addFixableError($error, $i, 'EmptyLine');
                                if ($fix === true) {
                                    $phpcsFile->fixer->replaceToken($i, '');
                                }
                            }

                            continue;
                        }
                    } else {
                        $nextCode = $i;
                    }

                    if ($tokens[$nextCode]['line'] === $tokens[$closeBracket]['line']) {
                        // Closing brace needs to be indented to the same level
                        // as the function call.
                        $inArg          = false;
                        $expectedIndent = ($foundFunctionIndent + $adjustment);
                    } else {
                        $expectedIndent = ($foundFunctionIndent + $this->indent + $adjustment);
                    }

                    if ($tokens[$i]['code'] !== T_WHITESPACE
                        && $tokens[$i]['code'] !== T_DOC_COMMENT_WHITESPACE
                    ) {
                        // Just check if it is a multi-line block comment. If so, we can
                        // calculate the indent from the whitespace before the content.
                        if ($tokens[$i]['code'] === T_COMMENT
                            && $tokens[($i - 1)]['code'] === T_COMMENT
                        ) {
                            $trimmedLength = strlen(ltrim($tokens[$i]['content']));
                            if ($trimmedLength === 0) {
                                // This is a blank comment line, so indenting it is
                                // pointless.
                                continue;
                            }

                            $foundIndent = (strlen($tokens[$i]['content']) - $trimmedLength);
                        } else {
                            $foundIndent = 0;
                        }
                    } else {
                        $foundIndent = $tokens[$i]['length'];
                    }

                    if ($foundIndent < $expectedIndent
                        || ($inArg === false
                        && $expectedIndent !== $foundIndent)
                    ) {
                        $error = 'Multi-line function call not indented correctly; expected %s spaces but found %s';
                        $data  = [
                            $expectedIndent,
                            $foundIndent,
                        ];

                        $fix = $phpcsFile->addFixableError($error, $i, 'Indent', $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();

                            $padding = str_repeat(' ', $expectedIndent);
                            if ($foundIndent === 0) {
                                $phpcsFile->fixer->addContentBefore($i, $padding);
                                if (isset($tokens[$i]['scope_opener']) === true) {
                                    $phpcsFile->fixer->changeCodeBlockIndent($i, $tokens[$i]['scope_closer'], $expectedIndent);
                                }
                            } else {
                                if ($tokens[$i]['code'] === T_COMMENT) {
                                    $comment = $padding.ltrim($tokens[$i]['content']);
                                    $phpcsFile->fixer->replaceToken($i, $comment);
                                } else {
                                    $phpcsFile->fixer->replaceToken($i, $padding);
                                }

                                if (isset($tokens[($i + 1)]['scope_opener']) === true) {
                                    $phpcsFile->fixer->changeCodeBlockIndent(($i + 1), $tokens[($i + 1)]['scope_closer'], ($expectedIndent - $foundIndent));
                                }
                            }

                            $phpcsFile->fixer->endChangeset();
                        }//end if
                    }//end if
                } else {
                    $nextCode = $i;
                }//end if

                if ($inArg === false) {
                    $argStart = $nextCode;
                    $argEnd   = $phpcsFile->findEndOfStatement($nextCode);
                }
            }//end if

            // If we are within an argument we should be ignoring commas
            // as these are not signalling the end of an argument.
            if ($inArg === false && $tokens[$i]['code'] === T_COMMA) {
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), $closeBracket, true);
                if ($next === false) {
                    continue;
                }

                if ($this->allowMultipleArguments === false) {
                    // Comma has to be the last token on the line.
                    if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                        $error = 'Only one argument is allowed per line in a multi-line function call';
                        $fix   = $phpcsFile->addFixableError($error, $next, 'MultipleArguments');
                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            for ($x = ($next - 1); $x > $i; $x--) {
                                if ($tokens[$x]['code'] !== T_WHITESPACE) {
                                    break;
                                }

                                $phpcsFile->fixer->replaceToken($x, '');
                            }

                            $phpcsFile->fixer->addContentBefore(
                                $next,
                                $phpcsFile->eolChar.str_repeat(' ', ($foundFunctionIndent + $this->indent))
                            );
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }//end if

                $argStart = $next;
                $argEnd   = $phpcsFile->findEndOfStatement($next);
            }//end if
        }//end for

    }//end processMultiLineCall()


}//end class
