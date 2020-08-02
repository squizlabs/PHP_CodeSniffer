<?php
/**
 * Unit test class for the LongConditionClosingComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class LongConditionClosingCommentUnitTest extends AbstractSniffUnitTest
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
            49   => 1,
            99   => 1,
            146  => 1,
            192  => 1,
            215  => 1,
            238  => 1,
            261  => 1,
            286  => 1,
            309  => 1,
            332  => 1,
            355  => 1,
            378  => 1,
            493  => 1,
            531  => 1,
            536  => 1,
            540  => 1,
            562  => 1,
            601  => 1,
            629  => 1,
            663  => 1,
            765  => 1,
            798  => 1,
            811  => 1,
            897  => 1,
            931  => 1,
            962  => 1,
            985  => 2,
            1008 => 1,
            1032 => 1,
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
        return [];

    }//end getWarningList()


}//end class
