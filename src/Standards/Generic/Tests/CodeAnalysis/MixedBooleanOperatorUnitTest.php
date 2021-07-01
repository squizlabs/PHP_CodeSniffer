<?php
/**
 * Unit test class for the MixedBooleanOperator sniff.
 *
 * @author    Tim Duesterhus <duesterhus@woltlab.com>
 * @copyright 2021 WoltLab GmbH.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\CodeAnalysis;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class MixedBooleanOperatorUnitTest extends AbstractSniffUnitTest
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
            3  => 1,
            7  => 1,
            12 => 1,
            17 => 1,
            29 => 1,
            31 => 1,
            33 => 1,
            34 => 1,
            35 => 1,
            37 => 1,
            39 => 1,
            41 => 2,
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
