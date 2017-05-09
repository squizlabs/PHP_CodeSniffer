<?php
/**
 * Unit test class for the ControlSignature sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Tests\ControlStructures;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ControlSignatureUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='ControlSignatureUnitTest.inc')
    {
        $errors = array(
                   7   => 1,
                   12  => 1,
                   15  => 1,
                   18  => 1,
                   20  => 1,
                   22  => 2,
                   28  => 2,
                   32  => 1,
                   38  => 2,
                   42  => 1,
                   48  => 2,
                   52  => 1,
                   62  => 2,
                   66  => 2,
                   76  => 4,
                   80  => 2,
                   94  => 1,
                   99  => 1,
                   108 => 1,
                   112 => 1,
                  );

        if ($testFile === 'ControlSignatureUnitTest.inc') {
            $errors[122] = 1;
            $errors[130] = 2;
            $errors[134] = 1;
            $errors[150] = 1;
            $errors[153] = 1;
            $errors[158] = 1;
            $errors[165] = 1;
            $errors[170] = 2;
            $errors[185] = 1;
            $errors[190] = 2;
            $errors[191] = 2;
            $errors[195] = 1;
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
        return array();

    }//end getWarningList()


}//end class
