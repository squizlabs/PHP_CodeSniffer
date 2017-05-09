<?php
/**
 * Unit test class for the EndFileNewline sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class EndFileNewlineUnitTest extends AbstractSniffUnitTest
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
        case 'EndFileNewlineUnitTest.1.inc':
        case 'EndFileNewlineUnitTest.3.inc':
        case 'EndFileNewlineUnitTest.6.inc':
        case 'EndFileNewlineUnitTest.7.inc':
            return array(2 => 1);
        case 'EndFileNewlineUnitTest.9.inc':
        case 'EndFileNewlineUnitTest.10.inc':
            // HHVM just removes the entire comment token, as if it was never there.
            if (defined('HHVM_VERSION') === true) {
                return array();
            }
            return array(2 => 1);
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
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        return array();

    }//end getWarningList()


}//end class
