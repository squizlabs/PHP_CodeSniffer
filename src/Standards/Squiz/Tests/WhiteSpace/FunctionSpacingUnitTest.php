<?php
/**
 * Unit test class for the FunctionSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionSpacingUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'FunctionSpacingUnitTest.1.inc':
            return [
                26  => 1,
                35  => 1,
                44  => 1,
                51  => 1,
                55  => 1,
                61  => 1,
                64  => 1,
                66  => 1,
                81  => 1,
                100 => 1,
                111 => 1,
                113 => 1,
                119 => 2,
                141 => 1,
                160 => 1,
                173 => 2,
                190 => 1,
                224 => 2,
                281 => 1,
                282 => 1,
                295 => 1,
                297 => 1,
                303 => 1,
                327 => 1,
                329 => 1,
                338 => 1,
                344 => 1,
                345 => 1,
                354 => 2,
                355 => 1,
                356 => 1,
                360 => 2,
                361 => 1,
                362 => 1,
                385 => 1,
                399 => 1,
                411 => 2,
                418 => 2,
                426 => 2,
                432 => 1,
                437 => 1,
                438 => 1,
                442 => 2,
                444 => 1,
                449 => 1,
                458 => 2,
                459 => 1,
                460 => 1,
                465 => 2,
                466 => 1,
                467 => 1,
                471 => 1,
                473 => 2,
                475 => 1,
                478 => 2,
                479 => 1,
                483 => 2,
                495 => 1,
            ];

        case 'FunctionSpacingUnitTest.2.inc':
            return [2 => 1];

        case 'FunctionSpacingUnitTest.3.inc':
            return [7 => 1];

        case 'FunctionSpacingUnitTest.5.inc':
            return [5 => 1];

        case 'FunctionSpacingUnitTest.6.inc':
            return [10 => 1];

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
