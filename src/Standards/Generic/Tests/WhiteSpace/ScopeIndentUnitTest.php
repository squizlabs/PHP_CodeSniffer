<?php
/**
 * Unit test class for the ScopeIndent sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\WhiteSpace;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ScopeIndentUnitTest extends AbstractSniffUnitTest
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
        // Tab width setting is only needed for the tabbed file.
        if ($testFile === 'ScopeIndentUnitTest.2.inc') {
            $config->tabWidth = 4;
        } else {
            $config->tabWidth = 0;
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
    public function getErrorList($testFile='ScopeIndentUnitTest.inc')
    {
        if ($testFile === 'ScopeIndentUnitTest.1.js') {
            return [
                6   => 1,
                14  => 1,
                21  => 1,
                30  => 1,
                32  => 1,
                33  => 1,
                34  => 1,
                39  => 1,
                42  => 1,
                59  => 1,
                60  => 1,
                75  => 1,
                120 => 1,
                121 => 1,
                122 => 1,
                123 => 1,
                141 => 1,
                142 => 1,
                155 => 1,
                156 => 1,
                168 => 1,
                184 => 1,
            ];
        }//end if

        if ($testFile === 'ScopeIndentUnitTest.3.inc') {
            return [
                6  => 1,
                7  => 1,
                10 => 1,
            ];
        }

        return [
            7    => 1,
            10   => 1,
            13   => 1,
            17   => 1,
            20   => 1,
            24   => 1,
            25   => 1,
            27   => 1,
            28   => 1,
            29   => 1,
            30   => 1,
            58   => 1,
            123  => 1,
            224  => 1,
            225  => 1,
            279  => 1,
            280  => 1,
            281  => 1,
            282  => 1,
            283  => 1,
            284  => 1,
            285  => 1,
            286  => 1,
            336  => 1,
            349  => 1,
            380  => 1,
            386  => 1,
            387  => 1,
            388  => 1,
            389  => 1,
            390  => 1,
            397  => 1,
            419  => 1,
            420  => 1,
            465  => 1,
            467  => 1,
            472  => 1,
            473  => 1,
            474  => 1,
            496  => 1,
            498  => 1,
            500  => 1,
            524  => 1,
            526  => 1,
            544  => 1,
            545  => 1,
            546  => 1,
            639  => 1,
            660  => 1,
            662  => 1,
            802  => 1,
            803  => 1,
            823  => 1,
            858  => 1,
            879  => 1,
            1163 => 1,
            1197 => 1,
            1198 => 1,
            1259 => 1,
            1264 => 1,
            1265 => 1,
            1266 => 1,
            1269 => 1,
            1272 => 1,
            1273 => 1,
            1274 => 1,
            1275 => 1,
            1276 => 1,
            1277 => 1,
            1280 => 1,
            1281 => 1,
            1282 => 1,
            1284 => 1,
            1285 => 1,
            1288 => 1,
            1289 => 1,
            1290 => 1,
            1292 => 1,
            1293 => 1,
            1307 => 1,
            1308 => 1,
            1309 => 1,
            1310 => 1,
            1311 => 1,
            1312 => 1,
            1315 => 1,
            1320 => 1,
            1322 => 1,
            1325 => 1,
            1336 => 1,
            1337 => 1,
            1338 => 1,
            1339 => 1,
        ];

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
