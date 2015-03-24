<?php
/**
 * Unit test class for the ComparisonOperatorUsage sniff.
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
 * Unit test class for the ComparisonOperatorUsage sniff.
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
class Squiz_Tests_Operators_ComparisonOperatorUsageUnitTest extends AbstractSniffUnitTest
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
    public function getErrorList($testFile='ComparisonOperatorUsageUnitTest.inc')
    {
        switch ($testFile) {
        case 'ComparisonOperatorUsageUnitTest.inc':
            return array(
                    6  => 1,
                    7  => 1,
                    10 => 1,
                    11 => 1,
                    18 => 1,
                    19 => 1,
                    22 => 1,
                    23 => 1,
                    29 => 2,
                    32 => 2,
                    38 => 4,
                    47 => 2,
                    69 => 1,
                    72 => 1,
                    75 => 1,
                    78 => 1,
                    80 => 1,
                    82 => 1,
                    83 => 1,
                    89 => 1,
                   );
            break;
        case 'ComparisonOperatorUsageUnitTest.js':
            return array(
                    5  => 1,
                    6  => 1,
                    17 => 1,
                    18 => 1,
                    28 => 2,
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

?>
