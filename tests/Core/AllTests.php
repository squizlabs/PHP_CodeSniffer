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

require_once dirname(dirname(__DIR__)).'/scripts/ValidatePEAR/FileList.php';

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

        $testFileIterator = (new \FileList(__DIR__, '', '`Test\.php$`Di'))->getList();
        foreach ($testFileIterator as $file) {
            if (strpos($file, 'AbstractMethodUnitTest.php') !== false) {
                continue;
            }

            include_once $file;

            $class = str_replace(__DIR__, '', $file);
            $class = str_replace('.php', '', $class);
            $class = str_replace('/', '\\', $class);
            $class = 'PHP_CodeSniffer\Tests\Core'.$class;

            $suite->addTestSuite($class);
        }

        return $suite;

    }//end suite()


}//end class
