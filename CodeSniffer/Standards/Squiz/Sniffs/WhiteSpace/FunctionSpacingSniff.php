<?php
/**
 * Squiz_Sniffs_Formatting_FunctionSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_WhiteSpace_FunctionSpacingSniff.
 *
 * Checks the separation between methods in a class or interface.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_WhiteSpace_FunctionSpacingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The number of blank lines between functions.
     *
     * @var int
     */
    public $spacing = 2;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);

    }//end register()


    /**
     * Processes this sniff when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens        = $phpcsFile->getTokens();
        $this->spacing = (int) $this->spacing;

        /*
            Check the number of blank lines
            after the function.
        */

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            // Must be an interface method, so the closer is the semi-colon.
            $closer = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
        } else {
            $closer = $tokens[$stackPtr]['scope_closer'];
        }

        $nextLineToken = null;
        for ($i = $closer; $i < $phpcsFile->numTokens; $i++) {
            if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false) {
                continue;
            } else {
                $nextLineToken = ($i + 1);
                if (isset($tokens[$nextLineToken]) === false) {
                    $nextLineToken = null;
                }

                break;
            }
        }

        $foundLines = 0;
        if (is_null($nextLineToken) === false) {
            $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($nextLineToken + 1), null, true);
            if ($nextContent === false) {
                // We are at the end of the file.
                // Don't check spacing after the function because this
                // should be done by an EOF sniff.
                $foundLines = $this->spacing;
            } else {
                $foundLines += ($tokens[$nextContent]['line'] - $tokens[$nextLineToken]['line']);
            }
        } else {
            // We are at the end of the file.
            // Don't check spacing after the function because this
            // should be done by an EOF sniff.
            $foundLines = $this->spacing;
        }

        if ($foundLines !== $this->spacing) {
            $error = 'Expected %s blank line';
            if ($this->spacing !== 1) {
                $error .= 's';
            }

            $error .= ' after function; %s found';
            $data   = array(
                       $this->spacing,
                       $foundLines,
                      );

            $fix = $phpcsFile->addFixableError($error, $closer, 'After', $data);
            if ($fix === true) {
                $nextSpace = $phpcsFile->findNext(T_WHITESPACE, ($closer + 1));
                if ($foundLines < $this->spacing) {
                    if ($nextSpace === false || $foundLines === 0) {
                        // Account for a comment after the closing brace.
                        $nextSpace = $closer;
                        if (isset($tokens[($closer + 1)]) === true
                            && $tokens[($closer + 1)]['code'] === T_COMMENT
                        ) {
                            $nextSpace++;
                        }
                    }

                    $padding = str_repeat($phpcsFile->eolChar, ($this->spacing - $foundLines));
                    $phpcsFile->fixer->addContent($nextSpace, $padding);
                } else {
                    $spacing = $this->spacing;
                    if ($tokens[($closer + 1)]['code'] === T_COMMENT) {
                        // Account for a comment after the closing brace.
                        $nextSpace++;
                        $spacing--;
                    }

                    if ($nextContent === ($phpcsFile->numTokens - 1)) {
                        $spacing--;
                    }

                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $nextSpace; $i < ($nextContent - 1); $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->replaceToken($i, str_repeat($phpcsFile->eolChar, $spacing));
                    $phpcsFile->fixer->endChangeset();
                }//end if
            }//end if
        }//end if

        /*
            Check the number of blank lines
            before the function.
        */

        $prevLineToken = null;
        for ($i = $stackPtr; $i > 0; $i--) {
            if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) === false) {
                continue;
            } else {
                $prevLineToken = $i;
                break;
            }
        }

        if (is_null($prevLineToken) === true) {
            // Never found the previous line, which means
            // there are 0 blank lines before the function.
            $foundLines  = 0;
            $prevContent = 0;
        } else {
            $prevContent = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $prevLineToken, null, true);

            // Before we throw an error, check that we are not throwing an error
            // for another function. We don't want to error for no blank lines after
            // the previous function and no blank lines before this one as well.
            $currentLine = $tokens[$stackPtr]['line'];
            $prevLine    = ($tokens[$prevContent]['line'] - 1);
            $i           = ($stackPtr - 1);
            $foundLines  = 0;
            while ($currentLine !== $prevLine && $currentLine > 1 && $i > 0) {
                if (isset($tokens[$i]['scope_condition']) === true) {
                    $scopeCondition = $tokens[$i]['scope_condition'];
                    if ($tokens[$scopeCondition]['code'] === T_FUNCTION) {
                        // Found a previous function.
                        return;
                    }
                } else if ($tokens[$i]['code'] === T_FUNCTION) {
                    // Found another interface function.
                    return;
                }

                $currentLine = $tokens[$i]['line'];
                if ($currentLine === $prevLine) {
                    break;
                }

                if ($tokens[($i - 1)]['line'] < $currentLine && $tokens[($i + 1)]['line'] > $currentLine) {
                    // This token is on a line by itself. If it is whitespace, the line is empty.
                    if ($tokens[$i]['code'] === T_WHITESPACE) {
                        $foundLines++;
                    }
                }

                $i--;
            }//end while
        }//end if

        if ($foundLines !== $this->spacing) {
            $error = 'Expected %s blank line';
            if ($this->spacing !== 1) {
                $error .= 's';
            }

            $error .= ' before function; %s found';
            $data   = array(
                       $this->spacing,
                       $foundLines,
                      );

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Before', $data);
            if ($fix === true) {
                if ($prevContent === 0) {
                    $nextSpace = 0;
                } else {
                    $nextSpace = $phpcsFile->findNext(T_WHITESPACE, ($prevContent + 1), $stackPtr);
                    if ($nextSpace === false) {
                        $nextSpace = ($stackPtr - 1);
                    }
                }

                if ($foundLines < $this->spacing) {
                    $padding = str_repeat($phpcsFile->eolChar, ($this->spacing - $foundLines));
                    $phpcsFile->fixer->addContent($nextSpace, $padding);
                } else {
                    $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($nextSpace + 1), null, true);
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $nextSpace; $i < ($nextContent - 1); $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->replaceToken($i, str_repeat($phpcsFile->eolChar, $this->spacing));
                    $phpcsFile->fixer->endChangeset();
                }
            }//end if
        }//end if

    }//end process()


}//end class
