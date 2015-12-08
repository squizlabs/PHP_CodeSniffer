<?php
/**
 * A test class for testing the core.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Util\Tokens;

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

require_once 'IsCamelCapsTest.php';
require_once 'ErrorSuppressionTest.php';
require_once 'File/GetMethodParametersTest.php';

class AllTests
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        \PHPUnit2_TextUI_TestRunner::run(self::suite());

    }//end main()


    /**
     * Add all core unit tests into a test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('PHP CodeSniffer Core');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\IsCamelCapsTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\ErrorSuppressionTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\GetMethodParametersTest');
        return $suite;

    }//end suite()


}//end class
