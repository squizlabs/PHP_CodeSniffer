<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:isReference method.
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

class IsReferenceTest extends TestCase
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
     * Test a class that extends another.
     *
     * @param string $identifier Comment which preceeds the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataIsReference
     *
     * @return void
     */
    public function testIsReference($identifier, $expected)
    {
        $start      = ($this->phpcsFile->numTokens - 1);
        $delim      = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            $identifier
        );
        $bitwiseAnd = $this->phpcsFile->findNext(T_BITWISE_AND, ($delim + 1));

        $result = $this->phpcsFile->isReference($bitwiseAnd);
        $this->assertSame($expected, $result);

    }//end testIsReference()


    /**
     * Data provider for the IsReference test.
     *
     * @see testIsReference()
     *
     * @return array
     */
    public function dataIsReference()
    {
        return array(
                array(
                 '/* bitwiseAndA */',
                 false,
                ),
                array(
                 '/* bitwiseAndB */',
                 false,
                ),
                array(
                 '/* bitwiseAndC */',
                 false,
                ),
                array(
                 '/* bitwiseAndD */',
                 false,
                ),
                array(
                 '/* bitwiseAndE */',
                 false,
                ),
                array(
                 '/* bitwiseAndF */',
                 false,
                ),
                array(
                 '/* bitwiseAndG */',
                 false,
                ),
                array(
                 '/* bitwiseAndH */',
                 false,
                ),
                array(
                 '/* bitwiseAndI */',
                 false,
                ),
                array(
                 '/* functionReturnByReference */',
                 true,
                ),
                array(
                 '/* functionPassByReferenceA */',
                 true,
                ),
                array(
                 '/* functionPassByReferenceB */',
                 true,
                ),
                array(
                 '/* functionPassByReferenceC */',
                 true,
                ),
                array(
                 '/* functionPassByReferenceD */',
                 true,
                ),
                array(
                 '/* functionPassByReferenceE */',
                 true,
                ),
                array(
                 '/* functionPassByReferenceF */',
                 true,
                ),
                array(
                 '/* functionPassByReferenceG */',
                 true,
                ),
                array(
                 '/* foreachValueByReference */',
                 true,
                ),
                array(
                 '/* foreachKeyByReference */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceA */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceB */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceC */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceD */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceE */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceF */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceG */',
                 true,
                ),
                array(
                 '/* arrayValueByReferenceH */',
                 true,
                ),
                array(
                 '/* assignByReferenceA */',
                 true,
                ),
                array(
                 '/* assignByReferenceB */',
                 true,
                ),
                array(
                 '/* assignByReferenceC */',
                 true,
                ),
                array(
                 '/* assignByReferenceD */',
                 true,
                ),
                array(
                 '/* assignByReferenceE */',
                 true,
                ),
                array(
                 '/* passByReferenceA */',
                 true,
                ),
                array(
                 '/* passByReferenceB */',
                 true,
                ),
                array(
                 '/* passByReferenceC */',
                 true,
                ),
                array(
                 '/* passByReferenceD */',
                 true,
                ),
                array(
                 '/* passByReferenceE */',
                 true,
                ),
                array(
                 '/* passByReferenceF */',
                 true,
                ),
                array(
                 '/* passByReferenceG */',
                 true,
                ),
                array(
                 '/* passByReferenceH */',
                 true,
                ),
                array(
                 '/* passByReferenceI */',
                 true,
                ),
                array(
                 '/* passByReferenceJ */',
                 true,
                ),
                array(
                 '/* newByReferenceA */',
                 true,
                ),
                array(
                 '/* newByReferenceB */',
                 true,
                ),
                array(
                 '/* useByReference */',
                 true,
                ),
               );

    }//end dataIsReference()


}//end class
