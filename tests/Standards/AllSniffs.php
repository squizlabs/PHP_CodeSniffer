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
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

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
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_AllSniffs
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());

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
        $suite = new PHPUnit_Framework_TestSuite('PHP CodeSniffer Standards');

        $isInstalled = !is_file(dirname(__FILE__).'/../../CodeSniffer.php');

        if ($isInstalled === false) {
            // We have not been installed.
            $standardsDir = realpath(dirname(__FILE__).'/../../CodeSniffer/Standards');
        } else {
            $standardsDir = '';
        }

        $standards = PHP_CodeSniffer::getInstalledStandards(true, $standardsDir);

        foreach ($standards as $standard) {
            if ($isInstalled === false) {
                $standardDir = $standardsDir.DIRECTORY_SEPARATOR.$standard.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR;
            } else {
                $standardDir = dirname(__FILE__).DIRECTORY_SEPARATOR.$standard.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR;
            }

            if (is_dir($standardDir) === false) {
                // No tests for this standard.
                continue;
            }

            // Locate the actual directory that contains the standard's tests.
            // This is individual to each standard as they could be symlinked in.
            $baseDir = dirname(dirname($standardDir));

            $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($standardDir));

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

                $filePath  = $file->getPathname();
                $className = str_replace($baseDir.DIRECTORY_SEPARATOR, '', $filePath);
                $className = substr($className, 0, -4);
                $className = str_replace(DIRECTORY_SEPARATOR, '_', $className);

                include_once $filePath;
                $class = new $className('getErrorList');
                $suite->addTest($class);
            }//end foreach
        }//end foreach

        return $suite;

    }//end suite()


}//end class

?>
