<?php
/**
 * Unit test class for the FunctionDeclarationArgumentSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionDeclarationArgumentSpacingUnitTest extends AbstractSniffUnitTest
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
            3   => 1,
            5   => 2,
            7   => 2,
            8   => 2,
            9   => 2,
            11  => 2,
            13  => 7,
            14  => 2,
            15  => 2,
            16  => 4,
            18  => 2,
            35  => 2,
            36  => 2,
            44  => 2,
            45  => 1,
            46  => 1,
            51  => 2,
            53  => 2,
            55  => 1,
            56  => 1,
            58  => 1,
            73  => 7,
            76  => 1,
            77  => 1,
            81  => 1,
            89  => 2,
            92  => 1,
            93  => 1,
            94  => 1,
            95  => 1,
            99  => 11,
            100 => 2,
            101 => 2,
            102 => 2,
            106 => 1,
            107 => 2,
            111 => 3,
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
