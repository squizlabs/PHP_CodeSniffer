<?php

namespace PHP_CodeSniffer\Tests;

use PHP_CodeSniffer\Util\Tokens;

/**
 * A test class for running all PHP_CodeSniffer unit tests.
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

if (defined('PHP_CODESNIFFER_IN_TESTS') === false) {
    define('PHP_CODESNIFFER_IN_TESTS', true);
}

if (defined('PHP_CODESNIFFER_CBF') === false) {
    define('PHP_CODESNIFFER_CBF', false);
}

if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
    define('PHP_CODESNIFFER_VERBOSITY', 0);
}

require_once __DIR__.'/../vendor/autoload.php';

$tokens = new Tokens();

require_once 'Core/AllTests.php';
require_once 'Standards/AllSniffs.php';

/**
 * A test class for running all PHP_CodeSniffer unit tests.
 *
 * Usage: phpunit AllTests.php
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
class PHP_CodeSniffer_AllTests
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
     * Add all PHP_CodeSniffer test suites into a single test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'] = array();

        // Use a special PHP_CodeSniffer test suite so that we can
        // unset our autoload function after the run.
        $suite = new TestSuite('PHP CodeSniffer');

        $suite->addTest(Core\AllTests::suite());
        $suite->addTest(Standards\AllSniffs::suite());

        // Unregister this here because the PEAR tester loads
        // all package suites before running then, so our autoloader
        // will cause problems for the packages included after us.
        #spl_autoload_unregister(array('PHP_CodeSniffer', 'autoload'));

        return $suite;

    }//end suite()


}//end class
