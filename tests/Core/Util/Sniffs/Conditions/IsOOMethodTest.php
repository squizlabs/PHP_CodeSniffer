<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOMethod() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Conditions;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Conditions;

class IsOOMethodTest extends AbstractMethodUnitTest
{


    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOMethod
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Conditions::isOOMethod(self::$phpcsFile, 10000);
        $this->assertFalse($result);

    }//end testNonExistentToken()


    /**
     * Test passing a non function token.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOMethod
     *
     * @return void
     */
    public function testNonFunctionToken()
    {
        $result = Conditions::isOOMethod(self::$phpcsFile, 0);
        $this->assertFalse($result);

    }//end testNonFunctionToken()


    /**
     * Test correctly identifying whether a T_FUNCTION token is a class method declaration.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @dataProvider dataIsOOMethod
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOMethod
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::validDirectScope
     *
     * @return void
     */
    public function testIsOOMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [T_FUNCTION, T_CLOSURE]);
        $result   = Conditions::isOOMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testIsOOMethod()


    /**
     * Data provider.
     *
     * @see testIsOOMethod()
     *
     * @return array
     */
    public function dataIsOOMethod()
    {
        return [
            [
                '/* testGlobalFunction */',
                false,
            ],
            [
                '/* testNestedFunction */',
                false,
            ],
            [
                '/* testNestedClosure */',
                false,
            ],
            [
                '/* testClassMethod */',
                true,
            ],
            [
                '/* testClassNestedFunction */',
                false,
            ],
            [
                '/* testClassNestedClosure */',
                false,
            ],
            [
                '/* testClassAbstractMethod */',
                true,
            ],
            [
                '/* testAnonClassMethod */',
                true,
            ],
            [
                '/* testInterfaceMethod */',
                true,
            ],
            [
                '/* testTraitMethod */',
                true,
            ],
        ];

    }//end dataIsOOMethod()


}//end class
