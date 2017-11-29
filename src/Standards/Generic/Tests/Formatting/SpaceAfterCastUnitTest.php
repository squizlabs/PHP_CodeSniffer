<?php
/**
 * Unit test class for the SpaceAfterCast sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Formatting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class SpaceAfterCastUnitTest extends AbstractSniffUnitTest
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
            4  => 1,
            5  => 1,
            8  => 1,
            9  => 1,
            12 => 1,
            13 => 1,
            16 => 1,
            17 => 1,
            20 => 1,
            21 => 1,
            24 => 1,
            25 => 1,
            28 => 1,
            29 => 1,
            32 => 1,
            33 => 1,
            36 => 1,
            37 => 1,
            40 => 1,
            41 => 1,
            44 => 1,
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
