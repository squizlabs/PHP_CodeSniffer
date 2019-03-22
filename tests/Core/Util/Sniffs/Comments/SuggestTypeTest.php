<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Comments;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Util\Sniffs\Comments;

class SuggestTypeTest extends TestCase
{


    /**
     * Test passing an empty type to the suggestType() method.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType
     *
     * @return void
     */
    public function testSuggestTypeEmpty()
    {
        $this->assertSame('', Comments::suggestType(''));

    }//end testSuggestTypeEmpty()


    /**
     * Test passing one of the allowed types to the suggestType() method.
     *
     * @param string $varType       The type.
     * @param string $expectedLong  Expected suggested long-form type.
     * @param string $expectedShort Expected suggested short-form type.
     *
     * @dataProvider dataSuggestTypeAllowedType
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType
     *
     * @return void
     */
    public function testSuggestTypeAllowedType($varType, $expectedLong, $expectedShort)
    {
        $result = Comments::suggestType($varType, 'long');
        $this->assertSame($expectedLong, $result);

        $result = Comments::suggestType($varType, 'short');
        $this->assertSame($expectedShort, $result);

    }//end testSuggestTypeAllowedType()


    /**
     * Data provider.
     *
     * @see testSuggestTypeAllowedType()
     *
     * @return array
     */
    public function dataSuggestTypeAllowedType()
    {
        $types = Comments::$allowedTypes;
        $data  = [];
        foreach ($types as $short => $long) {
            $data[$long] = [
                'input' => $short,
                'long'  => $long,
                'short' => $short,
            ];
        }

        // Add tests for input being long form.
        $data['int']  = [
            'input' => 'integer',
            'long'  => 'integer',
            'short' => 'int',
        ];
        $data['bool'] = [
            'input' => 'boolean',
            'long'  => 'boolean',
            'short' => 'bool',
        ];

        return $data;

    }//end dataSuggestTypeAllowedType()


    /**
     * Test passing one of the allowed types in the wrong case to the suggestType() method.
     *
     * @param string $varType       The type.
     * @param string $expectedLong  Expected suggested long-form type.
     * @param string $expectedShort Expected suggested short-form type.
     *
     * @dataProvider dataSuggestTypeAllowedTypeWrongCase
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType
     *
     * @return void
     */
    public function testSuggestTypeAllowedTypeWrongCase($varType, $expectedLong, $expectedShort)
    {
        $this->testSuggestTypeAllowedType($varType, $expectedLong, $expectedShort);

    }//end testSuggestTypeAllowedTypeWrongCase()


    /**
     * Data provider.
     *
     * @see testSuggestTypeAllowedTypeWrongCase()
     *
     * @return array
     */
    public function dataSuggestTypeAllowedTypeWrongCase()
    {
        $types = Comments::$allowedTypes;
        $data  = [];
        foreach ($types as $short => $long) {
            $data[] = [
                'input' => ucfirst($short),
                'long'  => $long,
                'short' => $short,
            ];
            $data[] = [
                'input' => strtoupper($short),
                'long'  => $long,
                'short' => $short,
            ];
        }

        // Add tests for input being long form in non-lowercase.
        $data[] = [
            'input' => 'Integer',
            'long'  => 'integer',
            'short' => 'int',
        ];
        $data[] = [
            'input' => 'INTEGER',
            'long'  => 'integer',
            'short' => 'int',
        ];
        $data[] = [
            'input' => 'Boolean',
            'long'  => 'boolean',
            'short' => 'bool',
        ];
        $data[] = [
            'input' => 'BOOLEAN',
            'long'  => 'boolean',
            'short' => 'bool',
        ];

        return $data;

    }//end dataSuggestTypeAllowedTypeWrongCase()


    /**
     * Test the suggestType() method for all other cases.
     *
     * @param string $varType       The type found.
     * @param string $expectedLong  Expected suggested long-form type.
     * @param string $expectedShort Expected suggested short-form type.
     *
     * @dataProvider dataSuggestTypeOther
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType
     *
     * @return void
     */
    public function testSuggestTypeOther($varType, $expectedLong, $expectedShort)
    {
        $result = Comments::suggestType($varType, 'long');
        $this->assertSame($expectedLong, $result);

        $result = Comments::suggestType($varType, 'short');
        $this->assertSame($expectedShort, $result);

    }//end testSuggestTypeOther()


