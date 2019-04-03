<?php
/**
 * Tests that all arithmetic operations are bracketed.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Formatting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class OperatorBracketSniff implements Sniff
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
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return Tokens::$operators;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($phpcsFile->tokenizerType === 'JS' && $tokens[$stackPtr]['code'] === T_PLUS) {
            // JavaScript uses the plus operator for string concatenation as well
            // so we cannot accurately determine if it is a string concat or addition.
            // So just ignore it.
            return;
        }

        // If the & is a reference, then we don't want to check for brackets.
        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND && $phpcsFile->isReference($stackPtr) === true) {
            return;
        }

        // There is one instance where brackets aren't needed, which involves
        // the minus sign being used to assign a negative number to a variable.
        if ($tokens[$stackPtr]['code'] === T_MINUS) {
            // Check to see if we are trying to return -n.
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prev]['code'] === T_RETURN) {
                return;
            }

            $number = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$number]['code'] === T_LNUMBER || $tokens[$number]['code'] === T_DNUMBER) {
                $previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if ($previous !== false) {
                    $isAssignment = isset(Tokens::$assignmentTokens[$tokens[$previous]['code']]);
                    $isEquality   = isset(Tokens::$equalityTokens[$tokens[$previous]['code']]);
                    $isComparison = isset(Tokens::$comparisonTokens[$tokens[$previous]['code']]);
                    if ($isAssignment === true || $isEquality === true || $isComparison === true) {
                        // This is a negative assignment or comparison.
                        // We need to check that the minus and the number are
                        // adjacent.
                        if (($number - $stackPtr) !== 1) {
                            $error = 'No space allowed between minus sign and number';
                            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterMinus');
                        }

                        return;
                    }
                }
            }
        }//end if

        $previousToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true, null, true);
        if ($previousToken !== false) {
            // A list of tokens that indicate that the token is not
            // part of an arithmetic operation.
            $invalidTokens = [
                T_COMMA               => true,
                T_COLON               => true,
                T_OPEN_PARENTHESIS    => true,
                T_OPEN_SQUARE_BRACKET => true,
                T_OPEN_CURLY_BRACKET  => true,
                T_OPEN_SHORT_ARRAY    => true,
                T_CASE                => true,
            ];

            if (isset($invalidTokens[$tokens[$previousToken]['code']]) === true) {
                return;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_BITWISE_OR
            && isset($tokens[$stackPtr]['nested_parenthesis']) === true
        ) {
            $brackets    = $tokens[$stackPtr]['nested_parenthesis'];
            $lastBracket = array_pop($brackets);
            if (isset($tokens[$lastBracket]['parenthesis_owner']) === true
                && $tokens[$tokens[$lastBracket]['parenthesis_owner']]['code'] === T_CATCH
            ) {
                // This is a pipe character inside a catch statement, so it is acting
                // as an exception type separator and not an arithmetic operation.
                return;
            }
        }

        // Tokens that are allowed inside a bracketed operation.
        $allowed = [
            T_VARIABLE,
            T_LNUMBER,
            T_DNUMBER,
            T_STRING,
            T_WHITESPACE,
            T_NS_SEPARATOR,
            T_THIS,
            T_SELF,
            T_STATIC,
            T_OBJECT_OPERATOR,
            T_DOUBLE_COLON,
            T_OPEN_SQUARE_BRACKET,
            T_CLOSE_SQUARE_BRACKET,
            T_MODULUS,
            T_NONE,
            T_BITWISE_NOT,
        ];

        $allowed += Tokens::$operators;

        $lastBracket = false;
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            $parenthesis = array_reverse($tokens[$stackPtr]['nested_parenthesis'], true);
            foreach ($parenthesis as $bracket => $endBracket) {
                $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($bracket - 1), null, true);
                $prevCode  = $tokens[$prevToken]['code'];

                if ($prevCode === T_ISSET) {
                    // This operation is inside an isset() call, but has
                    // no bracket of it's own.
                    break;
                }

                if ($prevCode === T_STRING || $prevCode === T_SWITCH) {
                    // We allow simple operations to not be bracketed.
                    // For example, ceil($one / $two).
                    for ($prev = ($stackPtr - 1); $prev > $bracket; $prev--) {
                        if (in_array($tokens[$prev]['code'], $allowed, true) === true) {
                            continue;
                        }

                        if ($tokens[$prev]['code'] === T_CLOSE_PARENTHESIS) {
                            $prev = $tokens[$prev]['parenthesis_opener'];
                        } else {
                            break;
                        }
                    }

                    if ($prev !== $bracket) {
                        break;
                    }

                    for ($next = ($stackPtr + 1); $next < $endBracket; $next++) {
                        if (in_array($tokens[$next]['code'], $allowed, true) === true) {
                            continue;
                        }

                        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
                            $next = $tokens[$next]['parenthesis_closer'];
                        } else {
                            break;
                        }
                    }

                    if ($next !== $endBracket) {
                        break;
                    }
                }//end if

                if (in_array($prevCode, Tokens::$scopeOpeners, true) === true) {
                    // This operation is inside a control structure like FOREACH
                    // or IF, but has no bracket of it's own.
                    // The only control structure allowed to do this is SWITCH.
                    if ($prevCode !== T_SWITCH) {
                        break;
                    }
                }

                if ($prevCode === T_OPEN_PARENTHESIS) {
                    // These are two open parenthesis in a row. If the current
                    // one doesn't enclose the operator, go to the previous one.
                    if ($endBracket < $stackPtr) {
                        continue;
                    }
                }

                $lastBracket = $bracket;
                break;
            }//end foreach
        }//end if

        if ($lastBracket === false) {
            // It is not in a bracketed statement at all.
            $this->addMissingBracketsError($phpcsFile, $stackPtr);
            return;
        } else if ($tokens[$lastBracket]['parenthesis_closer'] < $stackPtr) {
            // There are a set of brackets in front of it that don't include it.
            $this->addMissingBracketsError($phpcsFile, $stackPtr);
            return;
        } else {
            // We are enclosed in a set of bracket, so the last thing to
            // check is that we are not also enclosed in square brackets
            // like this: ($array[$index + 1]), which is invalid.
            $brackets = [
                T_OPEN_SQUARE_BRACKET,
                T_CLOSE_SQUARE_BRACKET,
            ];

            $squareBracket = $phpcsFile->findPrevious($brackets, ($stackPtr - 1), $lastBracket);
            if ($squareBracket !== false && $tokens[$squareBracket]['code'] === T_OPEN_SQUARE_BRACKET) {
                $closeSquareBracket = $phpcsFile->findNext($brackets, ($stackPtr + 1));
                if ($closeSquareBracket !== false && $tokens[$closeSquareBracket]['code'] === T_CLOSE_SQUARE_BRACKET) {
                    $this->addMissingBracketsError($phpcsFile, $stackPtr);
                }
            }

            return;
        }//end if

        $lastAssignment = $phpcsFile->findPrevious(Tokens::$assignmentTokens, $stackPtr, null, false, null, true);
        if ($lastAssignment !== false && $lastAssignment > $lastBracket) {
            $this->addMissingBracketsError($phpcsFile, $stackPtr);
        }

    }//end process()


    /**
     * Add and fix the missing brackets error.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function addMissingBracketsError($phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $allowed = [
            T_VARIABLE                 => true,
            T_LNUMBER                  => true,
            T_DNUMBER                  => true,
            T_STRING                   => true,
            T_CONSTANT_ENCAPSED_STRING => true,
            T_DOUBLE_QUOTED_STRING     => true,
            T_WHITESPACE               => true,
            T_NS_SEPARATOR             => true,
            T_THIS                     => true,
            T_SELF                     => true,
            T_STATIC                   => true,
            T_OBJECT_OPERATOR          => true,
            T_DOUBLE_COLON             => true,
            T_MODULUS                  => true,
            T_ISSET                    => true,
            T_ARRAY                    => true,
            T_NONE                     => true,
            T_BITWISE_NOT              => true,
        ];

        // Find the first token in the expression.
        for ($before = ($stackPtr - 1); $before > 0; $before--) {
            // Special case for plus operators because we can't tell if they are used
            // for addition or string contact. So assume string concat to be safe.
            if ($phpcsFile->tokenizerType === 'JS' && $tokens[$before]['code'] === T_PLUS) {
                break;
            }

            if (isset(Tokens::$emptyTokens[$tokens[$before]['code']]) === true
                || isset(Tokens::$operators[$tokens[$before]['code']]) === true
                || isset(Tokens::$castTokens[$tokens[$before]['code']]) === true
                || isset($allowed[$tokens[$before]['code']]) === true
            ) {
                continue;
            }

            if ($tokens[$before]['code'] === T_CLOSE_PARENTHESIS) {
                $before = $tokens[$before]['parenthesis_opener'];
                continue;
            }

            if ($tokens[$before]['code'] === T_CLOSE_SQUARE_BRACKET) {
                $before = $tokens[$before]['bracket_opener'];
                continue;
            }

            if ($tokens[$before]['code'] === T_CLOSE_SHORT_ARRAY) {
                $before = $tokens[$before]['bracket_opener'];
                continue;
            }

            break;
        }//end for

        $before = $phpcsFile->findNext(Tokens::$emptyTokens, ($before + 1), null, true);

        // Find the last token in the expression.
        for ($after = ($stackPtr + 1); $after < $phpcsFile->numTokens; $after++) {
            // Special case for plus operators because we can't tell if they are used
            // for addition or string concat. So assume string concat to be safe.
            if ($phpcsFile->tokenizerType === 'JS' && $tokens[$after]['code'] === T_PLUS) {
                break;
            }

            if (isset(Tokens::$emptyTokens[$tokens[$after]['code']]) === true
                || isset(Tokens::$operators[$tokens[$after]['code']]) === true
                || isset(Tokens::$castTokens[$tokens[$after]['code']]) === true
                || isset($allowed[$tokens[$after]['code']]) === true
            ) {
                continue;
            }

            if ($tokens[$after]['code'] === T_OPEN_PARENTHESIS) {
                $after = $tokens[$after]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$after]['code'] === T_OPEN_SQUARE_BRACKET) {
                $after = $tokens[$after]['bracket_closer'];
                continue;
            }

            if ($tokens[$after]['code'] === T_OPEN_SHORT_ARRAY) {
                $after = $tokens[$after]['bracket_closer'];
                continue;
            }

            break;
        }//end for

        $after = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($after - 1), null, true);

        $error = 'Operation must be bracketed';
        if ($before === $after || $before === $stackPtr || $after === $stackPtr) {
            $phpcsFile->addError($error, $stackPtr, 'MissingBrackets');
            return;
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'MissingBrackets');
        if ($fix === true) {
            // Can only fix this error if both tokens are available for fixing.
            // Adding one bracket without the other will create parse errors.
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($before, '('.$tokens[$before]['content']);
            $phpcsFile->fixer->replaceToken($after, $tokens[$after]['content'].')');
            $phpcsFile->fixer->endChangeset();
        }

    }//end addMissingBracketsError()


}//end class
