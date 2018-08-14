<?php

// No args.
function myFunction()
{
}

// No default args.
function myFunction($arg1)
{
}

// Valid
function myFunction($arg1, $arg2='hello')
{
}

// Valid with lots of args
function myFunction($arg1, $arg2, $arg3, $arg4='hello', $arg5=array(), $arg6='hello')
{
}

// Valid type hints
function myFunction(array $arg1, array $arg2=array())
{
}

// Invalid
function myFunction($arg2='hello', $arg1)
{
}

// Invalid with lots of args
function myFunction($arg1, $arg2, $arg3, $arg4='hello', $arg5, $arg6='hello')
{
}

// Invalid type hints
function myFunction(array $arg2=array(), array $arg1)
{
}

class myClass
{
    // No args.
    function myFunction()
    {
    }

    // No default args.
    function myFunction($arg1)
    {
    }

    // Valid
    function myFunction($arg1, $arg2='hello')
    {
    }

    // Valid with lots of args
    function myFunction($arg1, $arg2, $arg3, $arg4='hello', $arg5=array(), $arg6='hello')
    {
    }

    // Valid type hints
    function myFunction(array $arg1, array $arg2=array())
    {
    }

    // Invalid
    function myFunction($arg2='hello', $arg1)
    {
    }

    // Invalid with lots of args
    function myFunction($arg1, $arg2, $arg3, $arg4='hello', $arg5, $arg6='hello')
    {
    }

    // Invalid type hints
    function myFunction(array $arg2=array(), array $arg1)
    {
    }
}

function myFunc($req, $opt=null, ...$params) {}

// Type hinting with NULL
function foo(Foo $foo = null, $bar) {}
function foo(Foo $foo, $bar) {}
function foo(Foo $foo = null, $bar = true, $baz) {}
function foo($baz, Foo $foo = null, $bar = true) {}
function foo($baz, $bar = true, Foo $foo = null) {}

// Valid closure
$closure = function ($arg1, $arg2='hello') {};

// Invalid closure
$closure = function(array $arg2=array(), array $arg1) {}
