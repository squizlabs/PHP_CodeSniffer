<?php
/**
 * A test class for testing all sniffs for installed standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer.php';
require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

// Require this here so that the unit tests don't have to try and find the
// abstract class once it is installed into the PEAR tests directory.
require_once dirname(__FILE__).'/AbstractSniffUnitTest.php';

/**
 * A test class for testing all sniffs for installed standards.
 *
 * Usage: phpunit AllSniffs.php
 *
 * This test class loads all unit tests for all installed standards into a
 * single test suite and runs them. Errors are reported on the command line.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class AllSniffs
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
     * Add all sniff unit tests into a test suite.
     *
     * Sniff unit tests are found by recursing through the 'Tests' directory
     * of each installed coding standard.
     *
     * @return PHPUnit2_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('PHP CodeSniffer Standards');

        $standards = PHP_CodeSniffer::getInstalledStandards(true);
        foreach ($standards as $standard) {
            $standardDir = dirname(__FILE__).'/'.$standard.'/Tests/';
            $di          = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($standardDir));

            foreach ($di as $file) {
                // Tests must have the extention 'php'.
                $parts = explode('.', $file);
                $ext   = array_pop($parts);
                if ($ext !== 'php') {
                    continue;
                }

                $filePath  = $file->getPathname();
                $className = str_replace(dirname(__FILE__).'/', '', $filePath);
                $className = substr($className, 0, -4);
                $className = str_replace('/', '_', $className);

                include_once $file->getPathname();
                $suite->addTestSuite($className);
            }
        }

        return $suite;

    }//end suite()


}//end class

?>
