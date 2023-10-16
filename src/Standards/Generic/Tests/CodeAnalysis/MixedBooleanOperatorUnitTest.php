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
            3   => 1,
            7   => 1,
            12  => 1,
            17  => 1,
            29  => 1,
            31  => 1,
            33  => 1,
            34  => 1,
            35  => 1,
            37  => 1,
            39  => 1,
            41  => 2,
            43  => 2,
            44  => 1,
            47  => 1,
            61  => 1,
            65  => 3,
            68  => 2,
            71  => 1,
            72  => 1,
            73  => 1,
            76  => 2,
            78  => 1,
            79  => 1,
            80  => 1,
            81  => 2,
            83  => 1,
            92  => 1,
            110 => 1,
            126 => 1,
            128 => 1,
            130 => 1,

            // Debatable.
            103 => 1,
            116 => 1,
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
