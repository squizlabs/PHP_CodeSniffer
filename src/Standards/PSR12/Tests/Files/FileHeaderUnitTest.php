<?php
/**
 * Unit test class for the FileHeader sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FileHeaderUnitTest extends AbstractSniffUnitTest
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
        case 'FileHeaderUnitTest.2.inc':
            return [
                1  => 1,
                6  => 1,
                7  => 1,
                18 => 1,
                20 => 1,
                24 => 1,
            ];
        case 'FileHeaderUnitTest.3.inc':
            return [
                9  => 1,
                18 => 1,
            ];
        case 'FileHeaderUnitTest.4.inc':
            return [
                1 => 1,
                2 => 1,
                3 => 1,
                7 => 1,
            ];
        case 'FileHeaderUnitTest.5.inc':
            return [4 => 1];
        case 'FileHeaderUnitTest.7.inc':
        case 'FileHeaderUnitTest.10.inc':
        case 'FileHeaderUnitTest.11.inc':
            return [1 => 1];
        case 'FileHeaderUnitTest.12.inc':
            return [4 => 2];
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
