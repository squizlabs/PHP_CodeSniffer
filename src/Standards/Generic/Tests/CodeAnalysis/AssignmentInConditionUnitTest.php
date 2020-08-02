<?php
/**
 * Unit test class for the AssignmentInCondition sniff.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class AssignmentInConditionUnitTest extends AbstractSniffUnitTest
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
            46 => 1,
            47 => 1,
            48 => 1,
            49 => 1,
            50 => 1,
            51 => 1,
            52 => 1,
            53 => 1,
            54 => 1,
            55 => 1,
            56 => 1,
            57 => 1,
            58 => 1,
            59 => 1,
            60 => 1,
            61 => 2,
            63 => 1,
            64 => 1,
            67 => 1,
            68 => 1,
            69 => 1,
            70 => 1,
            71 => 1,
            72 => 1,
            73 => 1,
            75 => 1,
            77 => 1,
            80 => 2,
            84 => 1,
            85 => 2,
            88 => 1,
            90 => 1,
            92 => 1,
            95 => 1,
        ];

    }//end getWarningList()


}//end class
