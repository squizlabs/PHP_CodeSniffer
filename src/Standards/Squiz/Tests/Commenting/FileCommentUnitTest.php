<?php
/**
 * Unit test class for the FileComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FileCommentUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='FileCommentUnitTest.inc')
    {
        switch ($testFile) {
        case 'FileCommentUnitTest.1.inc':
        case 'FileCommentUnitTest.js':
            return [
                1  => 1,
                22 => 2,
                23 => 1,
                24 => 2,
                25 => 2,
                26 => 1,
                27 => 2,
                28 => 2,
                32 => 2,
            ];
        case 'FileCommentUnitTest.3.inc':
            // HHVM just removes the entire comment token, as if it was never there.
            if (defined('HHVM_VERSION') === true) {
                return [1 => 1];
            }
            return [];
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
