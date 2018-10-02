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


class myClass
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


class myClass
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

interface MyInterface
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

function myFunction() {}
function myFunction()
{}

// phpcs:set Generic.Functions.OpeningFunctionBraceKernighanRitchie checkClosures 1

$closureWithArgs = function ($arg1, $arg2){
    // body
};

$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2){
    // body
};

$test = function ($param) use ($result)
{
    return null;
};

$test = function ($param) use ($result) : Something
{
    return null;
};

$test = function ($param) use ($result): Something
{
    return null;
};

foo(function ($bar) { ?>
    <div><?php echo $bar; ?></div>
<?php });

// phpcs:set Generic.Functions.OpeningFunctionBraceKernighanRitchie checkClosures 0

$closureWithArgs = function ($arg1, $arg2){
    // body
};

function myFunction() : Something
{
    return null;
}

function myFunction() : Something // Break me
{
    return null;
}

function myFunction(): Something {
    return null;
}

function myFunction(): Something
{
    return null;
}

function myFunction($bar) { ?>
    <div><?php echo $bar; ?></div>
<?php }

function myFunction($a, $lot, $of, $params)
    : array
{
    return null;
}

function myFunction($a, $lot, $of, $params)
    : array {
    return null;
}

function myFunction($a, $lot, $of, $params) // comment
{
    return null;
}

function myFunction($a, $lot, $of, $params)
    : array // comment
{
    return null;
}

function myFunction($a, $lot, $of, $params)
    : array // phpcs:ignore Standard.Category.Sniff -- for reasons.
{
    return null;
}

function myFunction($a, $lot, $of, $params)
    : array { // phpcs:ignore Standard.Category.Sniff -- for reasons.
    return null;
}
