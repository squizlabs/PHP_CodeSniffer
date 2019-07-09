<?php
/**
 * Unit test class for the ControlStructuresBracketsNewLineSniff
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2018 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;


class ControlStructuresBracketsNewLineUnitTest extends AbstractSniffUnitTest
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
            6  => 1,
            12  => 1,
            18  => 1,
            22  => 1,
            29  => 1,
            34  => 1,
            40  => 1,
            47  => 1,
            53  => 1,
            70  => 1,
            74  => 1,
            102  => 1,
            108  => 1,
            116 => 1,
            122 => 1,
            131 => 1,
            178 => 1,
            184 => 1,
            205 => 1,
            223 => 1,
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
