<?php
/**
 * Unit test class for the SideEffects sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR1\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class SideEffectsUnitTest extends AbstractSniffUnitTest
{


    /**
     * Set CLI values before the file is tested.
     *
     * @param string                  $testFile The name of the file being tested.
     * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
     *
     * @return void
     */
    public function setCliValues($testFile, $config)
    {
        if ($testFile === 'SideEffectsUnitTest.12.inc') {
            $config->annotations = false;
        }

    }//end setCliValues()


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
        case 'SideEffectsUnitTest.3.inc':
        case 'SideEffectsUnitTest.4.inc':
        case 'SideEffectsUnitTest.5.inc':
        case 'SideEffectsUnitTest.10.inc':
        case 'SideEffectsUnitTest.12.inc':
        case 'SideEffectsUnitTest.15.inc':
        case 'SideEffectsUnitTest.16.inc':
            return [1 => 1];
        default:
            return [];
        }//end switch

    }//end getWarningList()


}//end class
