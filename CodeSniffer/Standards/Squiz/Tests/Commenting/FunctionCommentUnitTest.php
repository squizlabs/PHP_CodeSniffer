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
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for FunctionCommentSniff.
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
class Squiz_Tests_Commenting_FunctionCommentUnitTest extends AbstractSniffUnitTest
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
                10  => 4,
                12  => 3,
                13  => 3,
                14  => 1,
                15  => 1,
                16  => 1,
                28  => 1,
                35  => 3,
                38  => 1,
                41  => 1,
                44  => 1,
                53  => 1,
                54  => 1,
                66  => 1,
                76  => 1,
                87  => 1,
                96  => 1,
                103 => 1,
                109 => 1,
                112 => 2,
                122 => 1,
                123 => 4,
                124 => 3,
                125 => 4,
                126 => 6,
                127 => 1,
                137 => 3,
                138 => 2,
                139 => 3,
                141 => 1,
                144 => 2,
                156 => 2,
                159 => 1,
                160 => 2,
                168 => 1,
                175 => 1,
                184 => 1,
                185 => 3,
                186 => 1,
                196 => 4,
                199 => 1,
                200 => 1,
                201 => 1,
                204 => 2,
                216 => 2,
                217 => 2,
                228 => 1,
                229 => 1,
                230 => 1,
                231 => 1,
                232 => 1,
                233 => 1,
                237 => 1,
                239 => 1,
                254 => 1,
                256 => 4,
                269 => 1,
                271 => 1,
                272 => 1,
                285 => 1,
                286 => 1,
                287 => 1,
                288 => 1,
                289 => 1,
                290 => 1,
                294 => 1,
                296 => 2,
                303 => 1,
                310 => 1,
                318 => 1,
                328 => 1,
                346 => 1,
                356 => 1,
                357 => 1,
                370 => 1,
                371 => 1,
                386 => 1,
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
                203 => 1,
               );

    }//end getWarningList()


}//end class

?>
