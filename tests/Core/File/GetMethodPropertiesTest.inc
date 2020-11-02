<?php

/* testBasicFunction */
function myFunction() {}

/* testReturnFunction */
function myFunction(array ...$arrays): array
{
    /* testNestedClosure */
    return array_map(function(array $array): int {
        return array_sum($array);
    }, $arrays);
}

class MyClass {
    /* testBasicMethod */
    function myFunction() {}

    /* testPrivateStaticMethod */
    private static function myFunction() {}

    /* testFinalMethod */
    final public function myFunction() {}

    /* testProtectedReturnMethod */
    protected function myFunction() : int {}

    /* testPublicReturnMethod */
    public function myFunction(): array {}

    /* testNullableReturnMethod */
    public function myFunction(): ?array {}

    /* testMessyNullableReturnMethod */
    public function myFunction() /* comment
        */ :  
        /* comment */ ? //comment
        array {}

    /* testReturnNamespace */
    function myFunction(): \MyNamespace\MyClass {}

    /* testReturnMultilineNamespace */
    function myFunction(): \MyNamespace /** comment *\/ comment */
                           \MyClass /* comment */
                           \Foo {}
}

abstract class MyClass
{
    /* testAbstractMethod */
    abstract function myFunction();

    /* testAbstractReturnMethod */
    abstract protected function myFunction(): bool;
}

interface MyInterface
{
    /* testInterfaceMethod */
    function myFunction();
}

$result = array_map(
    /* testArrowFunction */
    static fn(int $number) : int => $number + 1,
    $numbers
);

class ReturnMe {
    /* testReturnTypeStatic */
    private function myFunction(): static {
        return $this;
    }
}

/* testPHP8MixedTypeHint */
function mixedTypeHint() :mixed {}

/* testPHP8MixedTypeHintNullable */
// Intentional fatal error - nullability is not allowed with mixed, but that's not the concern of the method.
function mixedTypeHintNullable(): ?mixed {}

/* testNamespaceOperatorTypeHint */
function namespaceOperatorTypeHint() : ?namespace\Name {}

/* testPHP8UnionTypesSimple */
function unionTypeSimple($number) : int|float {}

/* testPHP8UnionTypesTwoClasses */
$fn = fn($var): MyClassA|\Package\MyClassB => $var;

/* testPHP8UnionTypesAllBaseTypes */
function unionTypesAllBaseTypes() : array|bool|callable|int|float|null|Object|string {}

/* testPHP8UnionTypesAllPseudoTypes */
// Intentional fatal error - mixing types which cannot be combined, but that's not the concern of the method.
function unionTypesAllPseudoTypes($var) : false|MIXED|self|parent|static|iterable|Resource|void {}

/* testPHP8UnionTypesNullable */
// Intentional fatal error - nullability is not allowed with union types, but that's not the concern of the method.
$closure = function () use($a) :?int|float {};

/* testPHP8PseudoTypeNull */
// Intentional fatal error - null pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeNull(): null {}

/* testPHP8PseudoTypeFalse */
// Intentional fatal error - false pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeFalse(): false {}

/* testPHP8PseudoTypeFalseAndBool */
// Intentional fatal error - false pseudotype is not allowed in combination with bool, but that's not the concern of the method.
function pseudoTypeFalseAndBool(): bool|false {}

/* testPHP8ObjectAndClass */
// Intentional fatal error - object is not allowed in combination with class name, but that's not the concern of the method.
function objectAndClass(): object|ClassName {}

/* testPHP8PseudoTypeIterableAndArray */
// Intentional fatal error - iterable pseudotype is not allowed in combination with array or Traversable, but that's not the concern of the method.
interface FooBar {
    public function pseudoTypeIterableAndArray(): iterable|array|Traversable;
}

/* testPHP8DuplicateTypeInUnionWhitespaceAndComment */
// Intentional fatal error - duplicate types are not allowed in union types, but that's not the concern of the method.
function duplicateTypeInUnion(): int | /*comment*/ string | INT {}
