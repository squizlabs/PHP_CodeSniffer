<?php
function someFunctionWithAVeryLongName($firstParameter='something',
   $secondParameter='booooo', $third=null, $fourthParameter=false,
     $fifthParameter=123.12, $sixthParam=true
){
}

function someFunctionWithAVeryLongName2($firstParameter='something',
$secondParameter='booooo', $third=null, $fourthParameter=false,
$fifthParameter=123.12, $sixthParam=true
)  {
}

function blah() {
}

function  blah()
{
}

abstract class MyClass
{

    public function someFunctionWithAVeryLongName($firstParameter='something',
        $secondParameter='booooo', $third=null, $fourthParameter=false,
        $fifthParameter=123.12, $sixthParam=true
    ) /** w00t */ {
    }

    public function someFunctionWithAVeryLongName2(
        $firstParameter='something', $secondParameter='booooo', $third=null
    ) {
    }

    protected abstract function processTokenWithinScope(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $currScope
    );

    protected abstract function processToken(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $currScope);

}

function getInstalledStandards(
    $includeGeneric=false,
    $standardsDir=''
)
{
}

function &testFunction($arg1,
    $arg2,
) {
}

function testFunction($arg1,
$arg2) {
}

function validateUrl(
    $url,
    $requireScheme=TRUE,
    array $allowedSchemes=array(
                           'http',
                           'https',
                          ),
    array $notAllowedSchemes=array('ftp', 'sftp')
) {
}

function validateUrlShort(
    $url,
    $requireScheme=TRUE,
    array $allowedSchemes=[
        'http',
        'https',
    ],
    array $notAllowedSchemes=['ftp', 'sftp']
) {
}

$noArgs_longVars = function () use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};

$longArgs_longVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
) use  (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};

$longArgs_longVars = function (
    $longArgument,
    $longerArgument,
    $muchLongerArgument
    )  use (
    $longVar1,
    $longerVar2,
    $muchLongerVar3
) {
    // body
};

$longArgs_longVars = function (
    $longArgument,
    $muchLongerArgument)use(
    $muchLongerVar3) {
    // body
};

function test()
{
    $longArgs_longVars = function (
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    ) use (
        $longVar1,
        $longerVar2,
        $muchLongerVar3
    ) {
        // body
    };
}

function
    myFunction()
{
}

function
myFunction()
{
}


use function foo\bar;

use
    function bar\baz;

namespace {
    use function Name\Space\f;
    f();
}

$var = function() {return true;};
$var = function() {return true;
};
function blah(){return true;
}

$closureWithArgsAndVars = function($arg1, $arg2) use ($var1, $var2){
    // body
};

function
blah
()
{
    // body
}

$b = function &() {
    echo "hello";
};

function foo(
    $param1,
    $param2,
    $param3
) : SomeClass {
}

function foo(
    $param1,
    $param2,
    $param3
): SomeClass {
}

function foo(
    $param1,
    $param2,
    $param3
): SomeClass // Comment here
{
}

function foo(
    $param1,

    $param2,

    $param3
) : SomeClass {
}

function foo(
    $var
)
{
    // body
}

function foo(
    $var
)
/* hello */ {
    // body
}

function foo(
    $var
)
{ echo 'hi';
    // body
}

function foo(
    $var
)
/* hello */ { echo 'hi';
    // body
}

$a = function () {

function foo ()
{}

abstract class Foo {
    function bar ()
    {
    }

    abstract function baz () ;

    abstract function qux () : void ;
}

interface Foo {
    function bar () ;

    function baz (
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    ) ;

    function qux (
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    ) : void ;
}

trait Foo {
    function bar ()
    {
    }

    abstract function baz ()
    ;
}

if(true) {
    abstract class Foo {
        function bar ()
        {
        }

        abstract function baz () ;

        abstract function qux () : void ;
    }

    interface Foo {
        function bar () ;

        function baz (
            $longArgument,
            $longerArgument,
            $muchLongerArgument
        ) ;

        function qux (
            $longArgument,
            $longerArgument,
            $muchLongerArgument
        ) : void ;
    }

    trait Foo {
        function bar ()
        {
        }

        abstract function baz ()
        ;
    }
}
