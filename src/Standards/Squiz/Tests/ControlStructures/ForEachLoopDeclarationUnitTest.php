<?php
/**
 * Unit test class for the ForEachLoopDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ForEachLoopDeclarationUnitTest extends AbstractSniffUnitTest
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
            8  => 2,
            11 => 2,
            14 => 2,
            17 => 5,
            21 => 7,
            26 => 2,
            28 => 2,
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
