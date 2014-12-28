<?php
/**
 * Unit test class for FunctionCommentSniff.
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

/**
 * Unit test class for FunctionCommentSniff.
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
class PEAR_Tests_Commenting_FunctionCommentUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        return array(
                5   => 1,
                8   => 1,
                10  => 1,
                12  => 1,
                13  => 1,
                14  => 1,
                15  => 1,
                28  => 1,
                37  => 1,
                40  => 1,
                43  => 1,
                53  => 1,
                76  => 1,
                87  => 1,
                103 => 1,
                109 => 1,
                110 => 1,
                112 => 2,
                122 => 1,
                123 => 2,
                124 => 2,
                125 => 1,
                126 => 1,
                137 => 1,
                138 => 1,
                139 => 1,
                152 => 1,
                155 => 1,
                165 => 1,
                172 => 1,
                183 => 2,
                190 => 2,
                204 => 1,
                234 => 1,
                272 => 2,
                279 => 1,
                309 => 1,
                323 => 1,
                332 => 2,
                335 => 1,
                336 => 1,
               );

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return array();

    }//end getWarningList()


}//end class

?>
