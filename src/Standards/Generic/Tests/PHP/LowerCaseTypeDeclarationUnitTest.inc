<?php

// Ok: No function parameters or no (return) type declaration.
function baz() {}
function bax( $a, $b ) {}
$ok = function($a) {};

/*
 * Correct parameter type declarations.
 * Includes variations for testing sniff functioning independent
 * of code style, with closures and with nullable type hints.
 */
function fooA(array $a) {}
function fooB(callable $a) {}
function fooC  (   bool    $a   ) {}
function fooD(int $a) {}
$a = function (float $a) {};
$b = function (string $a) {};
function fooE(?iterable $a) {}
function fooF(?object $a) {}

// Class/interface type declaration.
function fooG(stdClass $a) {}
function fooH(MyClass $a) {}
function fooI(\MyClass $a) {}
function fooJ(MyNamespace\MyClass $a) {}
function fooK(\MyNamespace\MyClass $a) {}

class MyClass {
    function fooL(self $a) {}
}

// Invalid type declarations - will be treated as class/interface names.
function fooM(boolean $a) {}
function fooN(integer $a) {}

class MyOtherClass extends MyClass {
    function fooO(static $a) {}
}

/*
 * Test wrong case.
 */
function fooAA(Array $a) {}
function fooAB(CALLABLE $a) {}
function fooAC  (    BooL    $a  ) {}
function fooAD(INT $a) {}
$c = function (Float $a) {};
$d = function (String $a) {};
function fooAE(?IteRaBle $a) {}
function fooAF(?Object $a) {}

class MyClass {
    function fooAG(SELF $a) {}
    function fooAH(Parent $a) {}
}


/*
 * PHP 7: correct return type declarations.
 * Includes variations for testing sniff functioning independent
 * of code style, with closures and with nullable type hints.
 */
function barA(): bool {}
function barB():int{}
function barC(): float {}
function barD(): ?string {}
function barE(): array {}
$e = function (): callable {};
function barF(): self {}
function barG(): ?parent {}
function barH(): Baz {}
$f = function (): \Baz {};
function barI(): myNamespace\Baz {}
function barJ(): \myNamespace\Baz {}
function barK()  : iterable   {}
function barL()
    : void {}
function barM(): object {}

/*
 * Test wrong case.
 */
function barA(): Bool {}
function barB():INT{}
function barC(): Float {}
function barD(): ?String {}
function barE(): ARRAY {}
$e = function (): CallAble {};
function barF(): SELF {}
function barG(): ?Parent {}
function barK()  : IterAble   {}
function barL()
    : VOID {}
function barM(): Object {}
