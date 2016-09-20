<?php
/**
 * A PHP_CodeSniffer specific test suite for PHPUnit.
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
 * A PHP_CodeSniffer specific test suite for PHPUnit.
 *
 * Unregisters the PHP_CodeSniffer autoload function after the run.
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
class PHP_CodeSniffer_TestSuite extends PHPUnit_Framework_TestSuite
{


    /**
     * Runs the tests and collects their result in a TestResult.
     *
     * @param PHPUnit_Framework_TestResult $result A test result.
     * @param mixed                        $filter The filter passed to each test.
     *
     * @return PHPUnit_Framework_TestResult
     */
    public function run(PHPUnit_Framework_TestResult $result=null, $filter=false)
    {
        // Report on any test files that had no corresponding sniff file.
        if (empty(PHP_CodeSniffer_Standards_AllSniffs::$orphanedTests) === false) {
            echo PHP_EOL;
            echo 'IMPORTANT: No corresponding sniff files were found for the following test files:'.PHP_EOL.PHP_EOL;
            foreach (PHP_CodeSniffer_Standards_AllSniffs::$orphanedTests as $path) {
                echo "- $path".PHP_EOL;
            }

            echo PHP_EOL.PHP_EOL;
        }

        $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']   = array();
        $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = array();

        spl_autoload_register(array('PHP_CodeSniffer', 'autoload'));
        $result = parent::run($result, $filter);
        spl_autoload_unregister(array('PHP_CodeSniffer', 'autoload'));

        $codes = count($GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']);

        echo PHP_EOL.PHP_EOL;
        echo "Tests generated $codes unique error codes";
        if ($codes > 0) {
            $fixes = count($GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES']);
            $percent = round(($fixes / $codes * 100), 2);
            echo "; $fixes were fixable ($percent%)";
        }

        return $result;

    }//end run()


}//end class
