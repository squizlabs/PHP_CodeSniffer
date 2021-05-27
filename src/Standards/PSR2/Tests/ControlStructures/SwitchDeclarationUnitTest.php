<?php
/**
 * Unit test class for the SwitchDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class SwitchDeclarationUnitTest extends AbstractSniffUnitTest
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
            10  => 1,
            11  => 1,
            14  => 1,
            16  => 1,
            20  => 1,
            23  => 1,
            29  => 1,
            33  => 1,
            37  => 2,
            108 => 2,
            109 => 1,
            111 => 1,
            113 => 2,
            114 => 1,
            128 => 1,
            141 => 1,
            172 => 1,
            194 => 1,
            224 => 1,
            236 => 1,
            260 => 1,
            300 => 1,
            311 => 1,
            346 => 1,
            350 => 1,
            356 => 1,
            362 => 1,
            384 => 1,
            528 => 1,
            541 => 1,
            558 => 1,
            575 => 1,
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
