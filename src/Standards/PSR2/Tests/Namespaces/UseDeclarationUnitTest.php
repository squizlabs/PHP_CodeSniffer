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
            return array(
                    4  => 1,
                    5  => 1,
                    10 => 2,
                   );
        case 'UseDeclarationUnitTest.3.inc':
            return array(
                    4 => 1,
                    6 => 1,
                   );
        case 'UseDeclarationUnitTest.5.inc':
            return array(
                    5  => 1,
                    6  => 1,
                    8  => 1,
                    14 => 1,
                    17 => 1,
                    18 => 1,
                    19 => 1,
                   );
        default:
            return array();
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
        return array();

    }//end getWarningList()


}//end class
