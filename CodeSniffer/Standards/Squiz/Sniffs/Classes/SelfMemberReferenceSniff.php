<?php
/**
 * Squiz_Sniffs_Classes_ClassFileNameSniff.
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
 * Tests self member references.
 *
 * Verifies that :
 * <ul>
 *  <li>self:: is used instead of Self::</li>
 *  <li>self:: is used for local static member reference</li>
 *  <li>self:: is used instead of self ::</li>
 * </ul>
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
class Squiz_Sniffs_Classes_SelfMemberReferenceSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Classes_SelfMemberReferenceSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_DOUBLE_COLON));

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

        $className = ($stackPtr - 1);
        if ($tokens[$className]['code'] === T_SELF) {
            if (strtolower($tokens[$className]['content']) !== $tokens[$className]['content']) {
                $error = 'Must use "self::" for local static member reference. (Found "'.$tokens[$className]['content'].'").';
                $phpcsFile->addError($error, $className);
                return;
            }
        } else if ($tokens[$className]['code'] === T_STRING) {
            // Make sure this another class ref.
            $declarationName = $phpcsFile->getDeclarationName($currScope);
            if ($declarationName === $tokens[$className]['content']) {
                $error = 'Must use "self::" for local static member reference.';
                $phpcsFile->addError($error, $className);
                return;
            }
        } else if ($tokens[$className]['code'] !== T_PARENT) {
            $error = 'Cannot have "'.$tokens[$className]['content'].'" between "::" and class name.';
            $phpcsFile->addError($error, $className);
            return;
        }

    }//end processTokenWithinScope()


}//end class

?>
