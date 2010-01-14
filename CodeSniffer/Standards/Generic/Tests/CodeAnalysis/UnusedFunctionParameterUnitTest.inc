<?php

function foo($a, $b) {
    return $a * 2;
}

function baz($a, $b) {
    echo "baz({$a});";
}

function bar($a, $b) {
    $x = $b;
    for ($i = 0; $i <$a; $i++) {
        $x += $a * $i;
    }
    return $x;
}

function foobar($a, &$b) {
    return (preg_match('/foo/', $a, $b) !== 0);
}

class Foo {
    function barfoo($a, $b) {
        // Empty body means interface method in many cases.
    }
}

function foo($bar)
{
    print <<<BAZ
    $bar
BAZ
}