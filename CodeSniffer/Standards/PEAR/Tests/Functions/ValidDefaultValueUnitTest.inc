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

class myClass()
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

?>
