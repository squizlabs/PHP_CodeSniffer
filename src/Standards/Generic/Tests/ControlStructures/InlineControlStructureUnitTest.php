<?php
/**
 * Unit test class for the InlineControlStructure sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class InlineControlStructureUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='InlineControlStructureUnitTest.1.inc')
    {
        switch ($testFile) {
        case 'InlineControlStructureUnitTest.1.inc':
            return [
                3   => 1,
                7   => 1,
                11  => 1,
                13  => 1,
                15  => 1,
                17  => 1,
                23  => 1,
                45  => 1,
                46  => 1,
                49  => 1,
                62  => 1,
                66  => 1,
                78  => 1,
                120 => 1,
                128 => 1,
                134 => 1,
                142 => 1,
                143 => 1,
                144 => 1,
                150 => 1,
                158 => 1,
                159 => 1,
                162 => 1,
                163 => 1,
                164 => 1,
                167 => 1,
                168 => 1,
                170 => 1,
                178 => 1,
                185 => 1,
                188 => 2,
                191 => 1,
                195 => 1,
                198 => 1,
                206 => 1,
                222 => 1,
                232 => 1,
                235 => 1,
                236 => 1,
                238 => 1,
                242 => 1,
            ];

        case 'InlineControlStructureUnitTest.js':
            return [
                3  => 1,
                7  => 1,
                11 => 1,
                13 => 1,
                15 => 1,
                21 => 1,
                27 => 1,
                30 => 1,
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
