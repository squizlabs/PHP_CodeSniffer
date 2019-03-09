<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\TokenIs::isUnaryPlusMinus() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\TokenIs;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\TokenIs;

class IsUnaryPlusMinusTest extends AbstractMethodUnitTest
{


    /**
     * Test that false is returned when a non-plus/minus token is passed.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\TokenIs::isUnaryPlusMinus
     *
     * @return void
     */
    public function testNotPlusMinusToken()
    {
        $target = $this->getTargetToken('/* testNonUnaryPlus */', T_LNUMBER);
        $this->assertFalse(TokenIs::isUnaryPlusMinus(self::$phpcsFile, $target));

    }//end testNotPlusMinusToken()


    /**
     * Test whether a T_PLUS or T_MINUS token is a unary operator.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected boolean return value.
     *
     * @dataProvider dataIsUnaryPlusMinus
     * @covers       \PHP_CodeSniffer\Util\Sniffs\TokenIs::isUnaryPlusMinus
     *
     * @return void
     */
    public function testIsUnaryPlusMinus($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [T_PLUS, T_MINUS]);
        $result   = TokenIs::isUnaryPlusMinus(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);

    }//end testIsUnaryPlusMinus()


    /**
     * Data provider.
     *
     * @see testIsUnaryPlusMinus()
     *
     * @return array
     */
    public function dataIsUnaryPlusMinus()
    {
        return [
            [
                '/* testNonUnaryPlus */',
                false,
            ],
            [
                '/* testNonUnaryMinus */',
                false,
            ],
            [
                '/* testNonUnaryPlusArrays */',
                false,
            ],
            [
                '/* testUnaryPlusIntAssignment */',
                true,
            ],
            [
                '/* testUnaryMinusVariableAssignment */',
                true,
            ],
            [
                '/* testUnaryPlusFloatAssignment */',
                true,
            ],
            [
                '/* testUnaryMinusBoolAssignment */',
                true,
            ],
            [
                '/* testUnaryPlusStringAssignmentWithComment */',
                true,
            ],
            [
                '/* testUnaryMinusStringAssignment */',
                true,
            ],
            [
                '/* testUnaryPlusNullAssignment */',
                true,
            ],
            [
                '/* testUnaryMinusVariableVariableAssignment */',
                true,
            ],
            [
                '/* testUnaryPlusIntComparison */',
                true,
            ],
            [
                '/* testUnaryPlusIntComparisonYoda */',
                true,
            ],
            [
                '/* testUnaryMinusFloatComparison */',
                true,
            ],
            [
                '/* testUnaryMinusStringComparisonYoda */',
                true,
            ],
            [
                '/* testUnaryPlusVariableLogical */',
                true,
            ],
            [
                '/* testUnaryMinusVariableLogical */',
                true,
            ],
            [
                '/* testUnaryMinusInlineIf */',
                true,
            ],
            [
                '/* testUnaryPlusInlineThen */',
                true,
            ],
            [
                '/* testUnaryPlusIntReturn */',
                true,
            ],
            [
                '/* testUnaryMinusFloatReturn */',
                true,
            ],
            [
                '/* testUnaryPlusArrayAccess */',
                true,
            ],
            [
                '/* testUnaryMinusStringArrayAccess */',
                true,
            ],
            [
                '/* testUnaryPlusLongArrayAssignment */',
                true,
            ],
            [
                '/* testUnaryMinusLongArrayAssignmentKey */',
                true,
            ],
            [
                '/* testUnaryPlusLongArrayAssignmentValue */',
                true,
            ],
            [
                '/* testUnaryPlusShortArrayAssignment */',
                true,
            ],
            [
                '/* testNonUnaryMinusShortArrayAssignment */',
                false,
            ],
            [
                '/* testUnaryMinusCast */',
                true,
            ],
            [
                '/* testUnaryPlusFunctionCallParam */',
                true,
            ],
            [
                '/* testUnaryMinusFunctionCallParam */',
                true,
            ],
            [
                '/* testUnaryPlusCase */',
                true,
            ],
            [
                '/* testUnaryMinusCase */',
                true,
            ],
            [
                '/* testSequenceNonUnary1 */',
                false,
            ],
            [
                '/* testSequenceNonUnary2 */',
                false,
            ],
            [
                '/* testSequenceNonUnary3 */',
                false,
            ],
            [
                '/* testSequenceUnaryEnd */',
                true,
            ],
            [
                '/* testParseError */',
                false,
            ],
        ];

    }//end dataIsUnaryPlusMinus()


}//end class
