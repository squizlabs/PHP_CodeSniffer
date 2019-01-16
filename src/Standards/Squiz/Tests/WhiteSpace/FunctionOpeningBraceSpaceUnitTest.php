<?php
/**
 * Unit test class for the FunctionOpeningBraceSpace sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionOpeningBraceSpaceUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='FunctionOpeningBraceSpaceUnitTest.inc')
    {
        switch ($testFile) {
        case 'FunctionOpeningBraceSpaceUnitTest.inc':
            return [
                10 => 1,
                25 => 1,
                49 => 1,
            ];

        case 'FunctionOpeningBraceSpaceUnitTest.js':
            return [
                11 => 1,
                31 => 1,
                38 => 1,
                88 => 1,
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
