<?php
/**
 * Unit test class for the UseDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Tests\Namespaces;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class UseDeclarationUnitTest extends AbstractSniffUnitTest
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
        case 'UseDeclarationUnitTest.2.inc':
            return [
                4  => 1,
                5  => 1,
                6  => 1,
                7  => 1,
                12 => 1,
            ];
        case 'UseDeclarationUnitTest.3.inc':
            return [
                4 => 1,
                6 => 1,
            ];
        case 'UseDeclarationUnitTest.5.inc':
            return [
                5  => 1,
                6  => 1,
                8  => 1,
                14 => 1,
                17 => 1,
                18 => 1,
                19 => 1,
                21 => 1,
                28 => 1,
                30 => 1,
                35 => 1,
            ];
        case 'UseDeclarationUnitTest.10.inc':
        case 'UseDeclarationUnitTest.11.inc':
        case 'UseDeclarationUnitTest.12.inc':
        case 'UseDeclarationUnitTest.13.inc':
        case 'UseDeclarationUnitTest.14.inc':
        case 'UseDeclarationUnitTest.16.inc':
        case 'UseDeclarationUnitTest.17.inc':
            return [2 => 1];
        case 'UseDeclarationUnitTest.15.inc':
            return [
                3 => 1,
                4 => 1,
                5 => 1,
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
