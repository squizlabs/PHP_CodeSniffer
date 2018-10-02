<?php
/**
 * Unit test class for the FileComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FileCommentUnitTest extends AbstractSniffUnitTest
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
            21 => 1,
            23 => 2,
            24 => 1,
            26 => 1,
            28 => 1,
            29 => 1,
            30 => 1,
            31 => 1,
            32 => 2,
            33 => 1,
            34 => 1,
            35 => 1,
            40 => 2,
            41 => 2,
            42 => 1,
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
            29 => 1,
            30 => 1,
            34 => 1,
            42 => 1,
        ];

    }//end getWarningList()


}//end class
