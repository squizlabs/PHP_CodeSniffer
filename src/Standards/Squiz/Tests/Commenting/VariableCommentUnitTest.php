<?php
/**
 * Unit test class for the VariableComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class VariableCommentUnitTest extends AbstractSniffUnitTest
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
            21  => 1,
            24  => 1,
            56  => 1,
            64  => 1,
            73  => 1,
            84  => 1,
            130 => 1,
            136 => 1,
            144 => 1,
            152 => 1,
            160 => 1,
            168 => 1,
            176 => 1,
            184 => 1,
            192 => 1,
            200 => 1,
            208 => 1,
            216 => 1,
            224 => 1,
            232 => 1,
            240 => 1,
            248 => 1,
            256 => 1,
            264 => 1,
            272 => 1,
            280 => 1,
            290 => 1,
            294 => 1,
            311 => 1,
            336 => 1,
            361 => 1,
            364 => 1,
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
        return [93 => 1];

    }//end getWarningList()


}//end class
