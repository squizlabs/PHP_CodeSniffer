<?php
/**
 * Unit test class for the ReturnTypeDeclaration sniff.
 *
 * @author    Arent van Korlaar <avkorlaar@hostnet.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ReturnTypeDeclarationUnitTest extends AbstractSniffUnitTest
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
            17  => 1,
            22  => 1,
            27  => 1,
            32  => 1,
            37  => 1,
            42  => 1,
            47  => 1,
            53  => 1,
            59  => 1,
            76  => 1,
            81  => 1,
            86  => 1,
            91  => 1,
            96  => 1,
            101 => 1,
            106 => 1,
            112 => 1,
            118 => 1,
            133 => 1,
            135 => 1,
            137 => 1,
            139 => 1,
            142 => 1,
            145 => 1,
            148 => 1,
            152 => 1,
            156 => 1,
            170 => 1,
            175 => 1,
            180 => 1,
            185 => 1,
            190 => 1,
            195 => 1,
            200 => 1,
            206 => 1,
            212 => 1,
            223 => 1,
            229 => 1,
            235 => 1,
            240 => 1,
            245 => 1,
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
