<?php

/* testStandard */
$fn1 = fn($x) => $x + $y;

/* testMixedCase */
$fn1 = Fn($x) => $x + $y;

/* testWhitespace */
$fn1 = fn ($x) => $x + $y;

/* testComment */
$fn1 = fn /* comment here */ ($x) => $x + $y;

/* testFunctionName */
function fn() {}

/* testNestedOuter */
$fn = fn($x) => /* testNestedInner */ fn($y) => $x * $y + $z;

/* testFunctionCall */
$extended = fn($c) => $callable($factory($c), $c);

/* testChainedFunctionCall */
$result = Collection::from([1, 2])
    ->map(fn($v) => $v * 2)
    ->reduce(/* testFunctionArgument */ fn($tmp, $v) => $tmp + $v, 0);

/* testClosure */
$extended = fn($c) => $callable(function() {
    for ($x = 1; $x < 10; $x++) {
        echo $x;
    }

    echo 'done';
}, $c);

$result = array_map(
    /* testReturnType */
    static fn(int $number) : int => $number + 1,
    $numbers
);

/* testReference */
fn&($x) => $x;

/* testGrouped */
(fn($x) => $x) + $y;

/* testArrayValue */
$a = [
    'a' => fn() => return 1,
];

/* testYield */
$a = fn($x) => yield 'k' => $x;

/* testNullableNamespace */
$a = fn(?\DateTime $x) : ?\DateTime => $x;

/* testSelfReturnType */
fn(self $a) : self => $a;

/* testParentReturnType */
fn(parent $a) : parent => $a;

/* testCallableReturnType */
fn(callable $a) : callable => $a;

/* testArrayReturnType */
fn(array $a) : array => $a;

/* testTernary */
$fn = fn($a) => $a ? /* testTernaryThen */ fn() : string => 'a' : /* testTernaryElse */ fn() : string => 'b';

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
$fn = fn
