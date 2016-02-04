<?php
/**
 * Unit test class for the DisallowTabIndent sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DisallowTabIndentUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of CLI values to set before the file is tested.
     *
     * @param string                  $testFile The name of the file being tested.
     * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
     *
     * @return array
     */
    public function setCliValues($testFile, $config)
    {
        $config->tabWidth = 4;

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
    public function getErrorList($testFile='DisallowTabIndentUnitTest.inc')
    {
        switch ($testFile) {
        case 'DisallowTabIndentUnitTest.inc':
            return array(
                    5  => 2,
                    9  => 1,
                    15 => 1,
                    20 => 2,
                    21 => 1,
                    22 => 2,
                    23 => 1,
                    24 => 2,
                    31 => 1,
                    32 => 2,
                    33 => 2,
                    41 => 1,
                    42 => 1,
                    43 => 1,
                    44 => 1,
                    45 => 1,
                    46 => 1,
                    47 => 1,
                    48 => 1,
                   );
            break;
        case 'DisallowTabIndentUnitTest.js':
            return array(
                    3 => 1,
                    5 => 1,
                    6 => 1,
                   );
            break;
        case 'DisallowTabIndentUnitTest.css':
            return array(
                    1 => 1,
                    2 => 1,
                   );
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
