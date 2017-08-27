<?php
/**
 * Unit test class for the FunctionDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionDeclarationUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='FunctionDeclarationUnitTest.inc')
    {
        if ($testFile === 'FunctionDeclarationUnitTest.inc') {
            $errors = array(
                       3   => 1,
                       4   => 1,
                       5   => 1,
                       9   => 1,
                       10  => 1,
                       11  => 1,
                       14  => 1,
                       17  => 1,
                       44  => 1,
                       52  => 1,
                       61  => 2,
                       98  => 1,
                       110 => 2,
                       120 => 3,
                       121 => 1,
                       140 => 1,
                       145 => 1,
                       161 => 2,
                       162 => 2,
                       164 => 2,
                       167 => 2,
                       171 => 1,
                       173 => 1,
                       201 => 1,
                       206 => 1,
                       208 => 1,
                       216 => 1,
                       223 => 1,
                       230 => 1,
                       237 => 1,
                      );
        } else {
            $errors = array(
                       3  => 1,
                       4  => 1,
                       5  => 1,
                       9  => 1,
                       10 => 1,
                       11 => 1,
                       14 => 1,
                       17 => 1,
                       41 => 1,
                       48 => 1,
                      );
        }//end if

        return $errors;

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
