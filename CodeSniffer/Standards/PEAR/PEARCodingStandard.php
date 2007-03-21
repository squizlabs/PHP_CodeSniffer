<?php
/**
 * PEAR Coding Standard.
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
 * PEAR Coding Standard.
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
class PHP_CodeSniffer_Standards_PEAR_PEARCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{


    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The PEAR standard uses some generic sniffs.
     *
     * @return array
     */
    function getIncludedSniffs()
    {
        return array(
                'Generic/Sniffs/Formatting/MultipleStatementAlignmentSniff',
                'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff',
                'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff',
                'Generic/Sniffs/PHP/LowerCaseConstantSniff',
                'Generic/Sniffs/PHP/DisallowShortOpenTagSniff',
               );

    }//end getIncludedSniffs()


}//end class
?>
