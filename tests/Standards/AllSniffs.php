<?php
/**
 * A test class for testing all sniffs for installed standards.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Standards;

use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Util\Standards;
use PHP_CodeSniffer\Autoload;

if (defined('PHP_CODESNIFFER_IN_TESTS') === false) {
    define('PHP_CODESNIFFER_IN_TESTS', true);
}

if (defined('PHP_CODESNIFFER_CBF') === false) {
    define('PHP_CODESNIFFER_CBF', false);
}

if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
    define('PHP_CODESNIFFER_VERBOSITY', 0);
}

require_once __DIR__.'/../../vendor/autoload.php';

$tokens = new Tokens();

class AllSniffs
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());

    }//end main()


    /**
     * Add all sniff unit tests into a test suite.
     *
     * Sniff unit tests are found by recursing through the 'Tests' directory
     * of each installed coding standard.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']   = array();
        $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = array();

        $suite = new \PHPUnit_Framework_TestSuite('PHP CodeSniffer Standards');

        /*
            $isInstalled = !is_file(dirname(__FILE__).'/../../CodeSniffer.php');
        */

        $installedPaths = Standards::getInstalledStandardPaths();
        foreach ($installedPaths as $path) {
            $standards = Standards::getInstalledStandards(true, $path);

            /*
                // If the test is running PEAR installed, the built-in standards
                // are split into different directories; one for the sniffs and
                // a different file system location for tests.
                if ($isInstalled === true
                    && is_dir($path.DIRECTORY_SEPARATOR.'Generic') === true
                ) {
                    $path = dirname(__FILE__);
                }
            */

            foreach ($standards as $standard) {
                $standardDir = $path.DIRECTORY_SEPARATOR.$standard;
                $testsDir    = $standardDir.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR;

                if (is_dir($testsDir) === false) {
                    // Check if the installed path is actually a standard itself.
                    $standardDir = $path;
                    $testsDir    = $standardDir.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR;
                    if (is_dir($testsDir) === false) {
                        // No tests for this standard.
                        continue;
                    }
                } else {
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
                    $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'][$className] = $standardDir;
                    $suite->addTestSuite($className);
                }
            }//end foreach
        }//end foreach

        return $suite;

    }//end suite()


}//end class
