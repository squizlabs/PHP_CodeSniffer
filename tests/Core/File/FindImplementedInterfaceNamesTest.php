<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findImplementedInterfaceNames method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHPUnit\Framework\TestCase;

class FindImplementedInterfaceNamesTest extends TestCase
{

    /**
     * The \PHP_CodeSniffer\Files\File object containing parsed contents of the test case file.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    private $phpcsFile;


    /**
     * Initialize & tokenize \PHP_CodeSniffer\Files\File with code from the test case file.
     *
     * Methods used for these tests can be found in a test case file in the same
     * directory and with the same name, using the .inc extension.
     *
     * @return void
     */
    public function setUp()
    {
        $config            = new Config();
        $config->standards = array('Generic');

        $ruleset = new Ruleset($config);

        $pathToTestFile  = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.inc';
        $this->phpcsFile = new DummyFile(file_get_contents($pathToTestFile), $ruleset, $config);
        $this->phpcsFile->process();

    }//end setUp()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->phpcsFile);

    }//end tearDown()


    /**
     * Test a class that implements a single interface.
     *
     * @return void
     */
    public function testImplementedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testImplementedClass */'
        );

        $found = $this->phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertSame(array('testFIINInterface'), $found);

    }//end testImplementedClass()


    /**
     * Test a class that implements multiple interfaces.
     *
     * @return void
     */
    public function testMultiImplementedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testMultiImplementedClass */'
        );

        $found = $this->phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertSame(array('testFIINInterface', 'testFIINInterface2'), $found);

    }//end testMultiImplementedClass()


    /**
     * Test a class that implements an interface, using namespaces.
     *
     * @return void
     */
    public function testNamespacedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNamespacedClass */'
        );

        $found = $this->phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertSame(array('\PHP_CodeSniffer\Tests\Core\File\testFIINInterface'), $found);

    }//end testNamespacedClass()


    /**
     * Test a class that doesn't implement an interface.
     *
     * @return void
     */
    public function testNonImplementedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNonImplementedClass */'
        );

        $found = $this->phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertFalse($found);

    }//end testNonImplementedClass()


    /**
     * Test an interface.
     *
     * @return void
     */
    public function testInterface()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testInterface */'
        );

        $found = $this->phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertFalse($found);

    }//end testInterface()


}//end class
