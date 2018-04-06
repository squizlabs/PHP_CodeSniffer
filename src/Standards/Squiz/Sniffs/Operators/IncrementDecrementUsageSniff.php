<?php
/**
 * Ensures that the ++ operators are used when possible.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Operators;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class IncrementDecrementUsageSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_EQUAL,
            T_PLUS_EQUAL,
            T_MINUS_EQUAL,
            T_INC,
            T_DEC,
        ];

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
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_INC || $tokens[$stackPtr]['code'] === T_DEC) {
            $this->processIncDec($phpcsFile, $stackPtr);
        } else {
            $this->processAssignment($phpcsFile, $stackPtr);
        }

    }//end process()


    /**
     * Checks to ensure increment and decrement operators are not confusing.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processIncDec($phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Work out where the variable is so we know where to
        // start looking for other operators.
        if ($tokens[($stackPtr - 1)]['code'] === T_VARIABLE
            || ($tokens[($stackPtr - 1)]['code'] === T_STRING
            && $tokens[($stackPtr - 2)]['code'] === T_OBJECT_OPERATOR)
        ) {
            $start = ($stackPtr + 1);
        } else {
            $start = ($stackPtr + 2);
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, $start, null, true);
        if ($next === false) {
            return;
        }

        if (isset(Tokens::$arithmeticTokens[$tokens[$next]['code']]) === true) {
            $error = 'Increment and decrement operators cannot be used in an arithmetic operation';
            $phpcsFile->addError($error, $stackPtr, 'NotAllowed');
            return;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($start - 3), null, true);
        if ($prev === false) {
            return;
        }

        // Check if this is in a string concat.
        if ($tokens[$next]['code'] === T_STRING_CONCAT || $tokens[$prev]['code'] === T_STRING_CONCAT) {
            $error = 'Increment and decrement operators must be bracketed when used in string concatenation';
            $phpcsFile->addError($error, $stackPtr, 'NoBrackets');
        }

    }//end processIncDec()


    /**
     * Checks to ensure increment and decrement operators are used.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processAssignment($phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $assignedVar = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        // Not an assignment, return.
        if ($tokens[$assignedVar]['code'] !== T_VARIABLE) {
            return;
        }

        $statementEnd = $phpcsFile->findNext([T_SEMICOLON, T_CLOSE_PARENTHESIS, T_CLOSE_SQUARE_BRACKET, T_CLOSE_CURLY_BRACKET], $stackPtr);

        // If there is anything other than variables, numbers, spaces or operators we need to return.
        $noiseTokens = $phpcsFile->findNext([T_LNUMBER, T_VARIABLE, T_WHITESPACE, T_PLUS, T_MINUS, T_OPEN_PARENTHESIS], ($stackPtr + 1), $statementEnd, true);

        if ($noiseTokens !== false) {
            return;
        }

        // If we are already using += or -=, we need to ignore
        // the statement if a variable is being used.
        if ($tokens[$stackPtr]['code'] !== T_EQUAL) {
            $nextVar = $phpcsFile->findNext(T_VARIABLE, ($stackPtr + 1), $statementEnd);
            if ($nextVar !== false) {
                return;
            }
        }

        if ($tokens[$stackPtr]['code'] === T_EQUAL) {
            $nextVar          = ($stackPtr + 1);
            $previousVariable = ($stackPtr + 1);
            $variableCount    = 0;
            while (($nextVar = $phpcsFile->findNext(T_VARIABLE, ($nextVar + 1), $statementEnd)) !== false) {
                $previousVariable = $nextVar;
                $variableCount++;
            }

            if ($variableCount !== 1) {
                return;
            }

            $nextVar = $previousVariable;
            if ($tokens[$nextVar]['content'] !== $tokens[$assignedVar]['content']) {
                return;
            }
        }

        // We have only one variable, and it's the same as what is being assigned,
        // so we need to check what is being added or subtracted.
        $nextNumber     = ($stackPtr + 1);
        $previousNumber = ($stackPtr + 1);
        $numberCount    = 0;
        while (($nextNumber = $phpcsFile->findNext([T_LNUMBER], ($nextNumber + 1), $statementEnd, false)) !== false) {
            $previousNumber = $nextNumber;
            $numberCount++;
        }

        if ($numberCount !== 1) {
            return;
        }

        $nextNumber = $previousNumber;
        if ($tokens[$nextNumber]['content'] === '1') {
            if ($tokens[$stackPtr]['code'] === T_EQUAL) {
                $opToken = $phpcsFile->findNext([T_PLUS, T_MINUS], ($nextVar + 1), $statementEnd);
                if ($opToken === false) {
                    // Operator was before the variable, like:
                    // $var = 1 + $var;
                    // So we ignore it.
                    return;
                }

                $operator = $tokens[$opToken]['content'];
            } else {
                $operator = substr($tokens[$stackPtr]['content'], 0, 1);
            }

            // If we are adding or subtracting negative value, the operator
            // needs to be reversed.
            if ($tokens[$stackPtr]['code'] !== T_EQUAL) {
                $negative = $phpcsFile->findPrevious(T_MINUS, ($nextNumber - 1), $stackPtr);
                if ($negative !== false) {
                    if ($operator === '+') {
                        $operator = '-';
                    } else {
                        $operator = '+';
                    }
                }
            }

            $expected = $tokens[$assignedVar]['content'].$operator.$operator;
            $found    = $phpcsFile->getTokensAsString($assignedVar, ($statementEnd - $assignedVar + 1));

            if ($operator === '+') {
                $error = 'Increment';
            } else {
                $error = 'Decrement';
            }

            $error .= " operators should be used where possible; found \"$found\" but expected \"$expected\"";
            $phpcsFile->addError($error, $stackPtr, 'Found');
        }//end if

    }//end processAssignment()


}//end class
