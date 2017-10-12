<?php
/**
 * Unit test class for the ArbitraryParenthesesSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ArbitraryParenthesesSpacingUnitTest extends AbstractSniffUnitTest
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
            64  => 4,
            66  => 1,
            68  => 1,
            69  => 1,
            72  => 2,
            73  => 2,
            77  => 2,
            81  => 4,
            90  => 4,
            94  => 1,
            95  => 1,
            97  => 1,
            100 => 2,
            101 => 2,
            104 => 2,
            107 => 2,
            109 => 4,
            111 => 4,
            113 => 2,
            115 => 2,
            123 => 1,
            125 => 2,
            127 => 1,
            131 => 1,
            133 => 1,
            137 => 1,
            139 => 2,
            141 => 1,
            144 => 1,
            146 => 1,
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
        return [
            55 => 1,
            56 => 1,
        ];

    }//end getWarningList()


}//end class
