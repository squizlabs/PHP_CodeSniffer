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
     * @param string $varType The type.
     *
     * @dataProvider dataSuggestTypeAllowedType
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType
     *
     * @return void
     */
    public function testSuggestTypeAllowedType($varType)
    {
        $result = Comments::suggestType($varType);
        $this->assertSame($varType, $result);

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
        foreach ($types as $key => $type) {
            $types[$key] = [$type];
        }

        return $types;

    }//end dataSuggestTypeAllowedType()


    /**
     * Test passing one of the allowed types in the wrong case to the suggestType() method.
     *
     * @param string $varType  The type found.
     * @param string $expected Expected suggested type.
     *
     * @dataProvider dataSuggestTypeAllowedTypeWrongCase
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType
     *
     * @return void
     */
    public function testSuggestTypeAllowedTypeWrongCase($varType, $expected)
    {
        $result = Comments::suggestType($varType);
        $this->assertSame($expected, $result);

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
        foreach ($types as $type) {
            $data[] = [
                ucfirst($type),
                $type,
            ];
            $data[] = [
                strtoupper($type),
                $type,
            ];
        }

        return $data;

    }//end dataSuggestTypeAllowedTypeWrongCase()


    /**
     * Test the suggestType() method for all other cases.
     *
     * @param string $varType  The type found.
     * @param string $expected Expected suggested type.
     *
     * @dataProvider dataSuggestTypeOther
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::suggestType
     *
     * @return void
     */
    public function testSuggestTypeOther($varType, $expected)
    {
        $result = Comments::suggestType($varType);
        $this->assertSame($expected, $result);

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
            // Short forms.
            [
                'bool',
                'boolean',
            ],
            [
                'BOOL',
                'boolean',
            ],
            [
                'double',
                'float',
            ],
            [
                'Real',
                'float',
            ],
            [
                'DoUbLe',
                'float',
            ],
            [
                'int',
                'integer',
            ],
            [
                'INT',
                'integer',
            ],

            // Array types.
            [
                'Array()',
                'array',
            ],
            [
                'array(real)',
                'array(float)',
            ],
            [
                'array(int => object)',
                'array(integer => object)',
            ],
            [
                'array(integer => array(string => resource))',
                'array(integer => array(string => resource))',
            ],
            [
                'ARRAY(BOOL => DOUBLE)',
                'array(boolean => float)',
            ],
            [
                'array(string=>resource)',
                'array(string => resource)',
            ],

            // Incomplete array type.
            [
                'array(int =>',
                'array',
            ],

            // Custom types are returned unchanged.
            [
                '<string> => <int>',
                '<string> => <int>',
            ],
            [
                'string[]',
                'string[]',
            ],
        ];

    }//end dataSuggestTypeOther()


}//end class
