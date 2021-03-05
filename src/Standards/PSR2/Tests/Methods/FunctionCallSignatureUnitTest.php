<?php
/**
 * Unit test class for the FunctionCallSignature sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Tests\Methods;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionCallSignatureUnitTest extends AbstractSniffUnitTest
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
            18  => 3,
            21  => 1,
            48  => 1,
            87  => 1,
            90  => 1,
            91  => 1,
            103 => 1,
            111 => 1,
            117 => 4,
            123 => 1,
            127 => 1,
            131 => 1,
            136 => 1,
            143 => 1,
            148 => 1,
            152 => 1,
            156 => 1,
            160 => 1,
            165 => 1,
            170 => 1,
            175 => 1,
            178 => 2,
            186 => 1,
            187 => 1,
            194 => 3,
            199 => 1,
            200 => 2,
            202 => 1,
            203 => 1,
            210 => 2,
            211 => 1,
            212 => 2,
            217 => 1,
            218 => 1,
            227 => 1,
            228 => 1,
            233 => 1,
            234 => 1,
            242 => 1,
            243 => 1,
            256 => 1,
            257 => 1,
            258 => 1,
            263 => 1,
            264 => 1,
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
