<?php
/**
 * Unit test class for the ForLoopDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ForLoopDeclarationUnitTest extends AbstractSniffUnitTest
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
            8   => 2,
            11  => 2,
            14  => 2,
            17  => 2,
            21  => 6,
            27  => 1,
            30  => 1,
            37  => 2,
            39  => 2,
            43  => 1,
            49  => 1,
            50  => 1,
            53  => 1,
            54  => 1,
            59  => 4,
            62  => 1,
            63  => 1,
            64  => 1,
            66  => 1,
            69  => 1,
            74  => 1,
            77  => 1,
            82  => 2,
            86  => 2,
            91  => 1,
            95  => 1,
            101 => 2,
            105 => 2,
            110 => 1,
            116 => 2,
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
