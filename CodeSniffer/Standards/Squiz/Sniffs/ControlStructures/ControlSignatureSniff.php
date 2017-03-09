<?php
/**
 * Verifies that control statements conform to their coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Verifies that control statements conform to their coding standards.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_ControlStructures_ControlSignatureSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * How many spaces should precede the colon if using alternative syntax.
     *
     * @var int
     */
    public $requiredSpacesBeforeColon = 1;

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return array(
                T_TRY,
                T_CATCH,
                T_DO,
                T_WHILE,
                T_FOR,
                T_IF,
                T_FOREACH,
                T_ELSE,
                T_ELSEIF,
                T_SWITCH,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[($stackPtr + 1)]) === false) {
            return;
        }

        $isAlternative = false;
        if (isset($tokens[$stackPtr]['scope_opener']) === true
            && $tokens[$tokens[$stackPtr]['scope_opener']]['code'] === T_COLON
        ) {
            $isAlternative = true;
        }

        $expectedSpaces = 1;
        if (isset($tokens[$stackPtr]['parenthesis_closer']) === false && $isAlternative === true) {
            $expectedSpaces = (int) $this->requiredSpacesBeforeColon;
        }

        // Single space after the keyword.
        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $found = 0;
        } else if (strpos($tokens[($stackPtr + 1)]['content'], $phpcsFile->eolChar) !== false) {
            $found = 'newline';
        } else {
            $found = strlen($tokens[($stackPtr + 1)]['content']);
        }

        if ($found !== $expectedSpaces) {
            $error = 'Expected %s space(s) after %s keyword; %s found';
            $data  = array(
                      $expectedSpaces,
                      strtoupper($tokens[$stackPtr]['content']),
                      $found,
                     );

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword', $data);
            if ($fix === true) {
                if ($found === 0) {
                    $phpcsFile->fixer->addContent($stackPtr, str_repeat(' ', $expectedSpaces));
                } else {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), str_repeat(' ', $expectedSpaces));
                }
            }
        }

        // Single space after closing parenthesis.
        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true
            && isset($tokens[$stackPtr]['scope_opener']) === true
        ) {
            $closer  = $tokens[$stackPtr]['parenthesis_closer'];
            $opener  = $tokens[$stackPtr]['scope_opener'];
            $content = $phpcsFile->getTokensAsString(($closer + 1), ($opener - $closer - 1));

            $expectedSpaces = 1;
            if ($isAlternative === true) {
                $expectedSpaces = (int) $this->requiredSpacesBeforeColon;
            }

            if (trim($content) === '') {
                if (strpos($content, $phpcsFile->eolChar) !== false) {
                    $found = 'newline';
                } else {
                    $found = strlen($content);
                }
            } else {
                $found = '"'.str_replace($phpcsFile->eolChar, '\n', $content).'"';
            }

            if ($found !== $expectedSpaces) {
                $error = 'Expected %s space(s) after closing parenthesis; found %s';

                $fix = $phpcsFile->addFixableError($error, $closer, 'SpaceAfterCloseParenthesis', array($expectedSpaces, $found));
                if ($fix === true) {
                    if ($closer === ($opener - 1)) {
                        $phpcsFile->fixer->addContent($closer, str_repeat(' ', $expectedSpaces));
                    } else {
                        $phpcsFile->fixer->beginChangeset();

                        if (trim($content) === '') {
                            $phpcsFile->fixer->addContent($closer, str_repeat(' ', $expectedSpaces));
                            if ($found !== 0) {
                                for ($i = ($closer + 1); $i < $opener; $i++) {
                                    $phpcsFile->fixer->replaceToken($i, '');
                                }
                            }
                        } else {
                            $phpcsFile->fixer->addContent($closer, str_repeat(' ', $expectedSpaces).$tokens[$opener]['content']);
                            $phpcsFile->fixer->replaceToken($opener, '');

                            if ($tokens[$opener]['line'] !== $tokens[$closer]['line']) {
                                $next = $phpcsFile->findNext(T_WHITESPACE, ($opener + 1), null, true);
                                if ($tokens[$next]['line'] !== $tokens[$opener]['line']) {
                                    for ($i = ($opener + 1); $i < $next; $i++) {
                                        $phpcsFile->fixer->replaceToken($i, '');
                                    }
                                }
                            }
                        }

                        $phpcsFile->fixer->endChangeset();
                    }//end if
                }//end if
            }//end if
        }//end if

        // Single newline after opening brace.
        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $opener = $tokens[$stackPtr]['scope_opener'];
            for ($next = ($opener + 1); $next < $phpcsFile->numTokens; $next++) {
                $code = $tokens[$next]['code'];

                if ($code === T_WHITESPACE
                    || ($code === T_INLINE_HTML
                    && trim($tokens[$next]['content']) === '')
                ) {
                    continue;
                }

                // Skip all empty tokens on the same line as the opener.
                if ($tokens[$next]['line'] === $tokens[$opener]['line']
                    && (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$code]) === true
                    || $code === T_CLOSE_TAG)
                ) {
                    continue;
                }

                // We found the first bit of a code, or a comment on the
                // following line.
                break;
            }//end for

            if ($tokens[$next]['line'] === $tokens[$opener]['line']) {
                $error = 'Newline required after opening brace';
                $fix   = $phpcsFile->addFixableError($error, $opener, 'NewlineAfterOpenBrace');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($opener + 1); $i < $next; $i++) {
                        if (trim($tokens[$i]['content']) !== '') {
                            break;
                        }

                        // Remove whitespace.
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addContent($opener, $phpcsFile->eolChar);
                    $phpcsFile->fixer->endChangeset();
                }
            }//end if
        } else if ($tokens[$stackPtr]['code'] === T_WHILE) {
            // Zero spaces after parenthesis closer.
            $closer = $tokens[$stackPtr]['parenthesis_closer'];
            $found  = 0;
            if ($tokens[($closer + 1)]['code'] === T_WHITESPACE) {
                if (strpos($tokens[($closer + 1)]['content'], $phpcsFile->eolChar) !== false) {
                    $found = 'newline';
                } else {
                    $found = strlen($tokens[($closer + 1)]['content']);
                }
            }

            if ($found !== 0) {
                $error = 'Expected 0 spaces before semicolon; %s found';
                $data  = array($found);
                $fix   = $phpcsFile->addFixableError($error, $closer, 'SpaceBeforeSemicolon', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($closer + 1), '');
                }
            }
        }//end if

        // Only want to check multi-keyword structures from here on.
        if ($tokens[$stackPtr]['code'] === T_DO) {
            if (isset($tokens[$stackPtr]['scope_closer']) === false) {
                return;
            }

            $closer = $tokens[$stackPtr]['scope_closer'];
        } else if ($tokens[$stackPtr]['code'] === T_ELSE
            || $tokens[$stackPtr]['code'] === T_ELSEIF
            || $tokens[$stackPtr]['code'] === T_CATCH
        ) {
            $closer = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($closer === false || $tokens[$closer]['code'] !== T_CLOSE_CURLY_BRACKET) {
                return;
            }
        } else {
            return;
        }

        // Single space after closing brace.
        $found = 1;
        if ($tokens[($closer + 1)]['code'] !== T_WHITESPACE) {
            $found = 0;
        } else if ($tokens[($closer + 1)]['content'] !== ' ') {
            if (strpos($tokens[($closer + 1)]['content'], $phpcsFile->eolChar) !== false) {
                $found = 'newline';
            } else {
                $found = strlen($tokens[($closer + 1)]['content']);
            }
        }

        if ($found !== 1) {
            $error = 'Expected 1 space after closing brace; %s found';
            $data  = array($found);
            $fix   = $phpcsFile->addFixableError($error, $closer, 'SpaceAfterCloseBrace', $data);
            if ($fix === true) {
                if ($found === 0) {
                    $phpcsFile->fixer->addContent($closer, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken(($closer + 1), ' ');
                }
            }
        }

    }//end process()


}//end class
