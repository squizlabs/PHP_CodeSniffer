<?php

if (true && true || true);
if ((true && true) || true);
if (true && (true || true));

$var = true && true || true;
$var = (true && true) || true;
$var = true && (true || true);

$complex = true && (true || true) && true;
$complex = true && (true || true) || true;

if (
    true
    && true
    || true
);

if (
    true
    && (
        true
        || true
    )
);

if (true && foo(true || true));
if (true && foo(true && true || true));
if (true && $foo[true || true]);
if (true && $foo[true && true || true]);

if (true && foo(true) || true);
if (true && $foo[true] || true);
if (true && foo($foo[true]) || true);

$foo[] = true && true || false;

foo([true && true || false]);

if (true && true || true && true);
