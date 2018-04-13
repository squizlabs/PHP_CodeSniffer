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
            170 => 4,
            171 => 4,
            179 => 1,
            183 => 1,
            184 => 2,
            185 => 1,
            186 => 1,
            187 => 2,
            193 => 1,
            196 => 1,
            199 => 1,
            203 => 1,
            207 => 1,
            210 => 4,
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
