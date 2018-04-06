<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findExtendedClassName method.
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

class FindExtendedClassNameTest extends TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of the test case file.
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
        $config->standards = ['Generic'];

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
     * Test a class that extends another.
     *
     * @return void
     */
    public function testExtendedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testExtendedClass */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertSame('testFECNClass', $found);

    }//end testExtendedClass()


    /**
     * Test a class that extends another, using namespaces.
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

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertSame('\PHP_CodeSniffer\Tests\Core\File\testFECNClass', $found);

    }//end testNamespacedClass()


    /**
     * Test a class that doesn't extend another.
     *
     * @return void
     */
    public function testNonExtendedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNonExtendedClass */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertFalse($found);

    }//end testNonExtendedClass()


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

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertFalse($found);

    }//end testInterface()


    /**
     * Test an interface that extends another.
     *
     * @return void
     */
    public function testExtendedInterface()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testInterfaceThatExtendsInterface */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertSame('testFECNInterface', $found);

    }//end testExtendedInterface()


    /**
     * Test an interface that extends another, using namespaces.
     *
     * @return void
     */
    public function testExtendedNamespacedInterface()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testInterfaceThatExtendsFQCNInterface */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertSame('\PHP_CodeSniffer\Tests\Core\File\testFECNInterface', $found);

    }//end testExtendedNamespacedInterface()


}//end class
