<?php
/**
 * Unit test class for the SpaceAfterNot sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Formatting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class SpaceAfterNotUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'SpaceAfterNotUnitTest.inc':
            return [
                3  => 2,
                4  => 2,
                5  => 2,
                6  => 1,
                7  => 1,
                8  => 1,
                11 => 1,
                14 => 1,
                17 => 1,
                20 => 1,
                28 => 1,
                38 => 2,
                39 => 2,
                40 => 1,
                41 => 1,
                42 => 1,
                48 => 1,
                51 => 1,
                56 => 2,
                57 => 1,
                58 => 1,
                59 => 1,
                62 => 1,
                65 => 1,
                68 => 1,
                71 => 1,
                79 => 1,
            ];

        case 'SpaceAfterNotUnitTest.js':
            return [
                2 => 2,
                4 => 2,
                5 => 1,
            ];

        default:
            return [];
        }//end switch

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
