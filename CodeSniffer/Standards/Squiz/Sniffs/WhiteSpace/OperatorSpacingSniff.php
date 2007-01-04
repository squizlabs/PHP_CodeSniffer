<?php
/**
 * Sniffs_Squiz_WhiteSpace_OperatorSpacingSniff.
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

require_once 'PHP/CodeSniffer/Standards/AbstractPatternSniff.php';

/**
 * Sniffs_Squiz_WhiteSpace_OperatorSpacingSniff.
 *
 * Verifies that operators have valid spacing surrounding them.
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
class Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{


    /**
     * Returns the patterns to check for this test.
     *
     * @return array
     */
    protected function getPatterns()
    {
        return array(
                ' * ',
                ' + ',
                ' / ',
                ' *= ',
                ' /= ',
                ' -= ',
                ' += ',
                ' .= ',
                ' % ',
                ' | ',
               );

    }//end getPatterns()


    /**
     * Registers the supplementary tokens this sniff wishes to listen for.
     *
     * @return array(int)
     */
    protected function registerSupplementary()
    {
        return array(T_BITWISE_AND, T_MINUS);

    }//end registerSupplementary()


    /**
     * Processes the supplementary tokens this sniff is listening for.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token exists.
     * @param int                  $stackPtr  The position in the stack where the token was found.
     *
     * @return void
     */
    protected function processSupplementary(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND) {
            // If its not a reference, then we expect one space either side of the
            // bitwise operator.
            if ($phpcsFile->isReference($stackPtr) === false) {
                // Check there is one space before the & operator.
                if ($tokens[$stackPtr - 1]['code'] !== T_WHITESPACE) {
                    $error = 'Expected 1 space before "&" operator; 0 found';
                    $phpcsFile->addError($error, $stackPtr);
                } else {
                    if (strlen($tokens[$stackPtr - 1]['content']) !== 1) {
                        $found = strlen($tokens[$stackPtr - 1]['content']);
                        $error = "Expected 1 space before \"&\" operator; $found found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }

                // Check there is one space after the & operator.
                if ($tokens[$stackPtr + 1]['code'] !== T_WHITESPACE) {
                    $error = 'Expected 1 space after "&" operator; 0 found';
                    $phpcsFile->addError($error, $stackPtr);
                } else {
                    if (strlen($tokens[$stackPtr + 1]['content']) !== 1) {
                        $found = strlen($tokens[$stackPtr + 1]['content']);
                        $error = "Expected 1 space after \"&\" operator; $found found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }
            }
        } else {
            // Check minus spacing, but make sure we aren't just assigning
            // a minus value or returning one.
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($tokens[$prev]['code'] === T_RETURN) {
                return;
            }

            $number = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$number]['code'] === T_LNUMBER) {
                $semi = $phpcsFile->findNext(T_WHITESPACE, ($number + 1), null, true);
                if ($tokens[$semi]['code'] === T_SEMICOLON) {
                    if ($prev !== false && (in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true)) {
                        // This is a negative assignment.
                        return;
                    }
                }
            }

            $nextSpaceLength = strlen($tokens[($stackPtr + 1)]['content']);
            $prevSpaceLength = strlen($tokens[($stackPtr - 1)]['content']);
            if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE || $tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE || $nextSpaceLength !== 1 || $prevSpaceLength !== 1) {
                $found = $phpcsFile->getTokensAsString($prev, 3);
                $error = $this->prepareError($found, ' - ');
                $phpcsFile->addError($error, $stackPtr);
            }
        }

    }//end processSupplementary()


}//end class

?>
