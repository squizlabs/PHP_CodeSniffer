<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOConstant() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Conditions;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Conditions;

class IsOOConstantTest extends AbstractMethodUnitTest
{


    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOConstant
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Conditions::isOOConstant(self::$phpcsFile, 10000);
        $this->assertFalse($result);

    }//end testNonExistentToken()


    /**
     * Test passing a non const token.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOConstant
     *
     * @return void
     */
    public function testNonConstToken()
    {
        $result = Conditions::isOOConstant(self::$phpcsFile, 0);
        $this->assertFalse($result);

    }//end testNonConstToken()


    /**
     * Test correctly identifying whether a T_CONST token is a class constant.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @dataProvider dataIsOOConstant
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::isOOConstant
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Conditions::validDirectScope
     *
     * @return void
     */
    public function testIsOOConstant($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_CONST);
        $result   = Conditions::isOOConstant(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testIsOOConstant()


    /**
     * Data provider.
     *
     * @see testIsOOConstant()
     *
     * @return array
     */
    public function dataIsOOConstant()
    {
        return [
            [
                '/* testGlobalConst */',
                false,
            ],
            [
                '/* testFunctionConst */',
                false,
            ],
            [
                '/* testClassConst */',
                true,
            ],
            [
                '/* testClassMethodConst */',
                false,
            ],
            [
                '/* testAnonClassConst */',
                true,
            ],
            [
                '/* testInterfaceConst */',
                true,
            ],
            [
                '/* testTraitConst */',
                false,
            ],
        ];

    }//end dataIsOOConstant()


}//end class
