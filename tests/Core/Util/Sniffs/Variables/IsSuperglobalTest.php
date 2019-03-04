<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Variables::IsSuperglobal() and
 * \PHP_CodeSniffer\Util\Sniffs\Variables::IsSuperglobalName() methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Variables;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Variables;

class IsSuperglobalTest extends AbstractMethodUnitTest
{


    /**
     * Test correctly detecting superglobal variables.
     *
     * @param string     $testMarker      The comment which prefaces the target token in the test file.
     * @param bool       $expected        The expected function return value.
     * @param int|string $testTargetType  Optional. The token type for the target token in the test file.
     * @param string     $testTargetValue Optional. The token content for the target token in the test file.
     *
     * @dataProvider dataIsSuperglobal
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Variables::isSuperglobal
     *
     * @return void
     */
    public function testIsSuperglobal($testMarker, $expected, $testTargetType=T_VARIABLE, $testTargetValue=null)
    {
        $stackPtr = $this->getTargetToken($testMarker, $testTargetType, $testTargetValue);
        $result   = Variables::isSuperglobal(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testIsSuperglobal()


    /**
     * Data provider.
     *
     * @see testIsSuperglobal()
     *
     * @return array
     */
    public function dataIsSuperglobal()
    {
        return [
            [
                '/* testNotAVariable */',
                false,
                T_RETURN,
            ],
            [
                '/* testNotAReservedVar */',
                false,
            ],
            [
                '/* testReservedVarNotSuperglobal */',
                false,
            ],
            [
                '/* testReservedVarIsSuperglobal */',
                true,
            ],
            [
                '/* testGLOBALSArrayKeyNotAReservedVar */',
                false,
                T_CONSTANT_ENCAPSED_STRING,
            ],
            [
                '/* testGLOBALSArrayKeyVar */',
                false,
                T_VARIABLE,
                '$something',
            ],
            [
                '/* testGLOBALSArrayKeyReservedVar */',
                false,
                T_VARIABLE,
                '$php_errormsg',
            ],
            [
                '/* testGLOBALSArrayKeySuperglobal */',
                true,
                T_VARIABLE,
                '$_COOKIE',
            ],
            [
                '/* testGLOBALSArrayKeyNotSingleString */',
                false,
                T_CONSTANT_ENCAPSED_STRING,
            ],
            [
                '/* testGLOBALSArrayKeyInterpolatedVar */',
                false,
                T_DOUBLE_QUOTED_STRING,
            ],
            [
                '/* testGLOBALSArrayKeySingleStringSuperglobal */',
                true,
                T_CONSTANT_ENCAPSED_STRING,
            ],
            [
                '/* testGLOBALSArrayKeySuperglobalWithKey */',
                true,
                T_VARIABLE,
                '$_GET',
            ],
            [
                '/* testSuperglobalKeyNotGLOBALSArray */',
                false,
                T_CONSTANT_ENCAPSED_STRING,
            ],
        ];

    }//end dataIsSuperglobal()


    /**
     * Test valid PHP superglobal names.
     *
     * @param string $name The variable name to test.
     *
     * @dataProvider dataIsSuperglobalName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Variables::isSuperglobalName
     *
     * @return void
     */
    public function testIsSuperglobalName($name)
    {
        $this->assertTrue(Variables::isSuperglobalName($name));

    }//end testIsSuperglobalName()


    /**
     * Data provider.
     *
     * @see testIsSuperglobalName()
     *
     * @return array
     */
    public function dataIsSuperglobalName()
    {
        return [
            ['$_SERVER'],
            ['$_GET'],
            ['$_POST'],
            ['$_REQUEST'],
            ['_SESSION'],
            ['_ENV'],
            ['_COOKIE'],
            ['_FILES'],
            ['GLOBALS'],
        ];

    }//end dataIsSuperglobalName()


    /**
     * Test non-superglobal variable names.
     *
     * @param string $name The variable name to test.
     *
     * @dataProvider dataIsSuperglobalNameFalse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Variables::isSuperglobalName
     *
     * @return void
     */
    public function testIsSuperglobalNameFalse($name)
    {
        $this->assertFalse(Variables::isSuperglobalName($name));

    }//end testIsSuperglobalNameFalse()


    /**
     * Data provider.
     *
     * @see testIsSuperglobalNameFalse()
     *
     * @return array
     */
    public function dataIsSuperglobalNameFalse()
    {
        return [
            ['$not_a_superglobal'],
            ['$http_response_header'],
            ['$argc'],
            ['$argv'],
            ['$HTTP_RAW_POST_DATA'],
            ['$php_errormsg'],
            ['HTTP_SERVER_VARS'],
            ['HTTP_GET_VARS'],
            ['HTTP_POST_VARS'],
            ['HTTP_SESSION_VARS'],
            ['HTTP_ENV_VARS'],
            ['HTTP_COOKIE_VARS'],
            ['HTTP_POST_FILES'],
        ];

    }//end dataIsSuperglobalNameFalse()


}//end class
