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
     * Get a list of CLI values to set before the file is tested.
     *
     * @param string                  $testFile The name of the file being tested.
     * @param \PHP_CodeSniffer\Config $config   The config data for the test run.
     *
     * @return void
     */
    public function setCliValues($testFile, $config)
    {
        if ($testFile === 'DisallowSpaceIndentUnitTest.2.inc') {
            return;
        }

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
    public function getErrorList($testFile='DisallowSpaceIndentUnitTest.1.inc')
    {
        switch ($testFile) {
        case 'DisallowSpaceIndentUnitTest.1.inc':
        case 'DisallowSpaceIndentUnitTest.2.inc':
            return [
                5   => 1,
                9   => 1,
                15  => 1,
                22  => 1,
                24  => 1,
                30  => 1,
                35  => 1,
                50  => 1,
                55  => 1,
                57  => 1,
                58  => 1,
                59  => 1,
                60  => 1,
                65  => 1,
                66  => 1,
                67  => 1,
                68  => 1,
                69  => 1,
                70  => 1,
                73  => 1,
                77  => 1,
                81  => 1,
                104 => 1,
                105 => 1,
                106 => 1,
                107 => 1,
                108 => 1,
                110 => 1,
                111 => 1,
                112 => 1,
                114 => 1,
                115 => 1,
                117 => 1,
                118 => 1,
            ];
            break;
        case 'DisallowSpaceIndentUnitTest.js':
            return [3 => 1];
            break;
        case 'DisallowSpaceIndentUnitTest.css':
            return [2 => 1];
            break;
        default:
            return [];
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
        return [];

    }//end getWarningList()


}//end class
