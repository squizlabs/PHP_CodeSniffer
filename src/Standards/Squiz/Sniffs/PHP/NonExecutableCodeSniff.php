<?php
/**
 * Warns about code that can never been executed.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class NonExecutableCodeSniff implements Sniff
{

    /**
     * Tokens for terminating expressions, which can be used inline.
     *
     * This is in contrast to terminating statements, which cannot be used inline
     * and would result in a parse error (which is not the concern of this sniff).
     *
     * `throw` can be used as an expression since PHP 8.0.
     * {@link https://wiki.php.net/rfc/throw_expression}
     *
     * @var array
     */
    private $expressionTokens = [
        T_EXIT  => T_EXIT,
        T_THROW => T_THROW,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_BREAK,
            T_CONTINUE,
            T_RETURN,
            T_THROW,
            T_EXIT,
            T_GOTO,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        // Tokens which can be used in inline expressions need special handling.
        if (isset($this->expressionTokens[$tokens[$stackPtr]['code']]) === true) {
            // If this token is preceded by a logical operator, it only relates to one line
            // and should be ignored. For example: fopen() or die().
            // Note: There is one exception: throw expressions can not be used with xor.
            if (isset(Tokens::$booleanOperators[$tokens[$prev]['code']]) === true
                && ($tokens[$stackPtr]['code'] === T_THROW && $tokens[$prev]['code'] === T_LOGICAL_XOR) === false
            ) {
                return;
            }

            // Expressions are allowed in the `else` clause of ternaries.
            if ($tokens[$prev]['code'] === T_INLINE_THEN || $tokens[$prev]['code'] === T_INLINE_ELSE) {
                return;
            }

            // Expressions are allowed with PHP 7.0+ null coalesce and PHP 7.4+ null coalesce equals.
            if ($tokens[$prev]['code'] === T_COALESCE || $tokens[$prev]['code'] === T_COALESCE_EQUAL) {
                return;
            }

            // Expressions are allowed in arrow functions.
            if ($tokens[$prev]['code'] === T_FN_ARROW) {
                return;
            }
        }//end if

        // Check if this token is actually part of a one-line IF or ELSE statement.
        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                $i = $tokens[$i]['parenthesis_opener'];
                continue;
            } else if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            break;
        }

        if ($tokens[$i]['code'] === T_IF
            || $tokens[$i]['code'] === T_ELSE
            || $tokens[$i]['code'] === T_ELSEIF
        ) {
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_RETURN) {
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($tokens[$next]['code'] === T_SEMICOLON) {
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
                if ($tokens[$next]['code'] === T_CLOSE_CURLY_BRACKET) {
                    // If this is the closing brace of a function
                    // then this return statement doesn't return anything
                    // and is not required anyway.
                    $owner = $tokens[$next]['scope_condition'];
                    if ($tokens[$owner]['code'] === T_FUNCTION
                        || $tokens[$owner]['code'] === T_CLOSURE
                    ) {
                        $warning = 'Empty return statement not required here';
                        $phpcsFile->addWarning($warning, $stackPtr, 'ReturnNotRequired');
                        return;
                    }
                }
            }
        }

        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $owner = $tokens[$stackPtr]['scope_condition'];
            if ($tokens[$owner]['code'] === T_CASE || $tokens[$owner]['code'] === T_DEFAULT) {
                // This token closes the scope of a CASE or DEFAULT statement
                // so any code between this statement and the next CASE, DEFAULT or
                // end of SWITCH token will not be executable.
                $end  = $phpcsFile->findEndOfStatement($stackPtr);
                $next = $phpcsFile->findNext(
                    [
                        T_CASE,
                        T_DEFAULT,
                        T_CLOSE_CURLY_BRACKET,
                        T_ENDSWITCH,
                    ],
                    ($end + 1)
                );

                if ($next !== false) {
                    $lastLine = $tokens[$end]['line'];
                    for ($i = ($stackPtr + 1); $i < $next; $i++) {
                        if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                            continue;
                        }

                        $line = $tokens[$i]['line'];
                        if ($line > $lastLine) {
                            $type    = substr($tokens[$stackPtr]['type'], 2);
                            $warning = 'Code after the %s statement on line %s cannot be executed';
                            $data    = [
                                $type,
                                $tokens[$stackPtr]['line'],
                            ];
                            $phpcsFile->addWarning($warning, $i, 'Unreachable', $data);
                            $lastLine = $line;
                        }
                    }
                }//end if

                // That's all we have to check for these types of statements.
                return;
            }//end if
        }//end if

        // This token may be part of an inline condition.
        // If we find a closing parenthesis that belongs to a condition
        // we should ignore this token.
        if (isset($tokens[$prev]['parenthesis_owner']) === true) {
            $owner  = $tokens[$prev]['parenthesis_owner'];
            $ignore = [
                T_IF     => true,
                T_ELSE   => true,
                T_ELSEIF => true,
            ];
            if (isset($ignore[$tokens[$owner]['code']]) === true) {
                return;
            }
        }

        $ourConditions = array_keys($tokens[$stackPtr]['conditions']);

        if (empty($ourConditions) === false) {
            $condition = array_pop($ourConditions);

            if (isset($tokens[$condition]['scope_closer']) === false) {
                return;
            }

            // Special case for BREAK statements sitting directly inside SWITCH
            // statements. If we get to this point, we know the BREAK is not being
            // used to close a CASE statement, so it is most likely non-executable
            // code itself (as is the case when you put return; break; at the end of
            // a case). So we need to ignore this token.
            if ($tokens[$condition]['code'] === T_SWITCH
                && $tokens[$stackPtr]['code'] === T_BREAK
            ) {
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

            if ($nextOpener === null) {
                $end = $closer;
            } else {
                $end = ($nextOpener - 1);
            }
        } else {
            // This token is in the global scope.
            if ($tokens[$stackPtr]['code'] === T_BREAK) {
                return;
            }

            // Throw an error for all lines until the end of the file.
            $end = ($phpcsFile->numTokens - 1);
        }//end if

        // Find the semicolon or closing PHP tag that ends this statement,
        // skipping nested statements like FOR loops and closures.
        for ($start = ($stackPtr + 1); $start < $phpcsFile->numTokens; $start++) {
            if ($start === $end) {
                break;
            }

            if (isset($tokens[$start]['parenthesis_closer']) === true
                && $tokens[$start]['code'] === T_OPEN_PARENTHESIS
            ) {
                $start = $tokens[$start]['parenthesis_closer'];
                continue;
            }

            if (isset($tokens[$start]['bracket_closer']) === true
                && $tokens[$start]['code'] === T_OPEN_CURLY_BRACKET
            ) {
                $start = $tokens[$start]['bracket_closer'];
                continue;
            }

            if ($tokens[$start]['code'] === T_SEMICOLON || $tokens[$start]['code'] === T_CLOSE_TAG) {
                break;
            }
        }//end for

        if (isset($tokens[$start]) === false) {
            return;
        }

        $lastLine = $tokens[$start]['line'];
        for ($i = ($start + 1); $i < $end; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true
                || isset(Tokens::$bracketTokens[$tokens[$i]['code']]) === true
                || $tokens[$i]['code'] === T_SEMICOLON
            ) {
                continue;
            }

            // Skip whole functions and classes/interfaces because they are not
            // technically executed code, but rather declarations that may be used.
            if (isset(Tokens::$ooScopeTokens[$tokens[$i]['code']]) === true
                || $tokens[$i]['code'] === T_FUNCTION
                || $tokens[$i]['code'] === T_CLOSURE
            ) {
                if (isset($tokens[$i]['scope_closer']) === false) {
                    // Parse error/Live coding.
                    return;
                }

                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            // Skip HTML whitespace.
            if ($tokens[$i]['code'] === T_INLINE_HTML && \trim($tokens[$i]['content']) === '') {
                continue;
            }

            // Skip PHP re-open tag (eg, after inline HTML).
            if ($tokens[$i]['code'] === T_OPEN_TAG) {
                continue;
            }

            $line = $tokens[$i]['line'];
            if ($line > $lastLine) {
                $type    = substr($tokens[$stackPtr]['type'], 2);
                $warning = 'Code after the %s statement on line %s cannot be executed';
                $data    = [
                    $type,
                    $tokens[$stackPtr]['line'],
                ];
                $phpcsFile->addWarning($warning, $i, 'Unreachable', $data);
                $lastLine = $line;
            }
        }//end for

    }//end process()


}//end class
