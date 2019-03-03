<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicMethod(),
 * \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isPHPDoubleUnderscoreMethod(),
 * \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicFunction() and the
 * \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isSpecialMethod() methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\FunctionDeclarations;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;

class SpecialFunctionsTest extends AbstractMethodUnitTest
{


    /**
     * Test correctly detecting magic methods.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicMethod
     *
     * @return void
     */
    public function testIsMagicMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_FUNCTION);
        $result   = FunctionDeclarations::isMagicMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['method'], $result);

    }//end testIsMagicMethod()


    /**
     * Test correctly detecting PHP native double underscore methods.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isPHPDoubleUnderscoreMethod
     *
     * @return void
     */
    public function testIsPHPDoubleUnderscoreMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_FUNCTION);
        $result   = FunctionDeclarations::isPHPDoubleUnderscoreMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['double'], $result);

    }//end testIsPHPDoubleUnderscoreMethod()


    /**
     * Test correctly detecting magic functions.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicFunction
     *
     * @return void
     */
    public function testIsMagicFunction($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_FUNCTION);
        $result   = FunctionDeclarations::isMagicFunction(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['function'], $result);

    }//end testIsMagicFunction()


    /**
     * Test correctly detecting magic methods and double underscore methods.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isSpecialMethod
     *
     * @return void
     */
    public function testIsSpecialMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_FUNCTION);
        $result   = FunctionDeclarations::isSpecialMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['special'], $result);

    }//end testIsSpecialMethod()


    /**
     * Data provider.
     *
     * @see testIsMagicMethod()
     * @see testIsPHPDoubleUnderscoreMethod()
     * @see testIsMagicFunction()
     * @see testIsSpecialMethod()
     *
     * @return array
     */
    public function dataItsAKindOfMagic()
    {
        return [
            [
                '/* testMagicMethodInClass */',
                [
                    'method'   => true,
                    'double'   => false,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testMagicMethodInClassUppercase */',
                [
                    'method'   => true,
                    'double'   => false,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testMagicMethodInClassMixedCase */',
                [
                    'method'   => true,
                    'double'   => false,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testMagicFunctionInClassNotGlobal */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMethodInClassNotMagicName */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMagicMethodNotInClass */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMagicFunction */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => true,
                    'special'  => false,
                ],
            ],
            [
                '/* testMagicFunctionInConditionMixedCase */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => true,
                    'special'  => false,
                ],
            ],
            [
                '/* testFunctionNotMagicName */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMagicMethodInAnonClass */',
                [
                    'method'   => true,
                    'double'   => false,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testMagicMethodInAnonClassUppercase */',
                [
                    'method'   => true,
                    'double'   => false,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testMagicFunctionInAnonClassNotGlobal */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMethodInAnonClassNotMagicName */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testDoubleUnderscoreMethodInClass */',
                [
                    'method'   => false,
                    'double'   => true,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testDoubleUnderscoreMethodInClassMixedcase */',
                [
                    'method'   => false,
                    'double'   => true,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testDoubleUnderscoreMethodNotInClass */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMagicMethodInTrait */',
                [
                    'method'   => true,
                    'double'   => false,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testMagicFunctionInTraitNotGlobal */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMethodInTraitNotMagicName */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMagicMethodInInterface */',
                [
                    'method'   => true,
                    'double'   => false,
                    'function' => false,
                    'special'  => true,
                ],
            ],
            [
                '/* testMagicFunctionInInterfaceNotGlobal */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
            [
                '/* testMethodInInterfaceNotMagicName */',
                [
                    'method'   => false,
                    'double'   => false,
                    'function' => false,
                    'special'  => false,
                ],
            ],
        ];

    }//end dataItsAKindOfMagic()


}//end class
