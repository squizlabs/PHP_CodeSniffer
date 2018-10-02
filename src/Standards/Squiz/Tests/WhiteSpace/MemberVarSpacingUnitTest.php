<?php
/**
 * Unit test class for the MemberVarSpacing sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class MemberVarSpacingUnitTest extends AbstractSniffUnitTest
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
            7   => 1,
            20  => 1,
            30  => 1,
            35  => 1,
            44  => 1,
            50  => 1,
            73  => 1,
            86  => 1,
            106 => 1,
            115 => 1,
            150 => 1,
            160 => 1,
            165 => 1,
            177 => 1,
            186 => 1,
            200 => 1,
            209 => 1,
            211 => 1,
            224 => 1,
            229 => 1,
            241 => 1,
            246 => 1,
            252 => 1,
            254 => 1,
            261 => 1,
            275 => 1,
            276 => 1,
            288 => 1,
            292 => 1,
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
