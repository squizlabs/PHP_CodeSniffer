<?php
/**
 * Unit test class for the LineLength sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class LineLengthUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of CLI values to set before the file is tested.
     *
     * @param string                  $testFile The name of the file being tested.
     * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
     *
     * @return void
     */
    public function setCliValues($testFile, $config)
    {
        $config->tabWidth = 4;

    }//end setCliValues()


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
        case 'LineLengthUnitTest.1.inc':
            return [
                31 => 1,
                34 => 1,
                45 => 1,
                82 => 1,
            ];
            break;
        case 'LineLengthUnitTest.2.inc':
        case 'LineLengthUnitTest.3.inc':
            return [7 => 1];
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
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'LineLengthUnitTest.1.inc':
            return [
                9  => 1,
                15 => 1,
                21 => 1,
                24 => 1,
                29 => 1,
                37 => 1,
                63 => 1,
                73 => 1,
                75 => 1,
                84 => 1,
            ];
            break;
        case 'LineLengthUnitTest.2.inc':
        case 'LineLengthUnitTest.3.inc':
            return [6 => 1];
            break;
        case 'LineLengthUnitTest.4.inc':
            return [
                10 => 1,
                14 => 1,
            ];
            break;
        default:
            return [];
            break;
        }//end switch

    }//end getWarningList()


}//end class
