<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:isReference method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class IsReferenceTest extends AbstractMethodUnitTest
{


    /**
     * Test correctly identifying whether a "bitwise and" token is a reference or not.
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataIsReference
     *
     * @return void
     */
    public function testIsReference($identifier, $expected)
    {
        $bitwiseAnd = $this->getTargetToken($identifier, T_BITWISE_AND);
        $result     = self::$phpcsFile->isReference($bitwiseAnd);
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
        return [
            [
                '/* testBitwiseAndA */',
                false,
            ],
            [
                '/* testBitwiseAndB */',
                false,
            ],
            [
                '/* testBitwiseAndC */',
                false,
            ],
            [
                '/* testBitwiseAndD */',
                false,
            ],
            [
                '/* testBitwiseAndE */',
                false,
            ],
            [
                '/* testBitwiseAndF */',
                false,
            ],
            [
                '/* testBitwiseAndG */',
                false,
            ],
            [
                '/* testBitwiseAndH */',
                false,
            ],
            [
                '/* testBitwiseAndI */',
                false,
            ],
            [
                '/* testFunctionReturnByReference */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceA */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceB */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceC */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceD */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceE */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceF */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceG */',
                true,
            ],
            [
                '/* testForeachValueByReference */',
                true,
            ],
            [
                '/* testForeachKeyByReference */',
                true,
            ],
            [
                '/* testArrayValueByReferenceA */',
                true,
            ],
            [
                '/* testArrayValueByReferenceB */',
                true,
            ],
            [
                '/* testArrayValueByReferenceC */',
                true,
            ],
            [
                '/* testArrayValueByReferenceD */',
                true,
            ],
            [
                '/* testArrayValueByReferenceE */',
                true,
            ],
            [
                '/* testArrayValueByReferenceF */',
                true,
            ],
            [
                '/* testArrayValueByReferenceG */',
                true,
            ],
            [
                '/* testArrayValueByReferenceH */',
                true,
            ],
            [
                '/* testAssignByReferenceA */',
                true,
            ],
            [
                '/* testAssignByReferenceB */',
                true,
            ],
            [
                '/* testAssignByReferenceC */',
                true,
            ],
            [
                '/* testAssignByReferenceD */',
                true,
            ],
            [
                '/* testAssignByReferenceE */',
                true,
            ],
            [
                '/* testPassByReferenceA */',
                true,
            ],
            [
                '/* testPassByReferenceB */',
                true,
            ],
            [
                '/* testPassByReferenceC */',
                true,
            ],
            [
                '/* testPassByReferenceD */',
                true,
            ],
            [
                '/* testPassByReferenceE */',
                true,
            ],
            [
                '/* testPassByReferenceF */',
                true,
            ],
            [
                '/* testPassByReferenceG */',
                true,
            ],
            [
                '/* testPassByReferenceH */',
                true,
            ],
            [
                '/* testPassByReferenceI */',
                true,
            ],
            [
                '/* testPassByReferenceJ */',
                true,
            ],
            [
                '/* testNewByReferenceA */',
                true,
            ],
            [
                '/* testNewByReferenceB */',
                true,
            ],
            [
                '/* testUseByReference */',
                true,
            ],
            [
                '/* testArrowFunctionReturnByReference */',
                true,
            ],
            [
                '/* testArrowFunctionPassByReferenceA */',
                true,
            ],
            [
                '/* testArrowFunctionPassByReferenceB */',
                true,
            ],
            [
                '/* testClosureReturnByReference */',
                true,
            ],
        ];

    }//end dataIsReference()


}//end class
