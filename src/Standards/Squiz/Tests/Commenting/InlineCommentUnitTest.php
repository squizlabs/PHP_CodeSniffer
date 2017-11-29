<?php
/**
 * Unit test class for the InlineComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class InlineCommentUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='InlineCommentUnitTest.inc')
    {
        switch ($testFile) {
        case 'InlineCommentUnitTest.inc':
            $errors = [
                17  => 1,
                27  => 1,
                28  => 1,
                32  => 2,
                36  => 1,
                44  => 2,
                58  => 1,
                61  => 1,
                64  => 1,
                67  => 1,
                95  => 1,
                96  => 1,
                97  => 3,
                118 => 1,
                126 => 2,
                130 => 2,
            ];

            return $errors;
        case 'InlineCommentUnitTest.js':
            return [
                31  => 1,
                36  => 2,
                48  => 1,
                51  => 1,
                54  => 1,
                57  => 1,
                102 => 1,
                103 => 1,
                104 => 3,
                118 => 1,
                121 => 1,
                125 => 2,
                129 => 2,
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
