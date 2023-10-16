<?php

if (true && true || true); // Not OK.
if ((true && true) || true);
if (true && (true || true));

$var = true && true || true; // Not OK.
$var = (true && true) || true;
$var = true && (true || true);

$complex = true && (true || true) && true;
$complex = true && (true || true) || true; // Not OK.

if (
    true
    && true
    || true // Not OK.
);

if (
    true
    && (
        true
        || true
    )
);

if (true && foo(true || true));
if (true && foo(true && true || true)); // Not OK.
if (true && $foo[true || true]);
if (true && $foo[true && true || true]); // Not OK.

if (true && foo(true) || true); // Not OK.
if (true && $foo[true] || true); // Not OK.
if (true && foo($foo[true]) || true); // Not OK.

$foo[] = true && true || false; // Not OK.

foo([true && true || false]); // Not OK.

if (true && true || true && true); // Not OK.

$foo = false || true && (#[\Attr(true && true || true)] function (#[\SensitiveParameter] $p) { // Not OK.
    echo true || true && true; // Not OK.

    return true;
})('dummy') || false; // Not OK.

$foo = false || (true && (#[\Attr((true && true) || true)] function (#[\SensitiveParameter] $p) {
    echo (true || true) && true;

    return true;
})('dummy')) || false;

$foo = true || true || (#[\Attr(true && true && true)] function (#[\SensitiveParameter] $p) {
    echo true && true && true;

    return true;
})('dummy') || false;

if (true && [true, callMe(), ${true || true}] || true); // Not OK.
if (true && [true, callMe(), ${true || true}] && true);

for (true || true || true; true && true && true; true || true || true);
for (true || true && true; true && true || true; true || true && true); // Not OK.

for ($a = true || true || true, $b = true && true && true; $a; $b);
for ($a = true || true && true, $b = true || true && true; $a; $b); // Not OK.

$foo = true || true || true ? true && true && true : true || true || true;
$foo = true && true || true // Not OK.
    ? true || true && true // Not OK.
    : true || true && true; // Not OK.

for(true || true || true, true && true && true);
for(true && true || true, true && true || true); // Not OK.

(true && true and true); // Not OK.
(true && true or true); // Not OK.
(true and true or true); // Not OK.
(true and true xor true and true); // Not OK.

if (true || true && true && true && true); // Not OK.

match (true) {
    // OK.
    $a || ($b && $c) => true,
};

match (true) {
    // Not OK.
    $a || $b && $c => true,
};

match (true) {
    // OK.
    $a || $b => true,
    $a && $b => true,
};

match (true) {
    // Debatable.
    $a || $b, $a && $b => true,
};

// OK.
$foo = fn ($a, $b, $c) => $a && ($b || $c);

// Not OK.
$foo = fn ($a, $b, $c) => $a && $b || $c;

// OK.
$foo = $a && (fn ($a, $b, $c) => $a || $b);

// Debatable.
$foo = $a && fn ($a, $b, $c) => $a || $b;

// OK.
\array_map(
    fn ($a, $b, $c) => $a || $b,
    $a && $b
);

match (true) {
    // Not OK.
    $a || ($b && $c) && $d => true,
    // Not OK.
    $b && $c['a'] || $d => true,
    // Not OK.
    $b && ${$var} || $d => true,
};
