<?php
/**
 * Unit test class for the DeclareStatement sniff.
 *
 * @author    Sertan Danis <sdanis@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DeclareStatementUnitTest extends AbstractSniffUnitTest
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
            2  => 1,
            3  => 1,
            4  => 1,
            5  => 2,
            6  => 1,
            7  => 1,
            9  => 2,
            10 => 1,
            11 => 3,
            12 => 2,
            13 => 1,
            14 => 2,
            16 => 3,
            19 => 3,
            22 => 1,
            24 => 1,
            26 => 3,
            28 => 3,
            34 => 2,
            43 => 1,
            46 => 1,
            47 => 1,
            49 => 1,
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
