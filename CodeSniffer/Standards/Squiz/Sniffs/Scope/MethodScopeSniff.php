<?php
/**
 * Verifies that class methods have scope modifiers.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Verifies that class methods have scope modifiers.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Scope_MethodScopeSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Scope_MethodScopeSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE, T_TRAIT), array(T_FUNCTION));

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        if ($phpcsFile->hasCondition($stackPtr, T_FUNCTION) === true) {
            // Ignore nested functions.
            return;
        }

        $modifier = null;
        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if ($tokens[$i]['line'] < $tokens[$stackPtr]['line']) {
                break;
            } else if (isset(PHP_CodeSniffer_Tokens::$scopeModifiers[$tokens[$i]['code']]) === true) {
                $modifier = $i;
                break;
            }
        }

        if ($modifier) {
            return;
        }

        // Look for parent function
        $parent_scope = $phpcsFile->findPrevious(array(T_FUNCTION), $stackPtr-1);

        // Check if function is defined within another functions scope, in which case no visibility required
        if ($parent_scope && $stackPtr > $tokens[$parent_scope]['scope_opener'] && $stackPtr < $tokens[$parent_scope]['scope_closer']) {
            return;
        }

        $error = 'Visibility must be declared on method "%s"';
        $data  = array($methodName);
        $phpcsFile->addError($error, $stackPtr, 'Missing', $data);

    }//end processTokenWithinScope()


}//end class
