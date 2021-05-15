<?php
/**
 * Unit test class for the OpeningFunctionBraceBsdAllman sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class OpeningFunctionBraceBsdAllmanUnitTest extends AbstractSniffUnitTest
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
        return [
            4   => 1,
            13  => 1,
            19  => 1,
            24  => 1,
            30  => 1,
            40  => 1,
            44  => 1,
            50  => 1,
            55  => 1,
            67  => 1,
            78  => 1,
            85  => 1,
            91  => 1,
            98  => 1,
            110 => 1,
            115 => 1,
            122 => 1,
            128 => 1,
            155 => 1,
            158 => 1,
            164 => 1,
            168 => 1,
            172 => 1,
            176 => 1,
            196 => 1,
            201 => 1,
            205 => 2,
            210 => 2,
            215 => 1,
            220 => 1,
            231 => 1,
            236 => 1,
            244 => 1,
            252 => 1,
            260 => 1,
        ];

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
        return [];

    }//end getWarningList()


}//end class
