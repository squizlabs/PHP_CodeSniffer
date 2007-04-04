<?php
/**
 * Unit test class for VariableCommentSniff.
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
 * Unit test class for VariableCommentSniff.
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
class Squiz_Tests_Commenting_VariableCommentUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList()
    {
        return array(
                23  => 1,
                26  => 1,
                30  => 2,
                35  => 1,
                42  => 1,
                48  => 1,
                49  => 1,
                59  => 1,
                62  => 1,
                63  => 1,
                70  => 1,
                72  => 1,
                77  => 2,
                81  => 1,
                82  => 1,
                90  => 1,
                91  => 1,
                92  => 1,
                93  => 1,
                94  => 1,
                100 => 1,
                102 => 1,
                103 => 1,
                108 => 1,
                148 => 1,
                157 => 1,
                166 => 1,
                175 => 1,
                181 => 1,
                187 => 1,
               );

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return array(
                112 => 1,
               );

    }//end getWarningList()


}//end class

?>