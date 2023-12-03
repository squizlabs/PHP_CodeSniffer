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
        return [];

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'NonExecutableCodeUnitTest.1.inc':
            return [
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
                240 => 1,
                246 => 1,
                252 => 1,
                253 => 1,
                254 => 2,
                303 => 1,
                308 => 1,
                370 => 1,
                376 => 1,
                381 => 1,
                386 => 1,
                391 => 1,
                396 => 1,
            ];
            break;
        case 'NonExecutableCodeUnitTest.2.inc':
            return [
                7  => 1,
                8  => 1,
                9  => 1,
                10 => 2,
                14 => 1,
                54 => 2,
                65 => 2,
                69 => 2,
                70 => 2,
                71 => 2,
            ];
            break;
        case 'NonExecutableCodeUnitTest.3.inc':
            return [
                27 => 1,
                36 => 1,
                45 => 1,
                54 => 1,
                62 => 1,
            ];
        default:
            return [];
            break;
        }//end switch

    }//end getWarningList()


}//end class
