<?php
/**
 * Unit test class for the ExecutableFile sniff.
 *
 * @author    Matthew Peveler <matt.peveler@gmail.com>
 * @copyright 2019 Matthew Peveler
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ExecutableFileUnitTest extends AbstractSniffUnitTest
{


    /**
     * Should this test be skipped for some reason.
     *
     * @return void
     */
    protected function shouldSkipTest()
    {
        // PEAR doesn't preserve the executable flag, so skip
        // tests when running in a PEAR install.
        return $GLOBALS['PHP_CODESNIFFER_PEAR'];

    }//end shouldSkipTest()


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
        case 'ExecutableFileUnitTest.2.inc':
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
