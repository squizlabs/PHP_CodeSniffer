<?php
/**
 * Unit test class for the FunctionCallSignature sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Tests\Functions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionCallSignatureUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile='FunctionCallSignatureUnitTest.inc')
    {
        if ($testFile === 'FunctionCallSignatureUnitTest.js') {
            return array(
                    5  => 1,
                    6  => 2,
                    7  => 1,
                    8  => 1,
                    9  => 2,
                    10 => 3,
                    17 => 1,
                    18 => 1,
                    21 => 1,
                    24 => 1,
                    28 => 2,
                    30 => 2,
                    35 => 1,
                    49 => 1,
                    51 => 1,
                    54 => 1,
                    70 => 1,
                    71 => 1,
                   );
        }//end if

        return array(
                5   => 1,
                6   => 2,
                7   => 1,
                8   => 1,
                9   => 2,
                10  => 3,
                17  => 1,
                18  => 1,
                31  => 1,
                34  => 1,
                43  => 2,
                57  => 1,
                59  => 1,
                63  => 1,
                64  => 1,
                82  => 1,
                93  => 1,
                100 => 1,
                106 => 2,
                119 => 1,
                120 => 1,
                129 => 1,
                137 => 1,
                142 => 2,
                171 => 1,
                180 => 1,
                181 => 1,
                194 => 1,
                213 => 2,
                215 => 2,
                274 => 1,
                275 => 1,
                300 => 1,
                305 => 1,
               );

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
        return array();

    }//end getWarningList()


}//end class
