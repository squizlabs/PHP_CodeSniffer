<?php
function myFunc() {}
function myFunc( ) {}
function myFunc($blah) {}
function myFunc( $blah ) {}
function myFunc($blah, $blah2, $blah3) {}
function myFunc($blah , $blah2, $blah3 ) {}
function myFunc($blah,$blah2,$blah3) {}
function myFunc($blah,   $blah2,  $blah3) {}
function myFunc($blah='hello') {}
function myFunc($blah = 'hello') {}
function myFunc(PHP_CodeSniffer $object, array $array=array(), $blah3='yo') {}
function myFunc(  $blah='hello'   , $blah2=   'hi',  $blah3   = 'yo' ) {}
function myFunc(PHP_CodeSniffer   $object, array  $array=array(), $blah3='yo') {}
function myFunc( PHP_CodeSniffer $object,  array $array=array(), $blah3='yo') {}
function myFunc( array  &$one,  array  &$two) {}
function myFunc(&$blah) {}
function myFunc( &$blah ) {}

function multiLineFunction(
    array $flatList,
    $markup,
    array $otherList,
    $lastOffset=0
) {
}

function multiLineFunction(
    $markup,
    array $otherList,
    $lastOffset=0
) {
}

$noArgs_longVars = function ($longVar1,  $longerVar2= false) use (
    $longVar1 ,  $longerVar2= false,
    $muchLongerVar3
)  {
    // body
};

// @codingStandardsChangeSetting Squiz.Functions.FunctionDeclarationArgumentSpacing equalsSpacing 1
function myFunc($blah = 'hello') {}
function myFunc($blah  =  'hello') {}
function myFunc($blah =  'hello') {}
function myFunc($blah  = 'hello') {}
// @codingStandardsChangeSetting Squiz.Functions.FunctionDeclarationArgumentSpacing equalsSpacing 0

// @codingStandardsChangeSetting Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesAfterOpen 1
// @codingStandardsChangeSetting Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesBeforeClose 1
function myFunc($blah) {}
function myFunc( $blah ) {}
function myFunc(  $blah  ) {}
function myFunc( array $blah ) {}
function myFunc(array $blah ) {}
function myFunc() {}
function myFunc( ) {}
// @codingStandardsChangeSetting Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesAfterOpen 0
// @codingStandardsChangeSetting Squiz.Functions.FunctionDeclarationArgumentSpacing requiredSpacesBeforeClose 0