    /**
     * Data provider.
     *
     * @see testSuggestTypeOther()
     *
     * @return array
     */
    public function dataSuggestTypeOther()
    {
        return [
            // Wrong form.
            [
                'input' => 'double',
                'long'  => 'float',
                'short' => 'float',
            ],
            [
                'input' => 'Real',
                'long'  => 'float',
                'short' => 'float',
            ],
            [
                'input' => 'DoUbLe',
                'long'  => 'float',
                'short' => 'float',
            ],

            // Array types.
            [
                'input' => 'Array()',
                'long'  => 'array',
                'short' => 'array',
            ],
            [
                'input' => 'array(real)',
                'long'  => 'array(float)',
                'short' => 'array(float)',
            ],
            [
                'input' => 'array(int => object)',
                'long'  => 'array(integer => object)',
                'short' => 'array(int => object)',
            ],
            [
                'input' => 'array(integer => object)',
                'long'  => 'array(integer => object)',
                'short' => 'array(int => object)',
            ],
            [
                'input' => 'array(integer => array(string => resource))',
                'long'  => 'array(integer => array(string => resource))',
                'short' => 'array(int => array(string => resource))',
            ],
            [
                'input' => 'ARRAY(BOOL => DOUBLE)',
                'long'  => 'array(boolean => float)',
                'short' => 'array(bool => float)',
            ],
            [
                'input' => 'array(string=>resource)',
                'long'  => 'array(string => resource)',
                'short' => 'array(string => resource)',
            ],
            [
                'input' => 'ARRAY(   BOOLEAN    =>    Real   )',
                'long'  => 'array(boolean => float)',
                'short' => 'array(bool => float)',
            ],
            [
                'input' => 'array(string => string|null)',
                'long'  => 'array(string => string|null)',
                'short' => 'array(string => string|null)',
            ],
            [
                'input' => 'array(integer|string => int||null)',
                'long'  => 'array(integer|string => integer|null)',
                'short' => 'array(int|string => int|null)',
            ],

            // Arrays with <> brackets.
            [
                'input' => 'Array<>',
                'long'  => 'array',
                'short' => 'array',
            ],
            [
                'input' => 'array<real>',
                'long'  => 'array<float>',
                'short' => 'array<float>',
            ],
            [
                'input' => 'array<int, object>',
                'long'  => 'array<integer, object>',
                'short' => 'array<int, object>',
            ],
            [
                'input' => 'array<integer, resource>',
                'long'  => 'array<integer, resource>',
                'short' => 'array<int, resource>',
            ],
            [
                'input' => 'array<string, array<int, ClassMetadata>>',
                'long'  => 'array<string, array<integer, ClassMetadata>>',
                'short' => 'array<string, array<int, ClassMetadata>>',
            ],
            [
                'input' => 'ARRAY<BOOL, DOUBLE>',
                'long'  => 'array<boolean, float>',
                'short' => 'array<bool, float>',
            ],
            [
                'input' => 'ARRAY<  BOOLEAN  ,  Real  >',
                'long'  => 'array<boolean, float>',
                'short' => 'array<bool, float>',
            ],
            [
                'input' => 'array<string, string|null>',
                'long'  => 'array<string, string|null>',
                'short' => 'array<string, string|null>',
            ],
            [
                'input' => 'array<integer||string, int|null>',
                'long'  => 'array<integer|string, integer|null>',
                'short' => 'array<int|string, int|null>',
            ],

            // Incomplete array type.
            [
                'input' => 'array(int =>',
                'long'  => 'array',
                'short' => 'array',
            ],

            // Custom types are returned unchanged.
            [
                'input' => '<string> => <int>',
                'long'  => '<string> => <int>',
                'short' => '<string> => <int>',
            ],
            [
                'input' => '[]',
                'long'  => '[]',
                'short' => '[]',
            ],

            // Single type PSR-5 style arrays.
            [
                'input' => 'string[]',
                'long'  => 'string[]',
                'short' => 'string[]',
            ],
            [
                'input' => 'BOOLEAN[]',
                'long'  => 'boolean[]',
                'short' => 'bool[]',
            ],
            [
                'input' => 'int[]',
                'long'  => 'integer[]',
                'short' => 'int[]',
            ],
            [
                'input' => 'double[]',
                'long'  => 'float[]',
                'short' => 'float[]',
            ],

            // Multi-type PSR-5 style arrays.
            [
                'input' => '(string|null)[]',
                'long'  => '(string|null)[]',
                'short' => '(string|null)[]',
            ],
            [
                'input' => '(Int|False)[]',
                'long'  => '(integer|false)[]',
                'short' => '(int|false)[]',
            ],
            [
                'input' => '(real|TRUE|resource)[]',
                'long'  => '(float|true|resource)[]',
                'short' => '(float|true|resource)[]',
            ],

            // Multi-type PSR-5 style array with duplicate `|`.
            [
                'input' => '(string||null)[]',
                'long'  => '(string|null)[]',
                'short' => '(string|null)[]',
            ],
        ];

    }//end dataSuggestTypeOther()


}//end class
