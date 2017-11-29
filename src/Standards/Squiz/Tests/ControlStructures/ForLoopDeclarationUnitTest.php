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
                8  => 2,
                11 => 2,
                14 => 2,
                17 => 2,
                21 => 6,
                27 => 1,
                30 => 1,
                37 => 2,
                39 => 2,
            ];
             break;
        case 'ForLoopDeclarationUnitTest.js':
            return [
                6  => 2,
                9  => 2,
                12 => 2,
                15 => 2,
                19 => 6,
                33 => 1,
                36 => 1,
                43 => 2,
                45 => 2,
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
