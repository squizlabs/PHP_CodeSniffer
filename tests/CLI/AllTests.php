<?php
/**
 * A test class for testing the CLI execution.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\CLI;

use PHPUnit\Framework\TestSuite;

require_once 'Cat.php';
require_once 'SlowCat.php';
require_once 'Echo.php';

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
     * Add all CLI unit tests into a test suite.
     *
     * @return \PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new TestSuite('PHP CodeSniffer CLI');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\CLI\Cat');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\CLI\EchoCmd');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\CLI\FindXargs');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\CLI\SlowCat');
        return $suite;

    }//end suite()


}//end class
