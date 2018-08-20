<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findExtendedInterfaceNames method.
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

class FindExtendedInterfaceNamesTest extends TestCase
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
     * Test retrieving the names of the interfaces being extended by another interface.
     *
     * @param string $identifier Comment which preceeds the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataExtendedInterface
     *
     * @return void
     */
    public function testFindExtendedInterfaceNames($identifier, $expected)
    {
        $start     = ($this->phpcsFile->numTokens - 1);
        $delim     = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            $identifier
        );
        $interface = $this->phpcsFile->findNext(T_INTERFACE, ($delim + 1));

        $result = $this->phpcsFile->findExtendedInterfaceNames($interface);
        $this->assertSame($expected, $result);

    }//end testFindExtendedInterfaceNames()


    /**
     * Data provider for the FindExtendedInterfaceNames test.
     *
     * @see testFindExtendedInterfaceNames()
     *
     * @return array
     */
    public function dataExtendedInterface()
    {
        return [
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testExtendedInterface */',
                ['testFEINInterface'],
            ],
            [
                '/* testMultiExtendedInterface */',
                [
                    'testFEINInterface',
                    'testFEINInterface2',
                ],
            ],
            [
                '/* testNamespacedInterface */',
                ['\PHP_CodeSniffer\Tests\Core\File\testFEINInterface'],
            ],
            [
                '/* testMultiNamespacedInterface */',
                [
                    '\PHP_CodeSniffer\Tests\Core\File\testFEINInterface',
                    '\PHP_CodeSniffer\Tests\Core\File\testFEINInterface2',
                ],
            ],
            [
                '/* testMultiExtendedInterfaceWithComment */',
                [
                    'testFEINInterface',
                    '\PHP_CodeSniffer\Tests\Core\File\testFEINInterface2',
                    '\testFEINInterface3',
                ],
            ],
        ];

    }//end dataExtendedInterface()


}//end class
