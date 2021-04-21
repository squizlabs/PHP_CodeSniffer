<?php
/**
 * Unit test class for the EndFileNoNewline sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class EndFileNoNewlineUnitTest extends AbstractSniffUnitTest
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
        case 'EndFileNoNewlineUnitTest.1.inc':
        case 'EndFileNoNewlineUnitTest.1.css':
        case 'EndFileNoNewlineUnitTest.1.js':
        case 'EndFileNoNewlineUnitTest.2.inc':
            return [3 => 1];
        case 'EndFileNoNewlineUnitTest.2.css':
        case 'EndFileNoNewlineUnitTest.2.js':
        case 'EndFileNoNewlineUnitTest.6.inc':
            return [2 => 1];
        case 'EndFileNoNewlineUnitTest.8.inc':
        case 'EndFileNoNewlineUnitTest.9.inc':
            return [1 => 1];
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
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        return [];

    }//end getWarningList()


}//end class
