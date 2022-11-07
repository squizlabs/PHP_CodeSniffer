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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'array',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testArrayHint()


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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
            'default'           => '1',
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$var2',
            'content'           => "\$var2='value'",
            'has_attributes'    => false,
            'default'           => "'value'",
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testDefaultValues()


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
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'foo',
            'nullable_type'     => false,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => 'bar $var2',
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int',
            'nullable_type'     => true,
        ];

        $expected[1] = [
            'name'              => '$var2',
            'content'           => '?\bar $var2',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?\bar',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNullableTypeHint()


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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'int',
            'nullable_type'     => false,
        ];

        $expected[1] = [
            'name'              => '$b',
            'content'           => '...$b',
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'int|float',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$obj',
            'content'           => 'self|parent &...$obj',
            'has_attributes'    => false,
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
            'has_attributes'    => false,
            'pass_by_reference' => true,
            'variable_length'   => false,
            'type_hint'         => 'float|null',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$paramB',
            'content'           => 'string|int ...$paramB',
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'    => false,
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
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'property_readonly'   => false,
        ];
        $expected[1] = [
            'name'                => '$y',
            'content'             => 'protected $y = \'\'',
            'default'             => "''",
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'protected',
            'property_readonly'   => false,
        ];
        $expected[2] = [
            'name'                => '$z',
            'content'             => 'private $z = null',
            'default'             => 'null',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'property_readonly'   => false,
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
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'float|int',
            'nullable_type'       => false,
            'property_visibility' => 'protected',
            'property_readonly'   => false,
        ];
        $expected[1] = [
            'name'                => '$y',
            'content'             => 'public ?string &$y = \'test\'',
            'default'             => "'test'",
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'variable_length'     => false,
            'type_hint'           => '?string',
            'nullable_type'       => true,
            'property_visibility' => 'public',
            'property_readonly'   => false,
        ];
        $expected[2] = [
            'name'                => '$z',
            'content'             => 'private mixed $z',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'mixed',
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'property_readonly'   => false,
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
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'int',
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'property_readonly'   => false,
        ];
        $expected[1] = [
            'name'              => '$normalArg',
            'content'           => '?int $normalArg',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionAndNormalParam()


    /**
     * Verify recognition of PHP8 constructor with property promotion using PHP 8.1 readonly keyword.
     *
     * @return void
     */
    public function testPHP81ConstructorPropertyPromotionWithReadOnly()
    {
        $expected    = [];
        $expected[0] = [
            'name'                => '$promotedProp',
            'content'             => 'public readonly ?int $promotedProp',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => '?int',
            'nullable_type'       => true,
            'property_visibility' => 'public',
            'property_readonly'   => true,
        ];
        $expected[1] = [
            'name'                => '$promotedToo',
            'content'             => 'readonly private string|bool &$promotedToo',
            'has_attributes'      => false,
            'pass_by_reference'   => true,
            'variable_length'     => false,
            'type_hint'           => 'string|bool',
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'property_readonly'   => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81ConstructorPropertyPromotionWithReadOnly()


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
            'has_attributes'      => false,
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
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'callable',
            'nullable_type'       => false,
            'property_visibility' => 'public',
        ];
        $expected[1] = [
            'name'                => '$x',
            'content'             => 'private ...$x',
            'has_attributes'      => false,
            'pass_by_reference'   => false,
            'variable_length'     => true,
            'type_hint'           => '',
            'nullable_type'       => false,
            'property_visibility' => 'private',
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8ConstructorPropertyPromotionAbstractMethod()


    /**
     * Verify and document behaviour when there are comments within a parameter declaration.
     *
     * @return void
     */
    public function testCommentsInParameter()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$param',
            'content'           => '// Leading comment.
    ?MyClass /*-*/ & /*-*/.../*-*/ $param /*-*/ = /*-*/ \'default value\' . /*-*/ \'second part\' // Trailing comment.',
            'has_attributes'    => false,
            'pass_by_reference' => true,
            'variable_length'   => true,
            'type_hint'         => '?MyClass',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testCommentsInParameter()


    /**
     * Verify behaviour when parameters have attributes attached.
     *
     * @return void
     */
    public function testParameterAttributesInFunctionDeclaration()
    {
        $expected    = [];
        $expected[0] = [
            'name'                => '$constructorPropPromTypedParamSingleAttribute',
            'content'             => '#[\MyExample\MyAttribute] private string $constructorPropPromTypedParamSingleAttribute',
            'has_attributes'      => true,
            'pass_by_reference'   => false,
            'variable_length'     => false,
            'type_hint'           => 'string',
            'nullable_type'       => false,
            'property_visibility' => 'private',
        ];
        $expected[1] = [
            'name'              => '$typedParamSingleAttribute',
            'content'           => '#[MyAttr([1, 2])]
        Type|false
        $typedParamSingleAttribute',
            'has_attributes'    => true,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'Type|false',
            'nullable_type'     => false,
        ];
        $expected[2] = [
            'name'              => '$nullableTypedParamMultiAttribute',
            'content'           => '#[MyAttribute(1234), MyAttribute(5678)] ?int $nullableTypedParamMultiAttribute',
            'has_attributes'    => true,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?int',
            'nullable_type'     => true,
        ];
        $expected[3] = [
            'name'              => '$nonTypedParamTwoAttributes',
            'content'           => '#[WithoutArgument] #[SingleArgument(0)] $nonTypedParamTwoAttributes',
            'has_attributes'    => true,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];
        $expected[4] = [
            'name'              => '$otherParam',
            'content'           => '#[MyAttribute(array("key" => "value"))]
        &...$otherParam',
            'has_attributes'    => true,
            'pass_by_reference' => true,
            'variable_length'   => true,
            'type_hint'         => '',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testParameterAttributesInFunctionDeclaration()


    /**
     * Verify recognition of PHP8.1 intersection type declaration.
     *
     * @return void
     */
    public function testPHP8IntersectionTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$obj1',
            'content'           => 'Foo&Bar $obj1',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'Foo&Bar',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$obj2',
            'content'           => 'Boo&Bar $obj2',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'Boo&Bar',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP8IntersectionTypes()


    /**
     * Verify recognition of PHP8 intersection type declaration when the variable has either a spread operator or a reference.
     *
     * @return void
     */
    public function testPHP81IntersectionTypesWithSpreadOperatorAndReference()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$paramA',
            'content'           => 'Boo&Bar &$paramA',
            'has_attributes'    => false,
            'pass_by_reference' => true,
            'variable_length'   => false,
            'type_hint'         => 'Boo&Bar',
            'nullable_type'     => false,
        ];
        $expected[1] = [
            'name'              => '$paramB',
            'content'           => 'Foo&Bar ...$paramB',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => true,
            'type_hint'         => 'Foo&Bar',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81IntersectionTypesWithSpreadOperatorAndReference()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with more types.
     *
     * @return void
     */
    public function testPHP81MoreIntersectionTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$var',
            'content'           => 'MyClassA&\Package\MyClassB&\Package\MyClassC $var',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'MyClassA&\Package\MyClassB&\Package\MyClassC',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81MoreIntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with illegal simple types.
     *
     * @return void
     */
    public function testPHP81IllegalIntersectionTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$numeric_string',
            'content'           => 'string&int $numeric_string',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => 'string&int',
            'nullable_type'     => false,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81IllegalIntersectionTypes()


    /**
     * Verify recognition of PHP8.1 intersection type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP81NullableIntersectionTypes()
    {
        $expected    = [];
        $expected[0] = [
            'name'              => '$object',
            'content'           => '?Foo&Bar $object',
            'has_attributes'    => false,
            'pass_by_reference' => false,
            'variable_length'   => false,
            'type_hint'         => '?Foo&Bar',
            'nullable_type'     => true,
        ];

        $this->getMethodParametersTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPHP81NullableIntersectionTypes()


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
