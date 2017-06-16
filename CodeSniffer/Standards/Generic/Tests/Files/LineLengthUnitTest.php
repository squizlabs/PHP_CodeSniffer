<?php
/**
 * Unit test class for the LineLength sniff.
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
 * Unit test class for the LineLength sniff.
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
class Generic_Tests_Files_LineLengthUnitTest extends AbstractSniffUnitTest
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
        return array('--tab-width=4');

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
    public function getErrorList($testFile='')
    {
        switch ($testFile) {
        case 'LineLengthUnitTest.1.inc':
            return array(
                    31 => 1,
                    34 => 1,
                    45 => 1,
                   );
            break;
        case 'LineLengthUnitTest.2.inc':
        case 'LineLengthUnitTest.3.inc':
            return array(
                    7 => 1,
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
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getWarningList($testFile='')
    {
        switch ($testFile) {
        case 'LineLengthUnitTest.1.inc':
            return array(
                    9  => 1,
                    15 => 1,
                    21 => 1,
                    24 => 1,
                    29 => 1,
                    37 => 1,
                    63 => 1,
                    73 => 1,
                    75 => 1,
                   );
            break;
        case 'LineLengthUnitTest.2.inc':
        case 'LineLengthUnitTest.3.inc':
            return array(
                    6 => 1,
                   );
            break;
        case 'LineLengthUnitTest.4.inc':
            return array(
                    10 => 1,
                   );
            break;
        default:
            return array();
            break;
        }//end switch

    }//end getWarningList()


}//end class

?>
