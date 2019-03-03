<?php

/* testNotAFunction */
interface NotAFunction {}

/* testFunctionNoParams */
function noParams() {}

/* testPassByReference */
function passByReference(&$var) {}

/* testArrayHint */
function arrayHint(array $var) {}

/* testVariable */
function variable($var) {}

/* testSingleDefaultValue */
function defaultValue($var1=self::CONSTANT) {}

/* testDefaultValues */
function defaultValues($var1=1, $var2='value') {}

/* testArrayDefaultValues */
function arrayDefaultValues($var1 = [], $var2 = array(1, 2, 3) ) {}

/* testConstantDefaultValueSecondParam */
function constantDefaultValueSecondParam($var1, $var2 = M_PI) {}

/* testTypeHint */
function typeHint(foo $var1, bar $var2) {}

class MyClass {
    /* testSelfTypeHint */
    function typeSelfHint(self $var) {}

    /* testCallableTypeHint */
    function callableTypeHint(callable $var) {}

    /* testNullableCallableTypeHint */
    function nullableCallableTypeHint(?callable $var) {}
}

/* testNullableTypeHint */
function nullableTypeHint(?int $var1, ?\bar $var2) {}

/* testIterableTypeHint */
function iterableTypeHint(iterable $var) {}

/* testVariableLengthArgument */
function variableLengthArgument($unit, DateInterval ...$intervals) {}

/* testBitwiseAndConstantExpressionDefaultValue */
function myFunction($a = 10 & 20) {}

/* testFQCNTypeHint */
function FQCNTypeHint(\MyNS\SubCat\MyClass $var) {}

/* testFQCNTypeHintWithCommentsAndWhiteSpace */
function FQCNTypeHintWithComments(
    ?\MyNS /* comment */
        \SubCat // phpcs:ignore Standard.Cat.Sniff -- for reasons.
            \MyClass $var
) {}
