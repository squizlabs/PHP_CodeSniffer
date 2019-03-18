<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameterCount() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2016-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\PassedParameters;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\PassedParameters;

class GetParameterCountTest extends AbstractMethodUnitTest
{


    /**
     * Test correctly counting the number of passed parameters.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param int    $expected   The expected parameter count.
     *
     * @dataProvider dataGetParameterCount
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameterCount
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameters
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters
     *
     * @return void
     */
    public function testGetParameterCount($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [T_STRING, T_ARRAY, T_OPEN_SHORT_ARRAY, T_LIST, T_ISSET, T_UNSET]);
        $result   = PassedParameters::getParameterCount(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testGetParameterCount()


    /**
     * Data provider.
     *
     * @see testGetParameterCount()
     *
     * @return array
     */
    public function dataGetParameterCount()
    {
        return [
            [
                '/* testFunctionCall0 */',
                0,
            ],
            [
                '/* testFunctionCall1 */',
                1,
            ],
            [
                '/* testFunctionCall2 */',
                2,
            ],
            [
                '/* testFunctionCall3 */',
                3,
            ],
            [
                '/* testFunctionCall4 */',
                4,
            ],
            [
                '/* testFunctionCall5 */',
                5,
            ],
            [
                '/* testFunctionCall6 */',
                6,
            ],
            [
                '/* testFunctionCall7 */',
                7,
            ],
            [
                '/* testFunctionCall8 */',
                1,
            ],
            [
                '/* testFunctionCall9 */',
                1,
            ],
            [
                '/* testFunctionCall10 */',
                1,
            ],
            [
                '/* testFunctionCall11 */',
                2,
            ],
            [
                '/* testFunctionCall12 */',
                1,
            ],
            [
                '/* testFunctionCall13 */',
                1,
            ],
            [
                '/* testFunctionCall14 */',
                1,
            ],
            [
                '/* testFunctionCall15 */',
                2,
            ],
            [
                '/* testFunctionCall16 */',
                6,
            ],
            [
                '/* testFunctionCall17 */',
                6,
            ],
            [
                '/* testFunctionCall18 */',
                6,
            ],
            [
                '/* testFunctionCall19 */',
                6,
            ],
            [
                '/* testFunctionCall20 */',
                6,
            ],
            [
                '/* testFunctionCall21 */',
                6,
            ],
            [
                '/* testFunctionCall22 */',
                6,
            ],
            [
                '/* testFunctionCall23 */',
                3,
            ],
            [
                '/* testFunctionCall24 */',
                1,
            ],
            [
                '/* testFunctionCall25 */',
                1,
            ],
            [
                '/* testFunctionCall26 */',
                1,
            ],
            [
                '/* testFunctionCall27 */',
                1,
            ],
            [
                '/* testFunctionCall28 */',
                1,
            ],
            [
                '/* testFunctionCall29 */',
                1,
            ],
            [
                '/* testFunctionCall30 */',
                1,
            ],
            [
                '/* testFunctionCall31 */',
                1,
            ],
            [
                '/* testFunctionCall32 */',
                1,
            ],
            [
                '/* testFunctionCall33 */',
                1,
            ],
            [
                '/* testFunctionCall34 */',
                1,
            ],
            [
                '/* testFunctionCall35 */',
                1,
            ],
            [
                '/* testFunctionCall36 */',
                1,
            ],
            [
                '/* testFunctionCall37 */',
                1,
            ],
            [
                '/* testFunctionCall38 */',
                1,
            ],
            [
                '/* testFunctionCall39 */',
                1,
            ],
            [
                '/* testFunctionCall40 */',
                1,
            ],
            [
                '/* testFunctionCall41 */',
                1,
            ],
            [
                '/* testFunctionCall42 */',
                1,
            ],
            [
                '/* testFunctionCall43 */',
                1,
            ],
            [
                '/* testFunctionCall44 */',
                1,
            ],
            [
                '/* testFunctionCall45 */',
                1,
            ],
            [
                '/* testFunctionCall46 */',
                1,
            ],
            [
                '/* testFunctionCall47 */',
                1,
            ],

            // Long arrays.
            [
                '/* testLongArray1 */',
                7,
            ],
            [
                '/* testLongArray2 */',
                1,
            ],
            [
                '/* testLongArray3 */',
                6,
            ],
            [
                '/* testLongArray4 */',
                6,
            ],
            [
                '/* testLongArray5 */',
                6,
            ],
            [
                '/* testLongArray6 */',
                3,
            ],
            [
                '/* testLongArray7 */',
                3,
            ],
            [
                '/* testLongArray8 */',
                3,
            ],

            // Short arrays.
            [
                '/* testShortArray1 */',
                7,
            ],
            [
                '/* testShortArray2 */',
                1,
            ],
            [
                '/* testShortArray3 */',
                6,
            ],
            [
                '/* testShortArray4 */',
                6,
            ],
            [
                '/* testShortArray5 */',
                6,
            ],
            [
                '/* testShortArray6 */',
                3,
            ],
            [
                '/* testShortArray7 */',
                3,
            ],
            [
                '/* testShortArray8 */',
                3,
            ],
        ];

    }//end dataGetParameterCount()


}//end class
