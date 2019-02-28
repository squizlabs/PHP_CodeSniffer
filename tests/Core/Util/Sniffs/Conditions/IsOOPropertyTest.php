<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOProperty() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Conditions;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Conditions;

class IsOOPropertyTest extends AbstractMethodUnitTest
{


    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOProperty
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Conditions::isOOProperty(self::$phpcsFile, 10000);
        $this->assertFalse($result);

    }//end testNonExistentToken()


    /**
     * Test passing a non variable token.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOProperty
     *
     * @return void
     */
    public function testNonVariableToken()
    {
        $result = Conditions::isOOProperty(self::$phpcsFile, 0);
        $this->assertFalse($result);

    }//end testNonVariableToken()


    /**
     * Test correctly identifying whether a T_VARIABLE token is a class property declaration.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @dataProvider dataIsOOProperty
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOProperty
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::validDirectScope
     *
     * @return void
     */
    public function testIsOOProperty($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_VARIABLE);
        $result   = Conditions::isOOProperty(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testIsOOProperty()


    /**
     * Data provider.
     *
     * @see testIsOOProperty()
     *
     * @return array
     */
    public function dataIsOOProperty()
    {
        return [
            [
                '/* testGlobalVar */',
                false,
            ],
            [
                '/* testFunctionParameter */',
                false,
            ],
            [
                '/* testFunctionLocalVar */',
                false,
            ],
            [
                '/* testClassPropPublic */',
                true,
            ],
            [
                '/* testClassPropVar */',
                true,
            ],
            [
                '/* testClassPropStaticProtected */',
                true,
            ],
            [
                '/* testMethodParameter */',
                false,
            ],
            [
                '/* testMethodLocalVar */',
                false,
            ],
            [
                '/* testAnonClassPropPrivate */',
                true,
            ],
            [
                '/* testAnonMethodParameter */',
                false,
            ],
            [
                '/* testAnonMethodLocalVar */',
                false,
            ],
            [
                '/* testInterfaceProp */',
                false,
            ],
            [
                '/* testInterfaceMethodParameter */',
                false,
            ],
            [
                '/* testTraitProp */',
                true,
            ],
            [
                '/* testTraitMethodParameter */',
                false,
            ],
            [
                '/* testClassMultiProp1 */',
                true,
            ],
            [
                '/* testClassMultiProp2 */',
                true,
            ],
            [
                '/* testClassMultiProp3 */',
                true,
            ],
            [
                '/* testGlobalVarObj */',
                false,
            ],
            [
                '/* testNestedAnonClassProp */',
                true,
            ],
            [
                '/* testDoubleNestedAnonClassProp */',
                true,
            ],
            [
                '/* testDoubleNestedAnonClassMethodParameter */',
                false,
            ],
            [
                '/* testDoubleNestedAnonClassMethodLocalVar */',
                false,
            ],
            [
                '/* testFunctionCallParameter */',
                false,
            ],
        ];

    }//end dataIsOOProperty()


}//end class
