<?php
/**
 * Unit test class for the GitMergeConflict sniff.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\VersionControl;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class GitMergeConflictUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='GitMergeConflictUnitTest.1.inc')
    {
        switch ($testFile) {
        case 'GitMergeConflictUnitTest.1.inc':
            return [
                26 => 1,
                28 => 1,
                30 => 1,
                45 => 1,
                53 => 1,
                55 => 1,
                59 => 1,
                61 => 1,
            ];

        case 'GitMergeConflictUnitTest.2.inc':
            return [
                4  => 1,
                6  => 1,
                8  => 1,
                14 => 1,
                20 => 1,
                22 => 1,
                26 => 1,
                28 => 1,
                30 => 1,
            ];

        case 'GitMergeConflictUnitTest.3.inc':
            return [
                3  => 1,
                5  => 1,
                7  => 1,
                12 => 1,
                14 => 1,
                16 => 1,
                22 => 1,
                24 => 1,
                26 => 1,
                38 => 1,
                40 => 1,
                42 => 1,
            ];

        case 'GitMergeConflictUnitTest.4.inc':
            return [
                6  => 1,
                8  => 1,
                10 => 1,
                18 => 1,
                22 => 1,
                25 => 1,
                29 => 1,
                34 => 1,
                39 => 1,
                53 => 1,
                55 => 1,
                57 => 1,
                64 => 1,
                68 => 1,
                71 => 1,
            ];
        case 'GitMergeConflictUnitTest.5.inc':
        case 'GitMergeConflictUnitTest.6.inc':
            return [
                6  => 1,
                8  => 1,
                10 => 1,
                15 => 1,
                28 => 1,
                30 => 1,
                32 => 1,
            ];

        case 'GitMergeConflictUnitTest.1.css':
            return [
                3  => 1,
                5  => 1,
                7  => 1,
                12 => 1,
                14 => 1,
                16 => 1,
                30 => 1,
                32 => 1,
                34 => 1,
            ];

        case 'GitMergeConflictUnitTest.2.css':
            return [
                3  => 1,
                8  => 1,
                13 => 1,
                27 => 1,
                29 => 1,
                31 => 1,
            ];

        case 'GitMergeConflictUnitTest.js':
            return [
                5  => 1,
                7  => 1,
                9  => 1,
                12 => 1,
                14 => 1,
                16 => 1,
                24 => 1,
                30 => 1,
                32 => 1,
            ];

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
