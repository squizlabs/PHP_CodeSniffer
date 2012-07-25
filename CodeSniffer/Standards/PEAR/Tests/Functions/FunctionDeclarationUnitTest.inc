<?php
public function someFunctionWithAVeryLongName($firstParameter='something',
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

class MyClass
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
                          )
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
    $muchLongerArgument) use (
    $muchLongerVar3) {
    // body
};
?>
