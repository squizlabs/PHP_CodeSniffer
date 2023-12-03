<?php
/**
 * Unit test class for the ClassDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Classes;

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
            5   => 1,
            6   => 1,
            10  => 1,
            15  => 2,
            18  => 1,
            22  => 4,
            23  => 4,
            24  => 4,
            27  => 2,
            30  => 2,
            34  => 1,
            35  => 1,
            39  => 1,
            42  => 1,
            45  => 1,
            48  => 1,
            50  => 2,
            51  => 1,
            55  => 1,
            59  => 4,
            63  => 1,
            65  => 1,
            69  => 3,
            74  => 2,
            77  => 1,
            80  => 1,
            85  => 3,
            89  => 1,
            92  => 1,
            97  => 1,
            108 => 1,
            114 => 1,
            116 => 1,
            118 => 1,
            121 => 1,
            124 => 2,
            128 => 2,
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
