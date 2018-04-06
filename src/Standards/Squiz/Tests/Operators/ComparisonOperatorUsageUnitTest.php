<?php
/**
 * Unit test class for the ComparisonOperatorUsage sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Operators;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ComparisonOperatorUsageUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='ComparisonOperatorUsageUnitTest.inc')
    {
        switch ($testFile) {
        case 'ComparisonOperatorUsageUnitTest.inc':
            return [
                6   => 1,
                7   => 1,
                10  => 1,
                11  => 1,
                18  => 1,
                19  => 1,
                22  => 1,
                23  => 1,
                29  => 2,
                32  => 2,
                38  => 4,
                47  => 2,
                69  => 1,
                72  => 1,
                75  => 1,
                78  => 1,
                80  => 1,
                82  => 1,
                83  => 1,
                89  => 1,
                92  => 1,
                100 => 1,
                106 => 1,
                112 => 1,
                123 => 1,
                127 => 1,
                131 => 1,
                135 => 1,
            ];
            break;
        case 'ComparisonOperatorUsageUnitTest.js':
            return [
                5  => 1,
                6  => 1,
                17 => 1,
                18 => 1,
                28 => 2,
                40 => 1,
                47 => 1,
                52 => 1,
                63 => 1,
                67 => 1,
                71 => 1,
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
