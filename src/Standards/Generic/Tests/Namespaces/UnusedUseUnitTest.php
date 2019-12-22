<?php
/**
 * Unit test class for the UnusedUse sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Namespaces;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class UnusedUseUnitTest extends AbstractSniffUnitTest
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
            12 => 1,
            13 => 1,
            14 => 1,
            15 => 2,
            16 => 2,
            18 => 1,
            20 => 1,
            21 => 1,
            22 => 1,
            23 => 1,
            93 => 1,
            95 => 1,
        ];

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return [];

    }//end getWarningList()


}//end class
