<?php
/**
 * Unit test class for the LanguageConstructSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class LanguageConstructSpacingUnitTest extends AbstractSniffUnitTest
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
            3  => 1,
            7  => 1,
            11 => 1,
            15 => 1,
            19 => 1,
            23 => 1,
            27 => 1,
            30 => 1,
            33 => 1,
            34 => 2,
            35 => 1,
            38 => 1,
            39 => 1,
            40 => 2,
            43 => 1,
            45 => 1,
            47 => 1,
            50 => 1,
            52 => 1,
            54 => 1,
            58 => 1,
            61 => 1,
            62 => 1,
            66 => 1,
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
