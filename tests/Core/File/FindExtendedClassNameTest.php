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
     * Test retrieving the name of the class being extended by another class
     * (or interface).
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataExtendedClass
     *
     * @return void
     */
    public function testFindExtendedClassName($identifier, $expected)
    {
        $start   = ($this->phpcsFile->numTokens - 1);
        $delim   = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            $identifier
        );
        $OOToken = $this->phpcsFile->findNext([T_CLASS, T_ANON_CLASS, T_INTERFACE], ($delim + 1));

        $result = $this->phpcsFile->findExtendedClassName($OOToken);
        $this->assertSame($expected, $result);

    }//end testFindExtendedClassName()


    /**
     * Data provider for the FindExtendedClassName test.
     *
     * @see testFindExtendedClassName()
     *
     * @return array
     */
    public function dataExtendedClass()
    {
        return [
            [
                '/* testExtendedClass */',
                'testFECNClass',
            ],
            [
                '/* testNamespacedClass */',
                '\PHP_CodeSniffer\Tests\Core\File\testFECNClass',
            ],
            [
                '/* testNonExtendedClass */',
                false,
            ],
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testInterfaceThatExtendsInterface */',
                'testFECNInterface',
            ],
            [
                '/* testInterfaceThatExtendsFQCNInterface */',
                '\PHP_CodeSniffer\Tests\Core\File\testFECNInterface',
            ],
            [
                '/* testNestedExtendedClass */',
                false,
            ],
            [
                '/* testNestedExtendedAnonClass */',
                'testFECNAnonClass',
            ],
            [
                '/* testClassThatExtendsAndImplements */',
                'testFECNClass',
            ],
            [
                '/* testClassThatImplementsAndExtends */',
                'testFECNClass',
            ],
        ];

    }//end dataExtendedClass()


}//end class
