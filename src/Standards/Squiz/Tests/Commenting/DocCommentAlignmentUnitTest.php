<?php
/**
 * Unit test class for the DocCommentAlignment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DocCommentAlignmentUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='DocCommentAlignmentUnitTest.inc')
    {
        $errors = [
            3  => 1,
            11 => 1,
            17 => 1,
            18 => 1,
            19 => 1,
            23 => 2,
            24 => 1,
            25 => 2,
            26 => 1,
            32 => 1,
            33 => 1,
            38 => 1,
            39 => 1,
        ];

        if ($testFile === 'DocCommentAlignmentUnitTest.inc') {
            $errors[75] = 1;
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
