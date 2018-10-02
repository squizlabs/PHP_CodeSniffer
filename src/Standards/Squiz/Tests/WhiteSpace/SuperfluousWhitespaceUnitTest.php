<?php
/**
 * Unit test class for the SuperfluousWhitespace sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class SuperfluousWhitespaceUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='SuperfluousWhitespaceUnitTest.inc')
    {
        switch ($testFile) {
        case 'SuperfluousWhitespaceUnitTest.1.inc':
            return [
                2  => 1,
                4  => 1,
                5  => 1,
                6  => 1,
                7  => 1,
                16 => 1,
                23 => 1,
                28 => 1,
                33 => 1,
                49 => 1,
                55 => 1,
            ];
            break;
        case 'SuperfluousWhitespaceUnitTest.2.inc':
            return [
                2 => 1,
                8 => 1,
            ];
            break;
        case 'SuperfluousWhitespaceUnitTest.3.inc':
            return [
                6  => 1,
                10 => 1,
            ];
            break;
        case 'SuperfluousWhitespaceUnitTest.4.inc':
        case 'SuperfluousWhitespaceUnitTest.5.inc':
            return [
                1 => 1,
                4 => 1,
            ];
            break;
        case 'SuperfluousWhitespaceUnitTest.1.js':
            return [
                1  => 1,
                3  => 1,
                4  => 1,
                5  => 1,
                6  => 1,
                15 => 1,
                22 => 1,
                29 => 1,
                38 => 1,
                56 => 1,
            ];
            break;
        case 'SuperfluousWhitespaceUnitTest.1.css':
            return [
                1  => 1,
                8  => 1,
                9  => 1,
                11 => 1,
                25 => 1,
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
