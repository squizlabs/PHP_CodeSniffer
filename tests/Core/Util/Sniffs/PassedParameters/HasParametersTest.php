<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2016-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\PassedParameters;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\PassedParameters;

class HasParametersTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a token which is not supported by
     * these methods is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage The hasParameters() method expects a function call, array, list, isset or unset token to be passed. Received "T_INTERFACE" instead
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters
     *
     * @return void
     */
    public function testNotAnAcceptedTokenException()
    {
        $interface = $this->getTargetToken('/* testNotAnAcceptedToken */', T_INTERFACE);
        $result    = PassedParameters::hasParameters(self::$phpcsFile, $interface);

    }//end testNotAnAcceptedTokenException()


    /**
     * Test receiving an expected exception when T_SELF is passed not preceeded by `new`.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage The hasParameters() method expects a function call, array, list, isset or unset token to be passed. Received "T_SELF" instead
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters
     *
     * @return void
     */
    public function testNotACallToConstructor()
    {
        $self   = $this->getTargetToken('/* testNotACallToConstructor */', T_SELF);
        $result = PassedParameters::hasParameters(self::$phpcsFile, $self);

    }//end testNotACallToConstructor()


    /**
     * Test correctly identifying whether parameters were passed to a function call or construct.
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The type of token to look for.
     * @param bool       $expected   Whether or not the function/array has parameters/values.
     *
     * @dataProvider dataHasParameters
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters
     *
     * @return void
     */
    public function testHasParameters($testMarker, $targetType, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);
        $result   = PassedParameters::hasParameters(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testHasParameters()


    /**
     * Data provider.
     *
     * @see testHasParameters()
     *
     * @return array
     */
    public function dataHasParameters()
    {
        return [
            // Function calls.
            [
                '/* testNoParamsFunctionCall1 */',
                T_STRING,
                false,
            ],
            [
                '/* testNoParamsFunctionCall2 */',
                T_STRING,
                false,
            ],
            [
                '/* testNoParamsFunctionCall3 */',
                T_STRING,
                false,
            ],
            [
                '/* testNoParamsFunctionCall4 */',
                T_VARIABLE,
                false,
            ],
            [
                '/* testHasParamsFunctionCall1 */',
                T_STRING,
                true,
            ],
            [
                '/* testHasParamsFunctionCall2 */',
                T_VARIABLE,
                true,
            ],
            [
                '/* testHasParamsFunctionCall3 */',
                T_SELF,
                true,
            ],

            // Arrays.
            [
                '/* testNoParamsLongArray1 */',
                T_ARRAY,
                false,
            ],
            [
                '/* testNoParamsLongArray2 */',
                T_ARRAY,
                false,
            ],
            [
                '/* testNoParamsLongArray3 */',
                T_ARRAY,
                false,
            ],
            [
                '/* testNoParamsLongArray4 */',
                T_ARRAY,
                false,
            ],
            [
                '/* testNoParamsShortArray1 */',
                T_OPEN_SHORT_ARRAY,
                false,
            ],
            [
                '/* testNoParamsShortArray2 */',
                T_OPEN_SHORT_ARRAY,
                false,
            ],
            [
                '/* testNoParamsShortArray3 */',
                T_OPEN_SHORT_ARRAY,
                false,
            ],
            [
                '/* testNoParamsShortArray4 */',
                T_OPEN_SHORT_ARRAY,
                false,
            ],
            [
                '/* testHasParamsLongArray1 */',
                T_ARRAY,
                true,
            ],
            [
                '/* testHasParamsLongArray2 */',
                T_ARRAY,
                true,
            ],
            [
                '/* testHasParamsLongArray3 */',
                T_ARRAY,
                true,
            ],
            [
                '/* testHasParamsShortArray1 */',
                T_OPEN_SHORT_ARRAY,
                true,
            ],
            [
                '/* testHasParamsShortArray2 */',
                T_OPEN_SHORT_ARRAY,
                true,
            ],
            [
                '/* testHasParamsShortArray3 */',
                T_OPEN_SHORT_ARRAY,
                true,
            ],

            // Lists.
            [
                '/* testNoParamsLongList */',
                T_LIST,
                false,
            ],
            [
                '/* testHasParamsLongList */',
                T_LIST,
                true,
            ],
            [
                '/* testNoParamsShortList */',
                T_OPEN_SHORT_ARRAY,
                false,
            ],
            [
                '/* testHasParamsShortList */',
                T_OPEN_SHORT_ARRAY,
                true,
            ],

            // Isset.
            [
                '/* testNoParamsIsset */',
                T_ISSET,
                false,
            ],
            [
                '/* testHasParamsIsset */',
                T_ISSET,
                true,
            ],

            // Unset.
            [
                '/* testNoParamsUnset */',
                T_UNSET,
                false,
            ],
            [
                '/* testHasParamsUnset */',
                T_UNSET,
                true,
            ],

            // Defensive coding against parse errors and live coding.
            [
                '/* testNoCloseParenthesis */',
                T_ARRAY,
                false,
            ],
            [
                '/* testNoOpenParenthesis */',
                T_STRING,
                false,
            ],
            [
                '/* testLiveCoding */',
                T_ARRAY,
                false,
            ],
        ];

    }//end dataHasParameters()


}//end class
