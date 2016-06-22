<?php
/**
 * Unit test class for the NonExecutableCode sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class NonExecutableCodeUnitTest extends AbstractSniffUnitTest
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
        return array();

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
        return array(
                5   => 1,
                11  => 1,
                17  => 1,
                18  => 1,
                19  => 2,
                28  => 1,
                32  => 1,
                33  => 2,
                34  => 2,
                42  => 1,
                45  => 1,
                54  => 1,
                58  => 1,
                73  => 1,
                83  => 1,
                95  => 1,
                105 => 1,
                123 => 1,
                147 => 1,
                150 => 1,
                153 => 1,
                166 => 1,
                180 => 1,
                232 => 1,
                233 => 1,
                234 => 1,
                235 => 2,
                239 => 1,
               );

    }//end getWarningList()


}//end class
