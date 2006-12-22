<?php
/**
 * Squiz_Sniffs_Formatting_OperationBracketSniff.
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

require_once 'PHP/CodeSniffer/Sniff.php';
require_once 'PHP/CodeSniffer/Tokens.php';

/**
 * Squiz_Sniffs_Formatting_OperationBracketSniff.
 *
 * Tests that all arithmetic operations are bracketed.
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
class Squiz_Sniffs_Formatting_OperatorBracketSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$operators;

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

        // If the & is a reference, then we don't want to check for brackets.
        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND && $phpcsFile->isReference($stackPtr) === true) {
            return;
        }

        $lastBracket    = $phpcsFile->findPrevious(array(T_OPEN_PARENTHESIS, T_OPEN_SQUARE_BRACKET), $stackPtr, null, false);
        $lastAssignment = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$assignmentTokens, $stackPtr, null, false);

        // There is one instance where brackets aren't needed, which involves
        // the minus sign being used to assign a negative number to a variable.
        if ($tokens[$stackPtr]['code'] === T_MINUS) {
            // Check to see if we are trying to return -n.
            $prev = $phpcsFile->findPrevious(array(T_WHITESPACE, T_COMMENT), $stackPtr - 1, null, true);

            if ($tokens[$prev]['code'] === T_RETURN) {
                return;
            }

            $number = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
            if ($tokens[$number]['code'] === T_LNUMBER) {
                $semi = $phpcsFile->findNext(T_WHITESPACE, $number + 1, null, true);
                if ($tokens[$semi]['code'] === T_SEMICOLON) {
                    $previous = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
                    if ($previous !== false && (in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true)) {
                        // This is a negative assignment.
                        // We need to check that the minus and the number are
                        // adjacent.
                        if (($number - $stackPtr) !== 1) {
                            $error = 'No space allowed between minus sign and number';
                            $phpcsFile->addError($error, $stackPtr);
                        }

                        return;
                    }
                }
            }
        }//end if

        if (($lastBracket === false) || ($tokens[$lastBracket]['line'] !== $tokens[$stackPtr]['line'])) {
            $error = 'Arithmetic operation must be bracketed';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        if ($lastAssignment !== false && $lastAssignment > $lastBracket) {
            $error = 'Arithmetic operation must be bracketed';
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
