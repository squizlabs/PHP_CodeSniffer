<?php
/**
 * Unit test class for the DeclareStrictTypes sniff.
 *
 * @author    Michał Bundyra <contact@webimpress.com>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DeclareStrictTypesUnitTest extends AbstractSniffUnitTest
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
        case 'DeclareStrictTypesUnitTest.1.inc':
            return [1 => 1];
        case 'DeclareStrictTypesUnitTest.2.inc':
            return [2 => 1];
        case 'DeclareStrictTypesUnitTest.3.inc':
            return [8 => 1];
        case 'DeclareStrictTypesUnitTest.4.inc':
            return [1 => 1];
        case 'DeclareStrictTypesUnitTest.5.inc':
            return [2 => 2];
        case 'DeclareStrictTypesUnitTest.6.inc':
            return [1 => 2];
        case 'DeclareStrictTypesUnitTest.7.inc':
            return [
                1 => 1,
                6 => 1,
            ];
        case 'DeclareStrictTypesUnitTest.8.inc':
            return [6 => 1];
        case 'DeclareStrictTypesUnitTest.9.inc':
            return [1 => 2];
        case 'DeclareStrictTypesUnitTest.10.inc':
            return [
                1 => 1,
                4 => 1,
            ];
        case 'DeclareStrictTypesUnitTest.11.inc':
            return [
                1 => 1,
                5 => 1,
            ];
        case 'DeclareStrictTypesUnitTest.12.inc':
            return [3 => 2];
        case 'DeclareStrictTypesUnitTest.13.inc':
            return [2 => 2];
        }//end switch

        return [
            1 => 1,
            5 => 1,
        ];

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
