<?php
/**
 * Unit test class for the UpperCaseConstant sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class UpperCaseConstantUnitTest extends AbstractSniffUnitTest
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
            7  => 1,
            10 => 1,
            15 => 1,
            16 => 1,
            23 => 1,
            26 => 1,
            31 => 1,
            32 => 1,
            39 => 1,
            42 => 1,
            47 => 1,
            48 => 1,
            70 => 1,
            71 => 1,
            85 => 1,
            87 => 1,
            88 => 1,
            90 => 2,
            92 => 2,
            93 => 1,
            98 => 2,
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
