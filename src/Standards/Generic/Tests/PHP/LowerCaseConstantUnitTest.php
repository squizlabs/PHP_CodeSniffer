<?php
/**
 * Unit test class for the LowerCaseConstant sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class LowerCaseConstantUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='LowerCaseConstantUnitTest.inc')
    {
        switch ($testFile) {
        case 'LowerCaseConstantUnitTest.inc':
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
            ];
        break;
        case 'LowerCaseConstantUnitTest.js':
            return [
                2  => 1,
                3  => 1,
                4  => 1,
                7  => 1,
                8  => 1,
                12 => 1,
                13 => 1,
                14 => 1,
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
