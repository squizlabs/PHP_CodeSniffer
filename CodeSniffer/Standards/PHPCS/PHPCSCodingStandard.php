<?php
/**
 * PHP_CodeSniffer Coding Standard.
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

require_once 'PHP/CodeSniffer/Standards/CodingStandard.php';

 /**
 * PHP_CodeSniffer Coding Standard.
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
class PHP_CodeSniffer_Standards_PHPCS_PHPCSCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{


    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The PHP_CodeSniffer standard combines the PEAR and Squiz standards
     * but removes some sniffs from the Squiz standard that clash with
     * those in the PEAR standard.
     *
     * @return array
     */
    function getIncludedSniffs()
    {
        return array(
                'PEAR',
                'Squiz',
               );

    }//end getIncludedSniffs()


    /**
     * Return a list of external sniffs to exclude from this standard.
     *
     * The PHP_CodeSniffer standard combines the PEAR and Squiz standards
     * but removes some sniffs from the Squiz standard that clash with
     * those in the PEAR standard.
     *
     * @return array
     */
    function getExcludedSniffs()
    {
        return array(
                'Generic/Sniffs/PHP/UpperCaseConstantSniff',
                'Squiz/Sniffs/Classes/ClassFileNameSniff',
                'Squiz/Sniffs/Classes/ValidClassNameSniff',
                'Squiz/Sniffs/Commenting/ClassCommentSniff',
                'Squiz/Sniffs/Commenting/FileCommentSniff',
                'Squiz/Sniffs/Commenting/FunctionCommentSniff',
                'Squiz/Sniffs/Commenting/VariableCommentSniff',
                'Squiz/Sniffs/ControlStructures/SwitchDeclarationSniff',
                'Squiz/Sniffs/Files/FileExtensionSniff',
                'Squiz/Sniffs/Files/LineLengthSniff',
                'Squiz/Sniffs/WhiteSpace/ScopeIndentSniff',
               );

    }//end getExcludedSniffs()


}//end class
?>
