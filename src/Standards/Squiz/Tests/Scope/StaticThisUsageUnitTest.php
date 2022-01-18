<?php
/**
 * Unit test class for the StaticThisUsage sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Scope;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class StaticThisUsageUnitTest extends AbstractSniffUnitTest
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
            7   => 1,
            8   => 1,
            9   => 1,
            14  => 1,
            20  => 1,
            41  => 1,
            61  => 1,
            69  => 1,
            76  => 1,
            80  => 1,
            84  => 1,
            99  => 1,
            125 => 1,
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
