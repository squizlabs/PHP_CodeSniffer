<?php
/**
 * Unit test class for the InlineIfDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class InlineIfDeclarationUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of CLI values to set befor the file is tested.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array
     */
    public function getCliValues($testFile)
    {
        return ['--encoding=utf-8'];

    }//end getCliValues()


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        return [
            4  => 1,
            5  => 1,
            6  => 1,
            7  => 1,
            8  => 1,
            9  => 1,
            10 => 1,
            13 => 1,
            20 => 1,
            24 => 4,
            44 => 1,
            47 => 1,
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
