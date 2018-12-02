<?php
/**
 * A test class for testing all sniffs for installed standards.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Standards;

use PHP_CodeSniffer\Util\Standards;
use PHP_CodeSniffer\Autoload;
use PHPUnit\TextUI\TestRunner;
use PHPUnit\Framework\TestSuite;

class AllSniffs
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        TestRunner::run(self::suite());

    }//end main()


    /**
     * Add all sniff unit tests into a test suite.
     *
     * Sniff unit tests are found by recursing through the 'Tests' directory
     * of each installed coding standard.
     *
     * @return \PHPUnit\Framework\TestSuite
     */
    public static function suite()
    {
        $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']      = [];
        $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES']    = [];
        $GLOBALS['PHP_CODESNIFFER_SNIFF_CASE_FILES'] = [];

        $suite = new TestSuite('PHP CodeSniffer Standards');

        $isInstalled = !is_file(__DIR__.'/../../autoload.php');

        // Optionally allow for ignoring the tests for one or more standards.
        $ignoreTestsForStandards = getenv('PHPCS_IGNORE_TESTS');
        if ($ignoreTestsForStandards === false) {
            $ignoreTestsForStandards = [];
        } else {
            $ignoreTestsForStandards = explode(',', $ignoreTestsForStandards);
        }

        $installedStandards = self::getInstalledStandardDetails();

        foreach ($installedStandards as $standard => $details) {
            Autoload::addSearchPath($details['path'], $details['namespace']);

            // If the test is running PEAR installed, the built-in standards
            // are split into different directories; one for the sniffs and
            // a different file system location for tests.
            if ($isInstalled === true && is_dir(dirname($details['path']).DIRECTORY_SEPARATOR.'Generic') === true) {
                $testPath = realpath(__DIR__.'/../../src/Standards/'.$standard);
            } else {
                $testPath = $details['path'];
            }

            if (in_array($standard, $ignoreTestsForStandards) === true) {
                continue;
            }

            $testsDir = $testPath.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR;
            if (is_dir($testsDir) === false) {
                // No tests for this standard.
                continue;
            }

            $di = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($testsDir));

            foreach ($di as $file) {
                // Skip hidden files.
                if (substr($file->getFilename(), 0, 1) === '.') {
                    continue;
                }

                // Tests must have the extension 'php'.
                $parts = explode('.', $file);
                $ext   = array_pop($parts);
                if ($ext !== 'php') {
                    continue;
                }

                $className = Autoload::loadFile($file->getPathname());
                $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'][$className] = $details['path'];
                $GLOBALS['PHP_CODESNIFFER_TEST_DIRS'][$className]     = $testsDir;
                $suite->addTestSuite($className);
            }
        }//end foreach

        return $suite;

    }//end suite()


    /**
     * Get the details of all coding standards installed.
     *
     * @return array
     * @see    Standards::getInstalledStandardDetails()
     */
    protected static function getInstalledStandardDetails()
    {
        return Standards::getInstalledStandardDetails(true);

    }//end getInstalledStandardDetails()


}//end class
