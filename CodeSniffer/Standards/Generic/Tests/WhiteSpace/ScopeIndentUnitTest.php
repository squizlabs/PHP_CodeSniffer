<?php
/**
 * Unit test class for the ScopeIndent sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the ScopeIndent sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Tests_WhiteSpace_ScopeIndentUnitTest extends AbstractSniffUnitTest
{


    /**
     * Get a list of CLI values to set before the file is tested.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array
     */
    public function getCliValues($testFile)
    {
        // Tab width setting is only needed for the tabbed file.
        if ($testFile === 'ScopeIndentUnitTest.2.inc') {
            return array('--tab-width=4');
        }

        return array();

    }//end getCliValues()


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
            return array(
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
                   );
        }

        if ($testFile === 'ScopeIndentUnitTest.3.inc') {
            return array(
                    6  => 1,
                    7  => 1,
                    10 => 1,
                   );
        }

        return array(
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
                1185 => 1,
                1190 => 1,
                1192 => 1,
                1195 => 1,
                1199 => 1,
                1200 => 1,
                1201 => 1,
                1202 => 1,
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
