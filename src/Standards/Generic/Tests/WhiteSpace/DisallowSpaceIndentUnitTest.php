<?php
/**
 * Unit test class for the DisallowSpaceIndent sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DisallowSpaceIndentUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='DisallowSpaceIndentUnitTest.inc')
    {
        switch ($testFile) {
        case 'DisallowSpaceIndentUnitTest.inc':
            return array(
                    5  => 1,
                    9  => 1,
                    15 => 1,
                    22 => 1,
                    24 => 1,
                    30 => 1,
                    35 => 1,
                    50 => 1,
                    55 => 1,
                    57 => 1,
                    58 => 1,
                    59 => 1,
                    60 => 1,
                    65 => 1,
                    66 => 1,
                    67 => 1,
                    68 => 1,
                    69 => 1,
                    70 => 1,
                    73 => 1,
                    77 => 1,
                    81 => 1,
                   );
            break;
        case 'DisallowSpaceIndentUnitTest.js':
            return array(3 => 1);
            break;
        case 'DisallowSpaceIndentUnitTest.css':
            return array(2 => 1);
            break;
        default:
            return array();
            break;
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
