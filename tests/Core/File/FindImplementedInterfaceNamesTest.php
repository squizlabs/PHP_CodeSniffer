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
     * Test retrieving the name(s) of the interfaces being implemented by a class.
     *
     * @param string $identifier Comment which preceeds the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataImplementedInterface
     *
     * @return void
     */
    public function testFindImplementedInterfaceNames($identifier, $expected)
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

        $result = $this->phpcsFile->findImplementedInterfaceNames($OOToken);
        $this->assertSame($expected, $result);

    }//end testFindImplementedInterfaceNames()


    /**
     * Data provider for the FindImplementedInterfaceNames test.
     *
     * @see testFindImplementedInterfaceNames()
     *
     * @return array
     */
    public function dataImplementedInterface()
    {
        return [
            [
                '/* testImplementedClass */',
                ['testFIINInterface'],
            ],
            [
                '/* testMultiImplementedClass */',
                [
                    'testFIINInterface',
                    'testFIINInterface2',
                ],
            ],
            [
                '/* testNamespacedClass */',
                ['\PHP_CodeSniffer\Tests\Core\File\testFIINInterface'],
            ],
            [
                '/* testNonImplementedClass */',
                false,
            ],
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testClassThatExtendsAndImplements */',
                [
                    'InterfaceA',
                    '\NameSpaced\Cat\InterfaceB',
                ],
            ],
            [
                '/* testClassThatImplementsAndExtends */',
                [
                    '\InterfaceA',
                    'InterfaceB',
                ],
            ],
        ];

    }//end dataImplementedInterface()


}//end class
