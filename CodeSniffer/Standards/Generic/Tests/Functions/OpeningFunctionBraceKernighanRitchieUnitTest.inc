<?php

// Good.
function myFunction() {
}

// Brace should be on same line.
function myFunction()
{
}

// Too many spaces.
function myFunction()   {
}

// Uses tab.
function myFunction()	{
}


class myClass()
{
    // Good.
    function myFunction() {
    }
    
    // Brace should be on same line.
    function myFunction()
    {
    }
    
    // Too many spaces.
    function myFunction()   {
    }
    
    // Uses tab.
    function myFunction()	{
    }
}



/* Multi-line declarations */

// Good.
function myFunction($variable1, $variable2,
    $variable3, $variable4) {
}

// Brace should be on same line.
function myFunction($variable1, $variable2,
    $variable3, $variable4)
{
}

// Too many spaces.
function myFunction($variable1, $variable2,
    $variable3, $variable4)   {
}

// Uses tab.
function myFunction($variable1, $variable2,
    $variable3, $variable4)	{
}


class myClass()
{
    // Good.
    function myFunction($variable1, $variable2,
        $variable3, $variable4) {
    }
    
    // Brace should be on same line.
    function myFunction($variable1, $variable2,
        $variable3, $variable4)
    {
    }
    
    // Too many spaces.
    function myFunction($variable1, $variable2,
        $variable3, $variable4)   {
    }
    
    // Uses tab.
    function myFunction($variable1, $variable2,
        $variable3, $variable4)	{
    }
}

interface MyInterface()
{
    function myFunction();
}

function myFunction(
                    $arg1,
                    $arg2,
                    $arg3,
                    $arg4,
                    $arg5,
                    $arg6
                    )
{
}

function myFunction(
                    $arg1,
                    $arg2,
                    $arg3,
                    $arg4,
                    $arg5,
                    $arg6
                    ) {
}

?>
