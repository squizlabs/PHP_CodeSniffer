<?php
/**
 * Unit test class for the IncrementDecrementSpacing sniff.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class IncrementDecrementSpacingUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='IncrementDecrementSpacingUnitTest.inc')
    {
        $errors = [
            5  => 1,
            6  => 1,
            8  => 1,
            10 => 1,
            13 => 1,
            14 => 1,
            16 => 1,
            17 => 1,
        ];

        switch ($testFile) {
        case 'IncrementDecrementSpacingUnitTest.inc':
            $errors[21] = 1;
            $errors[23] = 1;
            $errors[26] = 1;
            $errors[27] = 1;
            $errors[30] = 1;
            $errors[31] = 1;
            $errors[34] = 1;
            $errors[37] = 1;

            return $errors;

        case 'IncrementDecrementSpacingUnitTest.js':
            return $errors;

        default:
            return [];
        }//end switch

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
