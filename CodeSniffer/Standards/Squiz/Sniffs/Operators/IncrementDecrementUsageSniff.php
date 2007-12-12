<?php
/**
 * Squiz_Sniffs_Operators_IncrementDecrementUsageSniff.
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
 * Squiz_Sniffs_Operators_IncrementDecrementUsageSniff.
 *
 * Tests that the ++ operators are used when possible.
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
class Squiz_Sniffs_Operators_IncrementDecrementUsageSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_EQUAL,
                T_PLUS_EQUAL,
                T_MINUS_EQUAL,
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

        $assignedVar = $phpcsFile->findPrevious(array(T_VARIABLE), ($stackPtr - 1), null, false);
        // Not an assignment, return.
        if ($assignedVar === false) {
            return;
        }

        $statementEnd = $phpcsFile->findNext(array(T_SEMICOLON, T_CLOSE_PARENTHESIS, T_CLOSE_SQUARE_BRACKET, T_CLOSE_CURLY_BRACKET), $stackPtr);

        // If there is anything other than variables, numbers, spaces or operators we need to return.
        $noiseTokens = $phpcsFile->findNext(array(T_LNUMBER, T_VARIABLE, T_WHITESPACE, T_PLUS, T_MINUS, T_OPEN_PARENTHESIS), ($stackPtr + 1), $statementEnd, true);

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
        while (($nextNumber = $phpcsFile->findNext(array(T_LNUMBER), ($nextNumber + 1), $statementEnd, false)) !== false) {
            $previousNumber = $nextNumber;
            $numberCount++;
        }

        if ($numberCount !== 1) {
            return;
        }

        $nextNumber = $previousNumber;
        if ($tokens[$nextNumber]['content'] === '1') {
            if ($tokens[$stackPtr]['code'] === T_EQUAL) {
                $operator = $tokens[$phpcsFile->findNext(array(T_PLUS, T_MINUS), ($stackPtr + 1), $statementEnd)]['content'];
            } else {
                $operator = substr($tokens[$stackPtr]['content'], 0, 1);
            }

            // If we are adding or subtracting negative value, the operator
            // needs to be reversed.
            if ($tokens[$stackPtr]['code'] !== T_EQUAL) {
                $negative = $phpcsFile->findPrevious(T_MINUS, ($nextNumber - 1), $stackPtr);
                if ($negative !== false) {
                    $operator = ($operator === '+') ? '-' : '+';
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
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
