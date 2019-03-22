<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Comments::suggestTypeString() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Comments;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Util\Sniffs\Comments;

class SuggestTypeStringTest extends TestCase
{


    /**
     * Test the suggestTypeString() method.
     *
     * @param string $typeString    The complete variable type string to process.
     * @param string $expectedLong  Expected suggested long-form type.
     * @param string $expectedShort Expected suggested short-form type.
     *
     * @dataProvider dataSuggestTypeString
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::suggestTypeString
     *
     * @return void
     */
    public function testSuggestTypeString($typeString, $expectedLong, $expectedShort)
    {
        $result = Comments::suggestTypeString($typeString, 'long');
        $this->assertSame($expectedLong, $result);

        $result = Comments::suggestTypeString($typeString, 'short');
        $this->assertSame($expectedShort, $result);

    }//end testSuggestTypeString()


    /**
     * Data provider.
     *
     * @see testSuggestTypeString()
     *
     * @return array
     */
    public function dataSuggestTypeString()
    {
        return [
            // Simple, singular type.
            [
                'input' => 'DoUbLe',
                'long'  => 'float',
                'short' => 'float',
            ],
            [
                'input' => 'BOOLEAN[]',
                'long'  => 'boolean[]',
                'short' => 'bool[]',
            ],
            [
                'input' => 'array(real)',
                'long'  => 'array(float)',
                'short' => 'array(float)',
            ],
            [
                'input' => 'array<int, object>',
                'long'  => 'array<integer, object>',
                'short' => 'array<int, object>',
            ],
            [
                'input' => '(Int|False)[]',
                'long'  => '(integer|false)[]',
                'short' => '(int|false)[]',
            ],

            // Slightly more complex array types.
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

            // Union types.
            [
                'input' => 'int|real',
                'long'  => 'integer|float',
                'short' => 'int|float',
            ],
            [
                'input' => 'NULL|MIXED|RESOURCE',
                'long'  => 'null|mixed|resource',
                'short' => 'null|mixed|resource',
            ],
            [
                'input' => 'NULL|(int|False)[]',
                'long'  => 'null|(integer|false)[]',
                'short' => 'null|(int|false)[]',
            ],
            [
                'input' => '\ArrayObject|\DateTime[]',
                'long'  => '\ArrayObject|\DateTime[]',
                'short' => '\ArrayObject|\DateTime[]',
            ],
            [
                'input' => 'NULL|array(int => object)',
                'long'  => 'null|array(integer => object)',
                'short' => 'null|array(int => object)',
            ],
            [
                'input' => 'array(int => object)|NULL',
                'long'  => 'array(integer => object)|null',
                'short' => 'array(int => object)|null',
            ],
            [
                'input' => 'NULL|array<int, object>',
                'long'  => 'null|array<integer, object>',
                'short' => 'null|array<int, object>',
            ],
            [
                'input' => 'array<int, object>|NULL',
                'long'  => 'array<integer, object>|null',
                'short' => 'array<int, object>|null',
            ],

            // Intersect types.
            [
                'input' => '\MyClass&\PHPUnit\Framework\MockObject\MockObject',
                'long'  => '\MyClass&\PHPUnit\Framework\MockObject\MockObject',
                'short' => '\MyClass&\PHPUnit\Framework\MockObject\MockObject',
            ],

            // Mixed union and intersect types.
            [
                'input' => 'NULL|(int|False)[]|\MyClass&\PHPUnit\Framework\MockObject\MockObject',
                'long'  => 'null|(integer|false)[]|\MyClass&\PHPUnit\Framework\MockObject\MockObject',
                'short' => 'null|(int|false)[]|\MyClass&\PHPUnit\Framework\MockObject\MockObject',
            ],

            // Simple union types with duplicates.
            [
                'input' => 'int|integer',
                'long'  => 'integer',
                'short' => 'int',
            ],
            [
                'input' => 'bool|null|boolean|null',
                'long'  => 'boolean|null',
                'short' => 'bool|null',
            ],

            // Simple union type with duplicate `|`.
            [
                'input' => 'int||false',
                'long'  => 'integer|false',
                'short' => 'int|false',
            ],

            // Combining PSR-5 style single/multi-type arrays.
            [
                'input' => 'int[]|real[]|string[]',
                'long'  => '(integer|float|string)[]',
                'short' => '(int|float|string)[]',
            ],
            [
                'input' => '\ClassName[]|\Package\AnotherClass[]',
                'long'  => '(\ClassName|\Package\AnotherClass)[]',
                'short' => '(\ClassName|\Package\AnotherClass)[]',
            ],
            [
                'input' => '(int|\ClassName)[]|float[]|null',
                'long'  => '(integer|\ClassName|float)[]|null',
                'short' => '(int|\ClassName|float)[]|null',
            ],
            [
                'input' => 'null|(float|int)[]|\ClassName[]',
                'long'  => 'null|(float|integer|\ClassName)[]',
                'short' => 'null|(float|int|\ClassName)[]',
            ],
            [
                'input' => 'null|\ClassName[]|(real|int)[]',
                'long'  => 'null|(\ClassName|float|integer)[]',
                'short' => 'null|(\ClassName|float|int)[]',
            ],

            // Combining single/multi-type arrays with duplicates.
            [
                'input' => 'int[]|integer[]|false',
                'long'  => 'integer[]|false',
                'short' => 'int[]|false',
            ],
            [
                'input' => '(int|\ClassName)[]|integer[]|null',
                'long'  => '(integer|\ClassName)[]|null',
                'short' => '(int|\ClassName)[]|null',
            ],
            [
                'input' => '(int|\ClassName)[]|(float|int)[]',
                'long'  => '(integer|\ClassName|float)[]',
                'short' => '(int|\ClassName|float)[]',
            ],

            // Combining single/multi-type arrays with duplicates and duplicate `|`.
            [
                'input' => '(int||\ClassName)[]|(float||int)[]',
                'long'  => '(integer|\ClassName|float)[]',
                'short' => '(int|\ClassName|float)[]',
            ],

            // Combining single/multi-type arrays with duplicates in the array and the simple types.
            [
                'input' => 'int|(int|\ClassName)[]|integer[]|integer',
                'long'  => 'integer|(integer|\ClassName)[]',
                'short' => 'int|(int|\ClassName)[]',
            ],

            // Intersect types with duplicates.
            [
                'input' => '\MyClass&\YourClass&\AnotherClass&\MyClass',
                'long'  => '\MyClass&\YourClass&\AnotherClass',
                'short' => '\MyClass&\YourClass&\AnotherClass',
            ],

            // Intersect type with duplicate `&`.
            [
                'input' => '\MyClass&&\YourClass',
                'long'  => '\MyClass&\YourClass',
                'short' => '\MyClass&\YourClass',
            ],

            // Nullable types.
            [
                'input' => '?int|?real',
                'long'  => '?integer|?float',
                'short' => '?int|?float',
            ],
            [
                'input' => '?int|?string|null',
                'long'  => 'integer|string|null',
                'short' => 'int|string|null',
            ],
        ];

    }//end dataSuggestTypeString()


}//end class
