<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\TokenIs::isReference() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\TokenIs;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\TokenIs;

class IsReferenceTest extends AbstractMethodUnitTest
{


    /**
     * Test whether a bitwise-and token is used as a reference.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataIsReference
     * @covers       \PHP_CodeSniffer\Util\Sniffs\TokenIs::isReference
     *
     * @return void
     */
    public function testIsReference($testMarker, $expected)
    {
        $bitwiseAnd = $this->getTargetToken($testMarker, T_BITWISE_AND);
        $result     = TokenIs::isReference(self::$phpcsFile, $bitwiseAnd);
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
        ];

    }//end dataIsReference()


}//end class
