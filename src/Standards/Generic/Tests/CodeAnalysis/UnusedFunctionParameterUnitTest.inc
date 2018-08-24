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

    function fooBar($a, $b) {
        return;
    }
}

function foo($bar)
{
    print <<<BAZ
    $bar
BAZ;
}

function foo( $parameter ) {
    $wango = <<<HERE
1 Must be a HEREdoc of at least one line
HERE;
    $x = $parameter; // This line must be immediately after the HERE; with no intervening blank lines.
    $tango = <<<HERE
1 Must be a HEREdoc
2 
3 
4 
5   
6
7
8 
9 at least 9 lines long
HERE;
}

function foo( $parameter ) {
    return <<<HTML
<?xml version="1.0"?>
<value>$parameter</value>
HTML;
}

print foo( 'PARAMETER' );
print "\n";

function foo($bar)
{
    print "${bar} things\n";
}

function bar($x)
{
    return 2 * ${x};
}

$foo = function ($a, $b) {
    return $a * 2;
};
