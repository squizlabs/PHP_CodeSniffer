<?php
/**
 * Unit test class for the MultiLineCondition sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class MultiLineConditionUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='MultiLineConditionUnitTest.inc')
    {
        $errors = [
            21  => 1,
            22  => 1,
            35  => 1,
            40  => 1,
            41  => 1,
            42  => 1,
            43  => 1,
            49  => 1,
            54  => 1,
            57  => 1,
            58  => 1,
            59  => 1,
            61  => 1,
            67  => 1,
            87  => 1,
            88  => 1,
            89  => 1,
            90  => 1,
            96  => 2,
            101 => 1,
            109 => 2,
            125 => 1,
            145 => 2,
            153 => 2,
            168 => 1,
            177 => 1,
            194 => 2,
            202 => 2,
            215 => 1,
            218 => 2,
            232 => 2,
            239 => 1,
            240 => 2,
            248 => 2,
        ];

        if ($testFile === 'MultiLineConditionUnitTest.inc') {
            $errors[183] = 1;
        }

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
