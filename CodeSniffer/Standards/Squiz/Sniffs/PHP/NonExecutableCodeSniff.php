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

        if ($tokens[$stackPtr]['code'] === T_RETURN) {
            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$next]['code'] === T_SEMICOLON) {
                $next = $phpcsFile->findNext(T_WHITESPACE, ($next + 1), null, true);
                if ($tokens[$next]['code'] === T_CLOSE_CURLY_BRACKET) {
                    // If this is the closing brace of a function
                    // then this return statement doesn't return anything
                    // and is not required anyway.
                    $owner = $tokens[$next]['scope_condition'];
                    if ($tokens[$owner]['code'] === T_FUNCTION) {
                        $warning = 'Empty return statement not required here';
                        $phpcsFile->addWarning($warning, $stackPtr, 'ReturnNotRequired');
                        return;
                    }
                }
            }
        }

        if ($tokens[$stackPtr]['code'] === T_BREAK
            && isset($tokens[$stackPtr]['scope_opener']) === true
        ) {
            // This break closes the scope of a CASE or DEFAULT statement
            // so any code between this token and the next CASE, DEFAULT or
            // end of SWITCH token will not be executable.
            $next = $phpcsFile->findNext(
                array(T_CASE, T_DEFAULT, T_CLOSE_CURLY_BRACKET),
                ($stackPtr + 1)
            );

            if ($next !== false) {
                $lastLine = $tokens[($stackPtr + 1)]['line'];
                for ($i = ($stackPtr + 1); $i < $next; $i++) {
                    if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                        continue;
                    }

                    $line = $tokens[$i]['line'];
                    if ($line > $lastLine) {
                        $type    = substr($tokens[$stackPtr]['type'], 2);
                        $warning = "Code after $type statement cannot be executed";
                        $phpcsFile->addWarning($warning, $i, 'Unreachable');
                        $lastLine = $line;
                    }
                }
            }//end if

            // That's all we have to check for these types of BREAK statements.
            return;
        }//end if

        // This token may be part of an inline condition.
        // If we find a closing parenthesis that belongs to a condition
        // we should ignore this token.
        $prev = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (isset($tokens[$prev]['parenthesis_owner']) === true) {
            $owner  = $tokens[$prev]['parenthesis_owner'];
            $ignore = array(
                       T_IF,
                       T_ELSE,
                       T_ELSEIF,
                      );
            if (in_array($tokens[$owner]['code'], $ignore) === true) {
                return;
            }
        }

        $ourConditions = array_keys($tokens[$stackPtr]['conditions']);
        $ourTokens     = $this->register();
        $hasConditions = empty($ourConditions);

        // Skip this token if it is non-executable code itself.
        if ($hasConditions === false) {
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

        if ($hasConditions === false) {
            $condition = array_pop($ourConditions);

            if (isset($tokens[$condition]['scope_closer']) === false) {
                return;
            }

            $closer = $tokens[$condition]['scope_closer'];

            // If the closer for our condition is shared with other openers,
            // we will need to throw errors from this token to the next
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
            // This token is in the global scope.
            if ($tokens[$stackPtr]['code'] === T_BREAK) {
                return;
            }

            // Throw an error for all lines until the end of the file.
            $start = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
            $end   = ($phpcsFile->numTokens - 1);
        }//end if

        $lastLine = $tokens[$start]['line'];
        for ($i = ($start + 1); $i < $end; $i++) {
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                continue;
            }

            $line = $tokens[$i]['line'];
            if ($line > $lastLine) {
                $type    = substr($tokens[$stackPtr]['type'], 2);
                $warning = "Code after $type statement cannot be executed";
                $phpcsFile->addWarning($warning, $i, 'Unreachable');
                $lastLine = $line;
            }
        }

    }//end process()


}//end class

?>
