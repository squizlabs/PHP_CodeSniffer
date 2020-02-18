<?php
/**
 * Unit test class for the EmptyStatement sniff.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class EmptyPHPStatementUnitTest extends AbstractSniffUnitTest
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
        return [];

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
            9  => 1,
            12 => 1,
            15 => 1,
            18 => 1,
            21 => 1,
            22 => 1,
            31 => 1,
            33 => 1,
            43 => 1,
            45 => 1,
            49 => 1,
            50 => 1,
            57 => 1,
            59 => 1,
            61 => 1,
            63 => 2,
            71 => 1,
            72 => 1,
            80 => 1,
        ];

    }//end getWarningList()


}//end class
