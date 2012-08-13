<?php
/**
 * A test class for testing the core.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'IsCamelCapsTest.php';
require_once 'ErrorSuppressionTest.php';
require_once 'File/GetMethodParametersTest.php';
require_once 'ReportingTest.php';
require_once 'Reports/CheckstyleTest.php';
require_once 'Reports/FullTest.php';
require_once 'Reports/SummaryTest.php';
require_once 'Reports/XmlTest.php';
require_once 'Reports/CsvTest.php';
require_once 'Reports/EmacsTest.php';
require_once 'Reports/SourceTest.php';
require_once 'Reports/SvnblameTest.php';
require_once 'Reports/GitblameTest.php';
require_once 'Reports/HgblameTest.php';

if (is_file(dirname(__FILE__).'/../../CodeSniffer.php') === true) {
    // We are not installed.
    include_once dirname(__FILE__).'/../../CodeSniffer.php';
} else {
    include_once 'PHP/CodeSniffer.php';
}

/**
 * A test class for testing the core.
 *
 * Do not run this file directly. Run the AllSniffs.php file in the root
 * testing directory of PHP_CodeSniffer.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Core_AllTests
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());

    }//end main()


    /**
     * Add all core unit tests into a test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP CodeSniffer Core');
        $suite->addTestSuite('Core_IsCamelCapsTest');
        $suite->addTestSuite('Core_ErrorSuppressionTest');
        $suite->addTestSuite('Core_File_GetMethodParametersTest');
        $suite->addTestSuite('Core_ReportingTest');
        $suite->addTestSuite('Core_Reports_CheckstyleTest');
        $suite->addTestSuite('Core_Reports_FullTest');
        $suite->addTestSuite('Core_Reports_SummaryTest');
        $suite->addTestSuite('Core_Reports_XmlTest');
        $suite->addTestSuite('Core_Reports_CsvTest');
        $suite->addTestSuite('Core_Reports_EmacsTest');
        $suite->addTestSuite('Core_Reports_SourceTest');
        $suite->addTestSuite('Core_Reports_SvnblameTest');
        $suite->addTestSuite('Core_Reports_GitblameTest');
        $suite->addTestSuite('Core_Reports_HgblameTest');
        return $suite;

    }//end suite()


}//end class

?>
