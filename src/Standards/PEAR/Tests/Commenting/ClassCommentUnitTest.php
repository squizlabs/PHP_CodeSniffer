<?php
/**
 * Unit test class for the ClassComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ClassCommentUnitTest extends AbstractSniffUnitTest
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
            4   => 1,
            15  => 1,
            51  => 1,
            63  => 1,
            65  => 2,
            66  => 1,
            68  => 1,
            70  => 1,
            71  => 1,
            72  => 1,
            74  => 2,
            75  => 1,
            76  => 1,
            77  => 1,
            85  => 1,
            96  => 5,
            106 => 5,
            116 => 5,
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
        return [
            71 => 1,
            73 => 1,
        ];

    }//end getWarningList()


}//end class
