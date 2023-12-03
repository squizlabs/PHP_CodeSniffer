<?php
/**
 * Unit test class for the FunctionComment sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FunctionCommentUnitTest extends AbstractSniffUnitTest
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
        $errors = [
            5    => 1,
            10   => 3,
            12   => 2,
            13   => 2,
            14   => 1,
            15   => 1,
            28   => 1,
            43   => 1,
            76   => 1,
            87   => 1,
            103  => 1,
            109  => 1,
            112  => 1,
            122  => 1,
            123  => 3,
            124  => 2,
            125  => 1,
            126  => 1,
            137  => 4,
            138  => 4,
            139  => 4,
            143  => 2,
            155  => 1,
            159  => 1,
            166  => 1,
            173  => 1,
            183  => 1,
            190  => 2,
            193  => 2,
            196  => 1,
            199  => 2,
            210  => 1,
            211  => 1,
            222  => 1,
            223  => 1,
            224  => 1,
            225  => 1,
            226  => 1,
            227  => 1,
            230  => 2,
            232  => 2,
            246  => 1,
            248  => 4,
            261  => 1,
            263  => 1,
            276  => 1,
            277  => 1,
            278  => 1,
            279  => 1,
            280  => 1,
            281  => 1,
            284  => 1,
            286  => 7,
            294  => 1,
            302  => 1,
            312  => 1,
            358  => 1,
            359  => 2,
            372  => 1,
            373  => 1,
            387  => 1,
            407  => 1,
            441  => 1,
            500  => 1,
            526  => 1,
            548  => 1,
            641  => 1,
            669  => 1,
            688  => 1,
            744  => 1,
            748  => 1,
            767  => 1,
            789  => 1,
            792  => 1,
            794  => 1,
            797  => 1,
            801  => 1,
            828  => 1,
            840  => 1,
            852  => 1,
            864  => 1,
            886  => 1,
            888  => 1,
            890  => 1,
            978  => 1,
            997  => 1,
            1004 => 2,
            1006 => 1,
            1029 => 1,
            1053 => 1,
            1058 => 2,
            1069 => 1,
            1070 => 1,
            1071 => 1,
            1080 => 2,
            1083 => 1,
            1084 => 1,
            1085 => 1,
            1093 => 4,
            1100 => 1,
            1101 => 1,
            1102 => 1,
            1103 => 1,
            1123 => 1,
            1124 => 1,
            1125 => 1,
        ];

        // Scalar type hints only work from PHP 7 onwards.
        if (PHP_VERSION_ID >= 70000) {
            $errors[17]   = 3;
            $errors[128]  = 1;
            $errors[143]  = 3;
            $errors[161]  = 2;
            $errors[201]  = 1;
            $errors[232]  = 7;
            $errors[363]  = 3;
            $errors[377]  = 1;
            $errors[575]  = 2;
            $errors[627]  = 1;
            $errors[1002] = 1;
            $errors[1075] = 6;
            $errors[1089] = 3;
            $errors[1107] = 8;
            $errors[1129] = 3;
        } else {
            $errors[729] = 4;
            $errors[740] = 2;
            $errors[752] = 2;
            $errors[982] = 1;
        }//end if

        // Object type hints only work from PHP 7.2 onwards.
        if (PHP_VERSION_ID >= 70200) {
            $errors[627] = 2;
        } else {
            $errors[992] = 2;
        }

        // Mixed type hints only work from PHP 8.0 onwards.
        if (PHP_VERSION_ID >= 80000) {
            $errors[265] = 1;
            $errors[459] = 1;
            $errors[893] = 3;
        } else {
            $errors[1023] = 1;
        }

        return $errors;

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
