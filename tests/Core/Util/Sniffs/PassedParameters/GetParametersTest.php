<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameters() and
 * \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameter() methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2016-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\PassedParameters;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\PassedParameters;

class GetParametersTest extends AbstractMethodUnitTest
{


    /**
     * Test retrieving the parameter details from a function call without parameters.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameters
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameter
     * @covers \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters
     *
     * @return void
     */
    public function testGetParametersNoParams()
    {
        $stackPtr = $this->getTargetToken('/* testNoParams */', T_STRING);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);
        $this->assertSame([], $result);

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 2);
        $this->assertFalse($result);

    }//end testGetParametersNoParams()


    /**
     * Test retrieving the parameter details from a function call or construct.
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The type of token to look for.
     * @param array      $expected   The expected parameter array.
     *
     * @dataProvider dataGetParameters
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameters
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters
     *
     * @return void
     */
    public function testGetParameters($testMarker, $targetType, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] = ($stackPtr + $value['start']);
            $expected[$key]['end']   = ($stackPtr + $value['end']);
        }

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testGetParameters()


    /**
     * Data provider.
     *
     * @see testGetParameters()
     *
     * @return array
     */
    public function dataGetParameters()
    {
        return [
            [
                '/* testFunctionCall */',
                T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '1',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 6,
                        'raw'   => '2',
                    ],
                    3 => [
                        'start' => 8,
                        'end'   => 9,
                        'raw'   => '3',
                    ],
                    4 => [
                        'start' => 11,
                        'end'   => 12,
                        'raw'   => '4',
                    ],
                    5 => [
                        'start' => 14,
                        'end'   => 15,
                        'raw'   => '5',
                    ],
                    6 => [
                        'start' => 17,
                        'end'   => 18,
                        'raw'   => '6',
                    ],
                    7 => [
                        'start' => 20,
                        'end'   => 22,
                        'raw'   => 'true',
                    ],
                ],
            ],
            [
                '/* testFunctionCallNestedFunctionCall */',
                T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 9,
                        'raw'   => 'dirname( __FILE__ )',
                    ],
                ],
            ],
            [
                '/* testAnotherFunctionCall */',
                T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '$stHour',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 5,
                        'raw'   => '0',
                    ],
                    3 => [
                        'start' => 7,
                        'end'   => 8,
                        'raw'   => '0',
                    ],
                    4 => [
                        'start' => 10,
                        'end'   => 14,
                        'raw'   => '$arrStDt[0]',
                    ],
                    5 => [
                        'start' => 16,
                        'end'   => 20,
                        'raw'   => '$arrStDt[1]',
                    ],
                    6 => [
                        'start' => 22,
                        'end'   => 26,
                        'raw'   => '$arrStDt[2]',
                    ],
                ],

            ],
            [
                '/* testFunctionCallTrailingComma */',
                T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 5,
                        'raw'   => 'array()',
                    ],
                ],
            ],
            [
                '/* testFunctionCallNestedShortArray */',
                T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 34,
                        'raw'   => '[\'a\' => $a,] + (isset($b) ? [\'b\' => $b,] : [])',
                    ],
                ],
            ],
            [
                '/* testFunctionCallNestedArrayNestedClosureWithCommas */',
                T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 90,
                        'raw'   => '/* testShortArrayNestedClosureWithCommas */
    [
        \'~\'.$dyn.\'~J\' => function ($match) {
            echo strlen($match[0]), \' matches for "a" found\', PHP_EOL;
        },
        \'~\'.function_call().\'~i\' => function ($match) {
            echo strlen($match[0]), \' matches for "b" found\', PHP_EOL;
        },
    ]',
                    ],
                    2 => [
                        'start' => 92,
                        'end'   => 95,
                        'raw'   => '$subject',
                    ],
                ],
            ],

            // Long array.
            [
                '/* testLongArrayNestedFunctionCalls */',
                T_ARRAY,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 8,
                        'raw'   => 'some_call(5, 1)',
                    ],
                    2 => [
                        'start' => 10,
                        'end'   => 14,
                        'raw'   => 'another(1)',
                    ],
                    3 => [
                        'start' => 16,
                        'end'   => 26,
                        'raw'   => 'why(5, 1, 2)',
                    ],
                    4 => [
                        'start' => 28,
                        'end'   => 29,
                        'raw'   => '4',
                    ],
                    5 => [
                        'start' => 31,
                        'end'   => 32,
                        'raw'   => '5',
                    ],
                    6 => [
                        'start' => 34,
                        'end'   => 35,
                        'raw'   => '6',
                    ],
                ],
            ],

            // Short array.
            [
                '/* testShortArrayNestedFunctionCalls */',
                T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 1,
                        'raw'   => '0',
                    ],
                    2 => [
                        'start' => 3,
                        'end'   => 4,
                        'raw'   => '0',
                    ],
                    3 => [
                        'start' => 6,
                        'end'   => 13,
                        'raw'   => 'date(\'s\', $timestamp)',
                    ],
                    4 => [
                        'start' => 15,
                        'end'   => 19,
                        'raw'   => 'date(\'m\')',
                    ],
                    5 => [
                        'start' => 21,
                        'end'   => 25,
                        'raw'   => 'date(\'d\')',
                    ],
                    6 => [
                        'start' => 27,
                        'end'   => 31,
                        'raw'   => 'date(\'Y\')',
                    ],
                ],
            ],

            // Nested arrays.
            [
                '/* testNestedArraysToplevel */',
                T_ARRAY,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 38,
                        'raw'   => '\'1\' => array(
        0 => \'more nesting\',
        /* testNestedArraysLevel2 */
        1 => array(1,2,3),
    )',
                    ],
                    2 => [
                        'start' => 40,
                        'end'   => 74,
                        'raw'   => '/* testNestedArraysLevel1 */
    \'2\' => [
        0 => \'more nesting\',
        1 => [1,2,3],
    ]',
                    ],
                ],
            ],

            // Array containing closure.
            [
                '/* testShortArrayNestedClosureWithCommas */',
                T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 38,
                        'raw'   => '\'~\'.$dyn.\'~J\' => function ($match) {
            echo strlen($match[0]), \' matches for "a" found\', PHP_EOL;
        }',
                    ],
                    2 => [
                        'start' => 40,
                        'end'   => 79,
                        'raw'   => '\'~\'.function_call().\'~i\' => function ($match) {
            echo strlen($match[0]), \' matches for "b" found\', PHP_EOL;
        }',
                    ],
                ],
            ],

            // Array containing anonymous class.
            [
                '/* testShortArrayNestedAnonClass */',
                T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 61,
                        'raw'   => '\'class\' => new class() {
        public $prop = [1,2,3];
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    }',
                    ],
                    2 => [
                        'start' => 63,
                        'end'   => 107,
                        'raw'   => '\'anotherclass\' => new class() {
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    }',
                    ],
                ],
            ],

            // Function calling closure in variable.
            [
                '/* testVariableFunctionCall */',
                T_VARIABLE,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '$a',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 11,
                        'raw'   => '(1 + 20)',
                    ],
                    3 => [
                        'start' => 13,
                        'end'   => 19,
                        'raw'   => '$a & $b',
                    ],
                ],
            ],
            [
                '/* testStaticVariableFunctionCall */',
                T_VARIABLE,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 4,
                        'raw'   => '$a->property',
                    ],
                    2 => [
                        'start' => 6,
                        'end'   => 12,
                        'raw'   => '$b->call()',
                    ],
                ],
            ],

            // Lists.
            [
                '/* testNestedList */',
                T_LIST,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '$a',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 11,
                        'raw'   => 'list($b, $c)',
                    ],
                ],
            ],
            [
                '/* testListWithEmptyEntries */',
                T_LIST,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 5,
                        'raw'   => '$a',
                    ],
                    3 => [
                        'start' => 7,
                        'end'   => 7,
                        'raw'   => '',
                    ],
                    4 => [
                        'start' => 9,
                        'end'   => 10,
                        'raw'   => '$b',
                    ],
                    5 => [
                        'start' => 12,
                        'end'   => 12,
                        'raw'   => '',
                    ],
                    6 => [
                        'start' => 14,
                        'end'   => 15,
                        'raw'   => '$a',
                    ],
                    7 => [
                        'start' => 17,
                        'end'   => 17,
                        'raw'   => '',
                    ],
                ],
            ],
            [
                '/* testMultiLineKeyedListWithTrailingComma */',
                T_LIST,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 10,
                        'raw'   => '"name" => $this->name',
                    ],
                    2 => [
                        'start' => 12,
                        'end'   => 20,
                        'raw'   => '"colour" => $this->colour',
                    ],
                    3 => [
                        'start' => 22,
                        'end'   => 30,
                        'raw'   => '"age" => $this->age',
                    ],
                    4 => [
                        'start' => 32,
                        'end'   => 40,
                        'raw'   => '"cuteness" => $this->cuteness',
                    ],
                ],
            ],
            [
                '/* testNestedShortList */',
                T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 6,
                        'raw'   => '[$a, $b]',
                    ],
                    2 => [
                        'start' => 8,
                        'end'   => 14,
                        'raw'   => '[$b, $a]',
                    ],
                ],
            ],
            [
                '/* testIsset */',
                T_ISSET,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 4,
                        'raw'   => '$variable',
                    ],
                    2 => [
                        'start' => 6,
                        'end'   => 10,
                        'raw'   => '$object->property',
                    ],
                    3 => [
                        'start' => 12,
                        'end'   => 16,
                        'raw'   => 'static::$property',
                    ],
                    4 => [
                        'start' => 18,
                        'end'   => 26,
                        'raw'   => '$array[$name][$sub]',
                    ],
                ],
            ],
            [
                '/* testUnset */',
                T_UNSET,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '$variable',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 8,
                        'raw'   => '$object->property',
                    ],
                    3 => [
                        'start' => 10,
                        'end'   => 13,
                        'raw'   => 'static::$property',
                    ],
                    4 => [
                        'start' => 15,
                        'end'   => 19,
                        'raw'   => '$array[$name]',
                    ],
                ],
            ],
        ];

    }//end dataGetParameters()


    /**
     * Test retrieving the details for a specific parameter from a function call or construct.
     *
     * @param string     $testMarker    The comment which prefaces the target token in the test file.
     * @param int|string $targetType    The type of token to look for.
     * @param int        $paramPosition The position of the parameter we want to retrieve the details for.
     * @param array      $expected      The expected array for the specific parameter.
     *
     * @dataProvider dataGetParameter
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameter
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::getParameters
     * @covers       \PHP_CodeSniffer\Util\Sniffs\PassedParameters::hasParameters
     *
     * @return void
     */
    public function testGetParameter($testMarker, $targetType, $paramPosition, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        $expected['start'] += $stackPtr;
        $expected['end']   += $stackPtr;

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, $paramPosition);
        $this->assertSame($expected, $result);

    }//end testGetParameter()


    /**
     * Data provider.
     *
     * @see testGetParameter()
     *
     * @return array
     */
    public function dataGetParameter()
    {
        return [
            [
                '/* testFunctionCall */',
                T_STRING,
                4,
                [
                    'start' => 11,
                    'end'   => 12,
                    'raw'   => '4',
                ],
            ],
            [
                '/* testFunctionCallNestedFunctionCall */',
                T_STRING,
                1,
                [
                    'start' => 2,
                    'end'   => 9,
                    'raw'   => 'dirname( __FILE__ )',
                ],
            ],
            [
                '/* testAnotherFunctionCall */',
                T_STRING,
                1,
                [
                    'start' => 2,
                    'end'   => 2,
                    'raw'   => '$stHour',
                ],
            ],
            [
                '/* testAnotherFunctionCall */',
                T_STRING,
                6,
                [
                    'start' => 22,
                    'end'   => 26,
                    'raw'   => '$arrStDt[2]',
                ],
            ],
            [
                '/* testLongArrayNestedFunctionCalls */',
                T_ARRAY,
                3,
                [
                    'start' => 16,
                    'end'   => 26,
                    'raw'   => 'why(5, 1, 2)',
                ],
            ],
            [
                '/* testSimpleLongArray */',
                T_ARRAY,
                1,
                [
                    'start' => 2,
                    'end'   => 3,
                    'raw'   => '1',
                ],
            ],
            [
                '/* testSimpleLongArray */',
                T_ARRAY,
                7,
                [
                    'start' => 20,
                    'end'   => 22,
                    'raw'   => 'true',
                ],
            ],
            [
                '/* testLongArrayWithKeys */',
                T_ARRAY,
                2,
                [
                    'start' => 8,
                    'end'   => 13,
                    'raw'   => '\'b\' => $b',
                ],
            ],
            [
                '/* testShortArrayMoreNestedFunctionCalls */',
                T_OPEN_SHORT_ARRAY,
                1,
                [
                    'start' => 1,
                    'end'   => 13,
                    'raw'   => 'str_replace("../", "/", trim($value))',
                ],
            ],
            [
                '/* testShortArrayWithKeysAndTernary */',
                T_OPEN_SHORT_ARRAY,
                3,
                [
                    'start' => 14,
                    'end'   => 32,
                    'raw'   => '6 => (isset($c) ? $c : null)',
                ],
            ],
            [
                '/* testNestedArraysLevel2 */',
                T_ARRAY,
                1,
                [
                    'start' => 2,
                    'end'   => 2,
                    'raw'   => '1',
                ],
            ],
            [
                '/* testNestedArraysLevel1 */',
                T_OPEN_SHORT_ARRAY,
                2,
                [
                    'start' => 9,
                    'end'   => 21,
                    'raw'   => '1 => [1,2,3]',
                ],
            ],
            [
                '/* testListWithKeys */',
                T_LIST,
                2,
                [
                    'start' => 8,
                    'end'   => 13,
                    'raw'   => '\'id\' => $b',
                ],
            ],
            [
                '/* testShortList */',
                T_OPEN_SHORT_ARRAY,
                3,
                [
                    'start' => 6,
                    'end'   => 7,
                    'raw'   => '$c',
                ],
            ],
        ];

    }//end dataGetParameter()


}//end class
