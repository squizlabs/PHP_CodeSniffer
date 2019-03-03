<?php
/**
 * Unit test class for the ValidFunctionName sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ValidFunctionNameUnitTest extends AbstractSniffUnitTest
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
            11  => 1,
            12  => 1,
            13  => 1,
            14  => 1,
            15  => 1,
            16  => 1,
            17  => 2,
            18  => 2,
            19  => 2,
            20  => 2,
            24  => 1,
            25  => 1,
            26  => 1,
            27  => 1,
            28  => 1,
            29  => 1,
            30  => 2,
            31  => 2,
            32  => 2,
            33  => 2,
            35  => 1,
            36  => 1,
            37  => 2,
            38  => 2,
            39  => 2,
            40  => 2,
            43  => 1,
            44  => 1,
            45  => 1,
            46  => 1,
            50  => 1,
            51  => 1,
            52  => 1,
            53  => 1,
            56  => 1,
            57  => 1,
            58  => 1,
            59  => 1,
            67  => 1,
            68  => 1,
            69  => 1,
            70  => 1,
            71  => 1,
            72  => 1,
            73  => 2,
            74  => 2,
            75  => 2,
            76  => 2,
            80  => 1,
            81  => 1,
            82  => 1,
            83  => 1,
            86  => 1,
            87  => 1,
            88  => 1,
            89  => 1,
            95  => 1,
            96  => 1,
            97  => 1,
            98  => 1,
            99  => 1,
            100 => 1,
            101 => 2,
            102 => 2,
            103 => 2,
            104 => 2,
            113 => 1,
            114 => 1,
            115 => 2,
            118 => 1,
            119 => 1,
            120 => 3,
            122 => 1,
            123 => 3,
            125 => 2,
            126 => 1,
            127 => 1,
            148 => 2,
            153 => 1,
            154 => 1,
            155 => 1,
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
