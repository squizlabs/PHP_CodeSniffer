<?php
/**
 * Unit test class for the ArrayDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Arrays;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ArrayDeclarationUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'ArrayDeclarationUnitTest.1.inc':
            return array(
                    7   => 2,
                    9   => 2,
                    22  => 1,
                    23  => 1,
                    24  => 1,
                    25  => 1,
                    31  => 1,
                    35  => 1,
                    36  => 1,
                    41  => 1,
                    46  => 1,
                    47  => 1,
                    50  => 1,
                    51  => 1,
                    53  => 1,
                    56  => 1,
                    58  => 1,
                    61  => 1,
                    62  => 1,
                    63  => 1,
                    64  => 1,
                    65  => 1,
                    66  => 3,
                    70  => 1,
                    76  => 2,
                    77  => 1,
                    78  => 7,
                    79  => 2,
                    81  => 2,
                    82  => 4,
                    87  => 1,
                    88  => 1,
                    92  => 1,
                    97  => 1,
                    100 => 1,
                    101 => 1,
                    102 => 1,
                    105 => 1,
                    106 => 1,
                    107 => 1,
                    125 => 1,
                    126 => 1,
                    141 => 1,
                    144 => 1,
                    146 => 1,
                    148 => 1,
                    151 => 1,
                    157 => 1,
                    174 => 3,
                    179 => 1,
                    182 => 1,
                    188 => 1,
                    207 => 1,
                    212 => 1,
                    214 => 1,
                    218 => 2,
                    219 => 2,
                    223 => 1,
                    255 => 1,
                    294 => 1,
                    295 => 1,
                    296 => 1,
                    311 => 1,
                    317 => 1,
                    339 => 2,
                    348 => 2,
                    352 => 2,
                   );
        case 'ArrayDeclarationUnitTest.2.inc':
            return array(
                    9   => 1,
                    23  => 1,
                    24  => 1,
                    25  => 1,
                    31  => 1,
                    36  => 1,
                    41  => 1,
                    46  => 1,
                    47  => 1,
                    51  => 1,
                    53  => 1,
                    56  => 1,
                    61  => 1,
                    63  => 1,
                    64  => 1,
                    65  => 1,
                    66  => 2,
                    70  => 1,
                    76  => 1,
                    77  => 1,
                    78  => 7,
                    79  => 2,
                    81  => 2,
                    82  => 4,
                    87  => 1,
                    88  => 1,
                    92  => 1,
                    97  => 1,
                    100 => 1,
                    101 => 1,
                    102 => 1,
                    105 => 1,
                    106 => 1,
                    107 => 1,
                    125 => 1,
                    126 => 1,
                    141 => 1,
                    144 => 1,
                    146 => 1,
                    148 => 1,
                    151 => 1,
                    157 => 1,
                    174 => 3,
                    179 => 1,
                    190 => 1,
                    191 => 1,
                    192 => 1,
                    207 => 1,
                    210 => 1,
                    211 => 1,
                    215 => 1,
                    247 => 1,
                    286 => 1,
                    287 => 1,
                    288 => 1,
                    303 => 1,
                    309 => 1,
                    331 => 2,
                   );
        default:
            return array();
        }//end switch

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
