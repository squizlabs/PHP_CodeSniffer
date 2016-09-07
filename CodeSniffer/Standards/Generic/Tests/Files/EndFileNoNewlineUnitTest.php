<?php
/**
 * Unit test class for the EndFileNoNewline sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the EndFileNoNewline sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Tests_Files_EndFileNoNewlineUnitTest extends AbstractSniffUnitTest
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
            return array(3 => 1);
        case 'EndFileNoNewlineUnitTest.2.css':
        case 'EndFileNoNewlineUnitTest.2.js':
            return array(2 => 1);
        case 'EndFileNoNewlineUnitTest.5.inc':
            // HHVM just removes the entire comment token, as if it was never there.
            if (defined('HHVM_VERSION') === true) {
                return array(1 => 1);
            }

            return array();
        case 'EndFileNoNewlineUnitTest.6.inc':
            // HHVM just removes the entire comment token, as if it was never there.
            if (defined('HHVM_VERSION') === true) {
                return array(1 => 1);
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

?>
