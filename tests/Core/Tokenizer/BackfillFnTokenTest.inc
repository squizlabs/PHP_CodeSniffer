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
