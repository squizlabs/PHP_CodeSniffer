<?php
/**
 * Unit test class for the NoSpaceAfterCast sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Formatting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class NoSpaceAfterCastUnitTest extends AbstractSniffUnitTest
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
            3  => 1,
            5  => 1,
            7  => 1,
            9  => 1,
            11 => 1,
            13 => 1,
            15 => 1,
            17 => 1,
            19 => 1,
            21 => 1,
            23 => 1,
            25 => 1,
            27 => 1,
            29 => 1,
            31 => 1,
            33 => 1,
            35 => 1,
            37 => 1,
            39 => 1,
            41 => 1,
            43 => 1,
            45 => 1,
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
