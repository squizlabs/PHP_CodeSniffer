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
        return array(T_BITWISE_AND);

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

        // If its not a reference, then we expect one space either side of the
        // bitwise operator.
        if ($phpcsFile->isReference($stackPtr) === false) {
            // Check there is one space before the & operator.
            if ($tokens[$stackPtr - 1]['code'] !== T_WHITESPACE) {
                $error = 'Space required before "&" operator.';
                $phpcsFile->addError($error, $stackPtr);
            } else {
                if (strlen($tokens[$stackPtr - 1]['content']) !== 1) {
                    $error  = strlen($tokens[$stackPtr - 1]['content']);
                    $error .= ' spaces found before "&" operator.';
                    $error .= ' Expected 1.';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

            // Check there is one space after the & operator.
            if ($tokens[$stackPtr + 1]['code'] !== T_WHITESPACE) {
                $error = 'Space required after "&" operator.';
                $phpcsFile->addError($error, $stackPtr);
            } else {
                if (strlen($tokens[$stackPtr + 1]['content']) !== 1) {
                    $error  = strlen($tokens[$stackPtr + 1]['content']);
                    $error .= ' spaces found after "&" operator.';
                    $error .= ' Expected 1.';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }
        } else {
            $tokenBefore = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
            $tokenAfter  = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

            if (($tokenAfter - $stackPtr) !== 1) {
                $error  = 'Expected "';
                $error .= $tokens[$stackPtr]['content'];
                $error .= $tokens[$tokenAfter]['content'];
                $error .= '".';
                $phpcsFile->addError($error, $stackPtr);
            }
        }//end if

    }//end processSupplementary()


}//end class

?>
