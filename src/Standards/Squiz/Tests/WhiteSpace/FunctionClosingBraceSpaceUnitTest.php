<?php
/**
 * Unit test class for the FunctionClosingBraceSpace sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionClosingBraceSpaceUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='FunctionClosingBraceSpaceUnitTest.inc')
    {
        switch ($testFile) {
        case 'FunctionClosingBraceSpaceUnitTest.inc':
            return [
                10 => 1,
                21 => 1,
                28 => 1,
                29 => 1,
                31 => 1,
                39 => 1,
            ];
            break;
        case 'FunctionClosingBraceSpaceUnitTest.js':
            return [
                13  => 1,
                25  => 1,
                32  => 1,
                53  => 1,
                59  => 1,
                67  => 1,
                84  => 1,
                128 => 1,
            ];
            break;
        default:
            return [];
            break;
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
