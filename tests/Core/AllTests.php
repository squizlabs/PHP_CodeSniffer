<?php
/**
 * A test class for testing the core.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHPUnit\TextUI\TestRunner;
use PHPUnit\Framework\TestSuite;

require_once 'IsCamelCapsTest.php';
require_once 'ErrorSuppressionTest.php';
require_once 'File/FindEndOfStatementTest.php';
require_once 'File/FindExtendedClassNameTest.php';
require_once 'File/FindImplementedInterfaceNamesTest.php';
require_once 'File/GetMemberPropertiesTest.php';
require_once 'File/GetMethodParametersTest.php';
require_once 'File/GetMethodPropertiesTest.php';
require_once 'File/IsReferenceTest.php';

class AllTests
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
     * Add all core unit tests into a test suite.
     *
     * @return \PHPUnit\Framework\TestSuite
     */
    public static function suite()
    {
        $suite = new TestSuite('PHP CodeSniffer Core');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\IsCamelCapsTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\ErrorSuppressionTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\FindEndOfStatementTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\FindExtendedClassNameTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\FindImplementedInterfaceNamesTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\GetMemberPropertiesTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\GetMethodParametersTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\GetMethodPropertiesTest');
        $suite->addTestSuite('PHP_CodeSniffer\Tests\Core\File\IsReferenceTest');
        return $suite;

    }//end suite()


}//end class
