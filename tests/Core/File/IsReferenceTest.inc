<?php

namespace PHP_CodeSniffer\Tests\Core\File;

/* testBitwiseAndA */
error_reporting( E_NOTICE & E_STRICT );

/* testBitwiseAndB */
$a = [ $something & $somethingElse ];

/* testBitwiseAndC */
$a = [ $first, $something & self::$somethingElse ];

/* testBitwiseAndD */
$a = array( $first, $something & $somethingElse );

/* testBitwiseAndE */
$a = [ 'a' => $first, 'b' => $something & $somethingElse ];

/* testBitwiseAndF */
$a = array( 'a' => $first, 'b' => $something & \MyClass::$somethingElse );

/* testBitwiseAndG */
$a = $something & $somethingElse;

/* testBitwiseAndH */
function myFunction($a = 10 & 20) {}

/* testBitwiseAndI */
$closure = function ($a = MY_CONSTANT & parent::OTHER_CONSTANT) {};

/* testFunctionReturnByReference */
function &myFunction() {}

/* testFunctionPassByReferenceA */
function myFunction( &$a ) {}

/* testFunctionPassByReferenceB */
function myFunction( $a, &$b ) {}

/* testFunctionPassByReferenceC */
$closure = function ( &$a ) {};

/* testFunctionPassByReferenceD */
$closure = function ( $a, &$b ) {};

/* testFunctionPassByReferenceE */
function myFunction(array &$one) {}

/* testFunctionPassByReferenceF */
$closure = function (\MyClass &$one) {};

/* testFunctionPassByReferenceG */
$closure = function ($param, &...$moreParams) {};

/* testForeachValueByReference */
foreach( $array as $key => &$value ) {}

/* testForeachKeyByReference */
foreach( $array as &$key => $value ) {}

/* testArrayValueByReferenceA */
$a = [ 'a' => &$something ];

/* testArrayValueByReferenceB */
$a = [ 'a' => $something, 'b' => &$somethingElse ];

/* testArrayValueByReferenceC */
$a = [ &$something ];

/* testArrayValueByReferenceD */
$a = [ $something, &$somethingElse ];

/* testArrayValueByReferenceE */
$a = array( 'a' => &$something );

/* testArrayValueByReferenceF */
$a = array( 'a' => $something, 'b' => &$somethingElse );

/* testArrayValueByReferenceG */
$a = array( &$something );

/* testArrayValueByReferenceH */
$a = array( $something, &$somethingElse );

/* testAssignByReferenceA */
$b = &$something;

/* testAssignByReferenceB */
$b =& $something;

/* testAssignByReferenceC */
$b .= &$something;

/* testAssignByReferenceD */
$myValue = &$obj->getValue();

/* testAssignByReferenceE */
$collection = &collector();

/* testPassByReferenceA */
functionCall(&$something, $somethingElse);

/* testPassByReferenceB */
functionCall($something, &$somethingElse);

/* testPassByReferenceC */
functionCall($something, &$this->somethingElse);

/* testPassByReferenceD */
functionCall($something, &self::$somethingElse);

/* testPassByReferenceE */
functionCall($something, &parent::$somethingElse);

/* testPassByReferenceF */
functionCall($something, &static::$somethingElse);

/* testPassByReferenceG */
functionCall($something, &SomeClass::$somethingElse);

/* testPassByReferenceH */
functionCall(&\SomeClass::$somethingElse);

/* testPassByReferenceI */
functionCall($something, &\SomeNS\SomeClass::$somethingElse);

/* testPassByReferenceJ */
functionCall($something, &namespace\SomeClass::$somethingElse);

/* testNewByReferenceA */
$foobar2 = &new Foobar();

/* testNewByReferenceB */
functionCall( $something , &new Foobar() );

/* testUseByReference */
$closure = function() use (&$var){};

/* testArrowFunctionReturnByReference */
fn&($x) => $x;

/* testArrowFunctionPassByReferenceA */
$fn = fn(array &$one) => 1;

/* testArrowFunctionPassByReferenceB */
$fn = fn($param, &...$moreParams) => 1;

/* testClosureReturnByReference */
$closure = function &($param) use ($value) {};
