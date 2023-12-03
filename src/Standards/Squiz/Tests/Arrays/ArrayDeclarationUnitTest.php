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
            return [
                2   => 1,
                8   => 2,
                10  => 2,
                22  => 1,
                23  => 2,
                24  => 2,
                25  => 1,
                31  => 2,
                35  => 1,
                36  => 2,
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
                173 => 1,
                174 => 3,
                179 => 1,
                182 => 1,
                188 => 1,
                207 => 1,
                212 => 2,
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
                355 => 3,
                358 => 3,
                359 => 2,
                360 => 1,
                362 => 1,
                363 => 2,
                364 => 1,
                365 => 2,
                366 => 2,
                367 => 2,
                368 => 2,
                369 => 1,
                370 => 1,
                383 => 1,
                394 => 1,
                400 => 1,
                406 => 1,
                441 => 1,
                444 => 2,
                445 => 2,
                447 => 2,
                448 => 3,
                467 => 1,
                471 => 1,
                472 => 1,
                510 => 1,
                516 => 1,
                523 => 1,
                530 => 1,
            ];
        case 'ArrayDeclarationUnitTest.2.inc':
            return [
                2   => 1,
                10  => 1,
                23  => 2,
                24  => 2,
                25  => 1,
                31  => 2,
                36  => 2,
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
                173 => 1,
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
                345 => 3,
                348 => 3,
                349 => 2,
                350 => 1,
                352 => 2,
                353 => 2,
                354 => 2,
                355 => 2,
                356 => 2,
                357 => 1,
                358 => 1,
                372 => 1,
                383 => 1,
                389 => 1,
                395 => 1,
                430 => 1,
                433 => 2,
                434 => 2,
                436 => 2,
                437 => 3,
                456 => 1,
                460 => 1,
                461 => 1,
                499 => 1,
                505 => 1,
                512 => 1,
                519 => 1,
            ];
        default:
            return [];
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
        return [];

    }//end getWarningList()


}//end class
