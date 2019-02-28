<?php
/**
 * Unit test class for the ClassDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Tests\Classes;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ClassDeclarationUnitTest extends AbstractSniffUnitTest
{


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
            2   => 1,
            7   => 3,
            12  => 1,
            13  => 1,
            17  => 1,
            19  => 2,
            20  => 1,
            21  => 1,
            22  => 1,
            25  => 1,
            27  => 2,
            34  => 1,
            35  => 2,
            44  => 1,
            45  => 1,
            63  => 1,
            95  => 1,
            116 => 1,
            118 => 1,
            119 => 1,
            124 => 1,
            130 => 2,
            131 => 1,
            158 => 1,
            168 => 1,
            178 => 1,
            179 => 1,
            184 => 1,
            189 => 1,
            194 => 1,
            204 => 1,
            205 => 1,
            210 => 1,
            215 => 2,
            216 => 1,
            231 => 2,
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
