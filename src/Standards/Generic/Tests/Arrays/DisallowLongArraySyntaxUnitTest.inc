<?php
$var = array();
$var = [1,2,3];
$var = array(1,2,3);
echo $var[1];
$foo = array($var[1],$var[2]);
$foo = array(
        1
        2
        3
       );
$var = array/*comment*/(1,2,3);
$var = array;

function foo(array $array) {}

function foo(array $array) use ($foo): array {}

abstract function foo(): array;

abstract function foo(): Foo\Bar;

abstract function foo(): \Foo\Bar;

// The following function has a simulated git conflict for testing.
// This is not a merge conflict - it is a valid test case to test handling of arrays without associated closer.
// Please do not remove.
function test()
    {
        $arr = array(
            'a' => 'a'
<<<<<<< HEAD
            'b' => 'b'
=======
            'c' => 'c'
>>>>>>> master
        );
    }
