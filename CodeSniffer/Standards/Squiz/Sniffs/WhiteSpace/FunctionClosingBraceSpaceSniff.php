<?php
/**
 * Squiz_Sniffs_WhiteSpace_FunctionClosingBraceSpaceSniff.
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

require_once 'PHP/CodeSniffer/Standards/AbstractScopeSniff.php';

/**
 * Squiz_Sniffs_WhiteSpace_FunctionClosingBraceSpaceSniff.
 *
 * Checks that an empty line is present before the closing brace of any
 * function.
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
class Squiz_Sniffs_WhiteSpace_FunctionClosingBraceSpaceSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs the test with the tokens it wishes to listen for.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(array(T_FUNCTION), array(T_CLOSE_CURLY_BRACKET));

    }//end __construct()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     * @param int                  $currScope A pointer to the start of the scope.
     *
     * @return void
     */
    public function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();
        // If this is not an end of function curly brace, return.
        if ((isset($tokens[$currScope]['scope_closer']) === false) || $tokens[$currScope]['scope_closer'] !== $stackPtr) {
            return;
        }

        // Find the last non-whitespace character in the method.
        $lastContent = $phpcsFile->findPrevious(array(T_WHITESPACE), ($stackPtr - 1), null, true);

        if ($lastContent === false) {
            return;
        }

        if (strpos($tokens[$lastContent]['content'], "\n") !== false) {
            // Comments add an extra line, so this extra exception needs to be made.
            $lineDifference = ($tokens[$stackPtr]['line'] - $tokens[$lastContent]['line']);
        } else {
            $lineDifference = ($tokens[$stackPtr]['line'] - $tokens[($lastContent + 1)]['line']);
        }

        $error = '';
        if ($lineDifference === 2) {
            return;
        } else if ($lineDifference < 2) {
            $error = 'Expected 1 blank line before closing brace; 0 found';
        } else {
            $lineDifference--;
            $error = "Expected 1 blank line before closing brace; $lineDifference found";
        }

        $phpcsFile->addError($error, $stackPtr);

    }//end processTokenWithinScope()


}//end class

?>
