<?php

/* testAttribute */
#[Attribute]
class CustomAttribute {}

/* testAttributeWithParams */
#[Attribute(Attribute::TARGET_CLASS)]
class SecondCustomAttribute {}

/* testAttributeWithNamedParam */
#[Attribute(flags: Attribute::TARGET_ALL)]
class AttributeWithParams {
    public function __construct($foo, array $bar) {}
}

/* testAttributeOnFunction */
#[CustomAttribute]
function attribute_on_function_test() {}

/* testAttributeOnFunctionWithParams */
#[AttributeWithParams('foo', bar: ['bar' => 'foobar'])]
function attribute_with_params_on_function_test() {}

/* testAttributeWithShortClosureParameter */
#[AttributeWithParams(static fn ($value) => ! $value)]
function attribute_with_short_closure_param_test() {}

/* testTwoAttributeOnTheSameLine */
#[CustomAttribute] #[AttributeWithParams('foo')]
function two_attribute_on_same_line_test() {}

/* testAttributeAndCommentOnTheSameLine */
#[CustomAttribute] // This is a comment
function attribute_and_line_comment_on_same_line_test() {}

/* testAttributeGrouping */
#[CustomAttribute, AttributeWithParams('foo'), AttributeWithParams('foo', bar: ['bar' => 'foobar'])]
function attribute_grouping_test() {}

/* testAttributeMultiline */
#[
    CustomAttribute,
    AttributeWithParams('foo'),
    AttributeWithParams('foo', bar: ['bar' => 'foobar'])
]
function attribute_multiline_test() {}

/* testAttributeMultilineWithComment */
#[
    CustomAttribute,                // comment
    AttributeWithParams(/* another comment */ 'foo'),
    AttributeWithParams('foo', bar: ['bar' => 'foobar'])
]
function attribute_multiline_with_comment_test() {}

/* testSingleAttributeOnParameter */
function single_attribute_on_parameter_test(#[ParamAttribute] int $param) {}

/* testMultipleAttributesOnParameter */
function multiple_attributes_on_parameter_test(#[ParamAttribute, AttributeWithParams(/* another comment */ 'foo')] int $param) {}

/* testFqcnAttribute */
#[Boo\QualifiedName, \Foo\FullyQualifiedName('foo')]
function fqcn_attrebute_test() {}

/* testNestedAttributes */
#[Boo\QualifiedName(fn (#[AttributeOne('boo')] $value) => (string) $value)]
function nested_attributes_test() {}

/* testMultilineAttributesOnParameter */
function multiline_attributes_on_parameter_test(#[
    AttributeWithParams(
        'foo'
    )
                                                ] int $param) {}

/* testAttributeContainingTextLookingLikeCloseTag */
#[DeprecationReason('reason: <https://some-website/reason?>')]
function attribute_containing_text_looking_like_close_tag() {}

/* testAttributeContainingMultilineTextLookingLikeCloseTag */
#[DeprecationReason(
    'reason: <https://some-website/reason?>'
)]
function attribute_containing_mulitline_text_looking_like_close_tag() {}

/* testInvalidAttribute */
#[ThisIsNotAnAttribute
function invalid_attribute_test() {}
