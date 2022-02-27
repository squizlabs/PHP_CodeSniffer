<?php
/**
 * Unit test class for the BracketsNewLineSniff
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2018 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;


class BracketsNewLineUnitTest extends AbstractSniffUnitTest
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
            10  => 1,
            16  => 1,
            22  => 1,
            26  => 1,
            35  => 1,
            38  => 1,
            44  => 1,
            51  => 1,
            57  => 1,
            74  => 1,
            78  => 1,
            106 => 1,
            112 => 1,
            120 => 1,
            126 => 1,
            135 => 1,
            182 => 1,
            188 => 1,
            209 => 1,
            227 => 1,
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
