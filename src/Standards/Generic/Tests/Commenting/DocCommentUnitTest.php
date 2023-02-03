<?php
/**
 * Unit test class for the DocCommentSniff sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DocCommentUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of CLI values to set before the file is tested.
     *
     * @param string                  $testFile The name of the file being tested.
     * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
     *
     * @return void
     */
    public function setCliValues($testFile, $config)
    {
        $config->tabWidth = 4;

    }//end setCliValues()


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
        return [
            14  => 1,
            16  => 1,
            18  => 1,
            23  => 1,
            26  => 1,
            30  => 1,
            32  => 1,
            38  => 2,
            40  => 1,
            41  => 1,
            51  => 1,
            54  => 1,
            58  => 1,
            60  => 2,
            67  => 1,
            69  => 2,
            80  => 1,
            81  => 2,
            88  => 1,
            91  => 1,
            95  => 1,
            156 => 1,
            158 => 1,
            166 => 1,
            171 => 1,
            172 => 1,
            173 => 1,
            174 => 1,
            177 => 1,
            182 => 1,
            191 => 3,
            192 => 3,
            200 => 1,
            204 => 1,
            205 => 2,
            206 => 1,
            207 => 1,
            208 => 2,
            214 => 1,
            217 => 1,
            220 => 1,
            224 => 1,
            227 => 1,
            232 => 1,
            235 => 4,
            239 => 1,
            241 => 2,
            243 => 1,
            245 => 3,
            264 => 1,
            265 => 1,
            267 => 1,
            269 => 1,
            270 => 1,
            273 => 1,
        ];

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
        return [];

    }//end getWarningList()


}//end class
