<?php
/**
 * Unit test class for the OpeningFunctionBraceKernighanRitchie sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class OpeningFunctionBraceKernighanRitchieUnitTest extends AbstractSniffUnitTest
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
            9   => 1,
            13  => 1,
            17  => 1,
            29  => 1,
            33  => 1,
            37  => 1,
            53  => 1,
            58  => 1,
            63  => 1,
            77  => 1,
            82  => 1,
            87  => 1,
            104 => 1,
            119 => 1,
            123 => 1,
            127 => 1,
            132 => 1,
            137 => 1,
            142 => 1,
            157 => 1,
            162 => 1,
            171 => 1,
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
