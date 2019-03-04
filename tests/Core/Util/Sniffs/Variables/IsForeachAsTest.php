<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Variables::isForeachAs() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Variables;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Variables;

class IsForeachAsTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non variable is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_VARIABLE
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Variables::isForeachAs
     *
     * @return void
     */
    public function testNotAVariableException()
    {
        $next   = $this->getTargetToken('/* testNotAVariable */', T_RETURN);
        $result = Variables::isForeachAs(self::$phpcsFile, $next);

    }//end testNotAVariableException()


    /**
     * Test correctly identifying whether a T_VARIABLE token in the `as ...` part of a foreach statement.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @dataProvider dataIsForeachAs
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Variables::isForeachAs
     *
     * @return void
     */
    public function testIsForeachAs($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_VARIABLE, '$something');
        $result   = Variables::isForeachAs(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testIsForeachAs()


    /**
     * Data provider.
     *
     * @see testIsForeachAs()
     *
     * @return array
     */
    public function dataIsForeachAs()
    {
        return [
            [
                '/* testNoParenthesis */',
                false,
            ],
            [
                '/* testNoParenthesisOwner */',
                false,
            ],
            [
                '/* testOwnerNotForeach */',
                false,
            ],
            [
                '/* testForeachWithoutAs */',
                false,
            ],
            [
                '/* testForeachVarBeforeAs */',
                false,
            ],
            [
                '/* testForeachVarAfterAs */',
                true,
            ],
            [
                '/* testForeachVarAfterAsKey */',
                true,
            ],
            [
                '/* testForeachVarAfterAsValue */',
                true,
            ],
            [
                '/* testForeachVarAfterAsList */',
                true,
            ],
            [
                '/* testNestedForeachVarAfterAs */',
                true,
            ],
            [
                '/* testParseError */',
                false,
            ],
        ];

    }//end dataIsForeachAs()


}//end class
