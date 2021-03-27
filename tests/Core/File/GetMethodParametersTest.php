<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getMethodParameters method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class GetMethodParametersTest extends AbstractMethodUnitTest
{


    /**
     * Verify pass-by-reference parsing.
     *
     * @return void
     */
    public function testPassByReference()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => '&$var',
            'pass_by_reference' => true,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPassByReference()


    /**
     * Verify array hint parsing.
     *
     * @return void
     */
    public function testArrayHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'array $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'array',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrayHint()


    /**
     * Verify type hint parsing.
     *
     * @return void
     */
    public function testTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => 'foo $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'foo',
            'nullable_type'     => false,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => 'bar $var2',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'bar',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testTypeHint()


    /**
     * Verify self type hint parsing.
     *
     * @return void
     */
    public function testSelfTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'self $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'self',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testSelfTypeHint()


    /**
     * Verify nullable type hint parsing.
     *
     * @return void
     */
    public function testNullableTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '?int $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int',
            'nullable_type'     => true,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => '?\bar $var2',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?\bar',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNullableTypeHint()


    /**
     * Verify variable.
     *
     * @return void
     */
    public function testVariable()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => '$var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testVariable()


    /**
     * Verify default value parsing with a single function param.
     *
     * @return void
     */
    public function testSingleDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '$var1=self::CONSTANT',
            'default'           => 'self::CONSTANT',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testSingleDefaultValue()


    /**
     * Verify default value parsing.
     *
     * @return void
     */
    public function testDefaultValues()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '$var1=1',
            'default'           => '1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$var2',
            'content'           => "\$var2='value'",
            'default'           => "'value'",
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testDefaultValues()


    /**
     * Verify "bitwise and" in default value !== pass-by-reference.
     *
     * @return void
     */
    public function testBitwiseAndConstantExpressionDefaultValue()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$a',
            'content'           => '$a = 10 & 20',
            'default'           => '10 & 20',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBitwiseAndConstantExpressionDefaultValue()


    /**
     * Verify that arrow functions are supported.
     *
     * @return void
     */
    public function testArrowFunction()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$a',
            'content'           => 'int $a',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'int',
            'nullable_type'     => false,
        ];

        $expected[1] = [
            'name'              => '$b',
            'content'           => '...$b',
            'pass_by_reference' => false,
            'variable_length'   => true,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrowFunction()


    /**
     * Verify recognition of PHP8 mixed type declaration.
     *
     * @return void
     */
    public function testPHP8MixedTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => 'mixed &...$var1',
            'pass_by_reference' => true,
            'variable_length'   => true,
            'type_hint'         => 'mixed',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHint()


    /**
     * Verify recognition of PHP8 mixed type declaration with nullability.
     *
     * @return void
     */
    public function testPHP8MixedTypeHintNullable()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '?Mixed $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?Mixed',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8MixedTypeHintNullable()


    /**
     * Verify recognition of type declarations using the namespace operator.
     *
     * @return void
     */
    public function testNamespaceOperatorTypeHint()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var1',
            'content'           => '?namespace\Name $var1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?namespace\Name',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNamespaceOperatorTypeHint()


    /**
     * Verify recognition of PHP8 union type declaration.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimple()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$number',
            'content'           => 'int|float $number',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'int|float',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$obj',
            'content'           => 'self|parent &...$obj',
            'pass_by_reference' => true,
            'variable_length'   => true,
            'type_hint'         => 'self|parent',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesSimple()


    /**
     * Verify recognition of PHP8 union type declaration when the variable has either a spread operator or a reference.
     *
     * @return void
     */
    public function testPHP8UnionTypesWithSpreadOperatorAndReference()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$paramA',
            'content'           => 'float|null &$paramA',
            'pass_by_reference' => true,
            'variable_length'   => false,
            'type_hint'         => 'float|null',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$paramB',
            'content'           => 'string|int ...$paramB',
            'pass_by_reference' => false,
            'variable_length'   => true,
            'type_hint'         => 'string|int',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesWithSpreadOperatorAndReference()


    /**
     * Verify recognition of PHP8 union type declaration with a bitwise or in the default value.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimpleWithBitwiseOrInDefault()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'int|float $var = CONSTANT_A | CONSTANT_B',
            'default'           => 'CONSTANT_A | CONSTANT_B',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'int|float',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesSimpleWithBitwiseOrInDefault()


    /**
     * Verify recognition of PHP8 union type declaration with two classes.
     *
     * @return void
     */
    public function testPHP8UnionTypesTwoClasses()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'MyClassA|\Package\MyClassB $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'MyClassA|\Package\MyClassB',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesTwoClasses()


    /**
     * Verify recognition of PHP8 union type declaration with all base types.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllBaseTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'array|bool|callable|int|float|null|object|string $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'array|bool|callable|int|float|null|object|string',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesAllBaseTypes()


    /**
     * Verify recognition of PHP8 union type declaration with all pseudo types.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllPseudoTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'false|mixed|self|parent|iterable|Resource $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'false|mixed|self|parent|iterable|Resource',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesAllPseudoTypes()


    /**
     * Verify recognition of PHP8 union type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP8UnionTypesNullable()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$number',
            'content'           => '?int|float $number',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int|float',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8UnionTypesNullable()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type null.
     *
     * @return void
     */
    public function testPHP8PseudoTypeNull()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'null $var = null',
            'default'           => 'null',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'null',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeNull()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type false.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalse()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'false $var = false',
            'default'           => 'false',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'false',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeFalse()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type false combined with type bool.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalseAndBool()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'bool|false $var = false',
            'default'           => 'false',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'bool|false',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeFalseAndBool()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type object combined with a class name.
     *
     * @return void
     */
    public function testPHP8ObjectAndClass()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'object|ClassName $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'object|ClassName',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ObjectAndClass()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) type iterable combined with array/Traversable.
     *
     * @return void
     */
    public function testPHP8PseudoTypeIterableAndArray()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'iterable|array|Traversable $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'iterable|array|Traversable',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8PseudoTypeIterableAndArray()


    /**
     * Verify recognition of PHP8 type declaration with (illegal) duplicate types.
     *
     * @return void
     */
    public function testPHP8DuplicateTypeInUnionWhitespaceAndComment()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'int | string /*comment*/ | INT $var',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'int|string|INT',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8DuplicateTypeInUnionWhitespaceAndComment()


    /**
     * Verify recognition of PHP8 constructor property promotion without type declaration, with defaults.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionNoTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'                => '$x',
            'content'             => 'public $x = 0.0',
            'default'             => '0.0',
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'public',
        ];
        $expected[1] = [
            'name'                => '$y',
            'content'             => 'protected $y = \'\'',
            'default'             => "''",
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'protected',
        ];
        $expected[2] = [
            'name'                => '$z',
            'content'             => 'private $z = null',
            'default'             => 'null',
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'private',
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionNoTypes()


    /**
     * Verify recognition of PHP8 constructor property promotion with type declarations.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionWithTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'                => '$x',
            'content'             => 'protected float|int $x',
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'float|int',
            'nullable_type'       => false,
            'property_visibility' => 'protected',
        ];
        $expected[1] = [
            'name'                => '$y',
            'content'             => 'public ?string &$y = \'test\'',
            'default'             => "'test'",
            'pass_by_reference'   => true,
            'variable_length'     => false,
            'type_hint'           => '?string',
            'nullable_type'       => true,
            'property_visibility' => 'public',
        ];
        $expected[2] = [
            'name'                => '$z',
            'content'             => 'private mixed $z',
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'mixed',
            'nullable_type'       => false,
            'property_visibility' => 'private',
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionWithTypes()


    /**
     * Verify recognition of PHP8 constructor with both property promotion as well as normal parameters.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionAndNormalParam()
    {
        $expected    = [];
        $expected[0] = [
            'name'                => '$promotedProp',
            'content'             => 'public int $promotedProp',
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'int',
            'nullable_type'       => false,
            'property_visibility' => 'public',
        ];
        $expected[1] = [
            'name'              => '$normalArg',
            'content'           => '?int $normalArg',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionAndNormalParam()


    /**
     * Verify behaviour when a non-constructor function uses PHP 8 property promotion syntax.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionGlobalFunction()
    {
        $expected    = [];
        $expected[0] = [
            'name'                => '$x',
            'content'             => 'private $x',
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'private',
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionGlobalFunction()


    /**
     * Verify behaviour when an abstract constructor uses PHP 8 property promotion syntax.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionAbstractMethod()
    {
        $expected    = [];
        $expected[0] = [
            'name'                => '$y',
            'content'             => 'public callable $y',
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'callable',
            'nullable_type'       => false,
            'property_visibility' => 'public',
        ];
        $expected[1] = [
            'name'                => '$x',
            'content'             => 'private ...$x',
            'pass_by_reference'   => false,
            'variable_length'     => true,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'private',
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionAbstractMethod()


    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     *
     * @return void
     */
    private function getMethodParametersTestHelper($commentString, $expected)
    {
        $function = $this->getTargetToken($commentString, [T_FUNCTION, T_CLOSURE, T_FN]);
        $found    = self::$phpcsFile->getMethodParameters($function);

        $this->assertArraySubset($expected, $found, true);

    }//end getMethodParametersTestHelper()


}//end class
