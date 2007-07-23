<?php
/**
 * Squiz_Sniffs_PHP_InnerFunctionsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_PHP_NonExecutableCodeSniff.
 *
 * Warns about code that can never been executed. This happens when a function
 * returns before the code, or a break ends execution of a statement etc.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_PHP_NonExecutableCodeSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_BREAK,
                T_CONTINUE,
                T_RETURN,
                T_EXIT,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Break statements can themselves be scope closers, so it this
        // is a closer, skip it.
        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            return;
        }

        // Skip this token if it is non-executable code itself.
        if (empty($tokens[$stackPtr]['conditions']) === false) {
            $ourConditions = array_keys($tokens[$stackPtr]['conditions']);
            $ourTokens     = $this->register();

            for ($i = ($stackPtr - 1); $i >= 1; $i--) {
                // Skip tokens that close the scope. They don't end
                // the execution of code.
                if (isset($tokens[$i]['scope_opener']) === true) {
                    continue;
                }

                // Skip tokens that do not end execution.
                if (in_array($tokens[$i]['code'], $ourTokens) === false) {
                    continue;
                }

                if (empty($tokens[$i]['conditions']) === true) {
                    // Found an end of execution token in the global
                    // scope, so it will be executed before us.
                    return;
                }

                // If the deepest condition this token is in also happens
                // to be a condition we are in, it will get executed before us.
                $conditions = array_keys($tokens[$i]['conditions']);
                $condition  = array_pop($conditions);
                if (in_array($condition, $ourConditions) === true) {
                    return;
                }
            }//end for
        } else {
            // Look for other end of execution tokens in the global scope.
            for ($i = ($stackPtr - 1); $i >= 1; $i--) {
                $ourTokens = $this->register();
                if (in_array($tokens[$i]['code'], $ourTokens) === false) {
                    continue;
                }

                if (empty($tokens[$i]['conditions']) === false) {
                    continue;
                }

                // Another end of execution token was before us in the
                // global scope, so we are not executable.
                return;
            }
        }//end if

        if (empty($tokens[$stackPtr]['conditions']) === false) {
            $conditions = array_keys($tokens[$stackPtr]['conditions']);
            $condition  = array_pop($conditions);

            if (isset($tokens[$condition]['scope_closer']) === true) {
                $closer = $tokens[$condition]['scope_closer'];
                if ($tokens[$closer]['scope_condition'] !== $condition) {
                    // The closer for our condition is shared with other openers,
                    // so we need to throw errors from this token to the next
                    // shared opener (if there is one), not to the scope closer.
                    $nextOpener = null;
                    for ($i = ($stackPtr + 1); $i < $closer; $i++) {
                        if (isset($tokens[$i]['scope_closer']) === true) {
                            if ($tokens[$i]['scope_closer'] === $closer) {
                                // We found an opener that shares the same
                                // closing token as us.
                                $nextOpener = $i;
                                break;
                            }
                        }
                    }//end for

                    $start = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));

                    if ($nextOpener === null) {
                        $end = $closer;
                    } else {
                        $end = $nextOpener;
                    }
                } else {
                    // Any tokens between the return and the closer
                    // cannot be executed.
                    $start = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
                    $end   = $tokens[$condition]['scope_closer'];
                }//end if

                $lastLine = $tokens[$start]['line'];
                $endLine  = $tokens[$end]['line'];

                for ($i = ($start + 1); $i < $end; $i++) {
                    if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                        continue;
                    }

                    $line = $tokens[$i]['line'];
                    if ($line > $lastLine) {
                        $type    = substr($tokens[$stackPtr]['type'], 2);
                        $warning = "Code after $type statement cannot be executed";
                        $phpcsFile->addWarning($warning, $i);
                        $lastLine = $line;
                    }
                }
            }//end if
        } else {
            // This token is in the global scope.
            if ($tokens[$stackPtr]['code'] === T_BREAK) {
                return;
            }

            // Throw an error for all lines until the end of the file.
            $start = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
            $end   = (count($tokens) - 1);

            $lastLine = $tokens[$start]['line'];
            $endLine  = $tokens[$end]['line'];

            for ($i = ($start + 1); $i < $end; $i++) {
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                    continue;
                }

                $line = $tokens[$i]['line'];
                if ($line > $lastLine) {
                    $type    = substr($tokens[$stackPtr]['type'], 2);
                    $warning = "Code after $type statement cannot be executed";
                    $phpcsFile->addWarning($warning, $i);
                    $lastLine = $line;
                }
            }
        }//end if

    }//end process()


}//end class

?>
