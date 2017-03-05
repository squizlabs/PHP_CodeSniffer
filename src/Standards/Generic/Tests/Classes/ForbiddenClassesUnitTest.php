<?php
/**
 * Unit test class for the ForbiddenClasses sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Classes;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ForbiddenClassesUnitTest extends AbstractSniffUnitTest
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

        return array(
                // PHPDoc property.
                8   => 1,
                9   => 1,
                10  => 1,
                11  => 1,
                12  => 1,

                // Implements.
                19  => 5,

                // Trait imports.
                22  => 5,

                // PHPDoc var.
                26  => 1,
                27  => 1,
                28  => 1,
                29  => 1,
                30  => 1,

                // PHPDoc multi-type and arrays.
                38  => 2,

                // New instance creation.
                43  => 1,
                44  => 1,
                45  => 1,
                46  => 1,
                47  => 1,

                // Static calls.
                49  => 1,
                50  => 1,
                51  => 1,
                52  => 1,
                53  => 1,

                // Type hints (closure).
                55  => 1,
                56  => 1,
                57  => 1,
                58  => 1,
                59  => 1,

                // Type hints (methods) and PHPDoc.
                78  => 1,
                81  => 1,
                83  => 1,

                87  => 1,
                90  => 1,
                92  => 1,

                96  => 1,
                99  => 1,
                101 => 1,

                105 => 1,
                108 => 1,
                110 => 1,

                114 => 1,
                117 => 1,
                119 => 1,

                // Extends.
                151 => 1,
                152 => 1,
                153 => 1,
                154 => 1,
                155 => 1,
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
