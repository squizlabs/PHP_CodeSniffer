<?php
/**
 * Unit test class for the ForLoopDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ForLoopDeclarationUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='ForLoopDeclarationUnitTest.inc')
    {
        switch ($testFile) {
        case 'ForLoopDeclarationUnitTest.inc':
            return [
                8   => 2,
                11  => 2,
                14  => 2,
                17  => 2,
                21  => 6,
                27  => 1,
                30  => 1,
                37  => 2,
                39  => 2,
                43  => 1,
                49  => 1,
                50  => 1,
                53  => 1,
                54  => 1,
                59  => 4,
                62  => 1,
                63  => 1,
                64  => 1,
                66  => 1,
                69  => 1,
                74  => 1,
                77  => 1,
                82  => 2,
                86  => 2,
                91  => 1,
                95  => 1,
                101 => 2,
                105 => 2,
                110 => 1,
                116 => 2,
            ];

        case 'ForLoopDeclarationUnitTest.js':
            return [
                6   => 2,
                9   => 2,
                12  => 2,
                15  => 2,
                19  => 6,
                33  => 1,
                36  => 1,
                43  => 2,
                45  => 2,
                49  => 1,
                55  => 1,
                56  => 1,
                59  => 1,
                60  => 1,
                65  => 4,
                68  => 1,
                69  => 1,
                70  => 1,
                72  => 1,
                75  => 1,
                80  => 1,
                83  => 1,
                88  => 2,
                92  => 2,
                97  => 1,
                101 => 1,
                107 => 2,
                111 => 2,
                116 => 1,
                122 => 2,
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
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='ForLoopDeclarationUnitTest.inc')
    {
        switch ($testFile) {
        case 'ForLoopDeclarationUnitTest.inc':
            return [129 => 1];

        case 'ForLoopDeclarationUnitTest.js':
            return [125 => 1];

        default:
            return [];
        }//end switch

    }//end getWarningList()


}//end class
