<?php
/**
 * Unit test class for the ControlStructureSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ControlStructureSpacingUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='ControlStructureSpacingUnitTest.inc')
    {
        switch ($testFile) {
        case 'ControlStructureSpacingUnitTest.inc':
            return [
                3   => 1,
                5   => 1,
                8   => 1,
                15  => 1,
                23  => 1,
                74  => 1,
                79  => 1,
                82  => 1,
                83  => 1,
                87  => 1,
                103 => 1,
                113 => 2,
                114 => 2,
                118 => 1,
                150 => 1,
                153 => 1,
                154 => 1,
                157 => 1,
                170 => 1,
                176 => 2,
                179 => 1,
                189 => 1,
                222 => 1,
                233 => 1,
                235 => 1,
            ];
            break;
        case 'ControlStructureSpacingUnitTest.js':
            return [
                3  => 1,
                9  => 1,
                15 => 1,
                21 => 1,
                56 => 1,
                61 => 1,
                64 => 1,
                65 => 1,
                68 => 1,
                74 => 2,
                75 => 2,
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
