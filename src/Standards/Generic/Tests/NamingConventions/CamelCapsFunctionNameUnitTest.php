<?php
/**
 * Unit test class for the CamelCapsFunctionName sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class CamelCapsFunctionNameUnitTest extends AbstractSniffUnitTest
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
        $errors = [
            10 => 1,
            11 => 1,
            12 => 1,
            13 => 1,
            16 => 1,
            17 => 1,
            20 => 1,
            21 => 1,
            24 => 1,
            25 => 1,
            30 => 1,
            31 => 1,
            40 => 1,
            41 => 1,
            42 => 2,
            46 => 1,
            47 => 1,
            48 => 2,
            50 => 1,
            51 => 2,
            80 => 1,
            81 => 1,
        ];

        return $errors;

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
