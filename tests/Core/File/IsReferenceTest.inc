<?php
/* @codingStandardsIgnoreFile */

namespace PHP_CodeSniffer\Tests\Core\File;

/* bitwiseAndA */
error_reporting( E_NOTICE & E_STRICT );

/* bitwiseAndB */
$a = [ $something & $somethingElse ];

/* bitwiseAndC */
$a = [ $first, $something & self::$somethingElse ];

/* bitwiseAndD */
$a = array $first, $something & $somethingElse );

/* bitwiseAndE */
$a = [ 'a' => $first, 'b' => $something & $somethingElse ];

/* bitwiseAndF */
$a = array( 'a' => $first, 'b' => $something & \MyClass::$somethingElse );

/* bitwiseAndG */
$a = $something & $somethingElse;

/* bitwiseAndH */
function myFunction($a = 10 & 20) {}

/* bitwiseAndI */
$closure = function ($a = MY_CONSTANT & parent::OTHER_CONSTANT) {};

/* functionReturnByReference */
function &myFunction() {}

/* functionPassByReferenceA */
function myFunction( &$a ) {}

/* functionPassByReferenceB */
function myFunction( $a, &$b ) {}

/* functionPassByReferenceC */
$closure = function ( &$a ) {};

/* functionPassByReferenceD */
$closure = function ( $a, &$b ) {};

/* functionPassByReferenceE */
function myFunction(array &$one) {}

/* functionPassByReferenceF */
$closure = function (\MyClass &$one) {};

/* functionPassByReferenceG */
$closure = function myFunc($param, &...$moreParams) {};

/* foreachValueByReference */
foreach( $array as $key => &$value ) {}

/* foreachKeyByReference */
foreach( $array as &$key => $value ) {}

/* arrayValueByReferenceA */
$a = [ 'a' => &$something ];

/* arrayValueByReferenceB */
$a = [ 'a' => $something, 'b' => &$somethingElse ];

/* arrayValueByReferenceC */
$a = [ &$something ];

/* arrayValueByReferenceD */
$a = [ $something, &$somethingElse ];

/* arrayValueByReferenceE */
$a = array( 'a' => &$something );

/* arrayValueByReferenceF */
$a = array( 'a' => $something, 'b' => &$somethingElse );

/* arrayValueByReferenceG */
$a = array( &$something );

/* arrayValueByReferenceH */
$a = array( $something, &$somethingElse );

/* assignByReferenceA */
$b = &$something;

/* assignByReferenceB */
$b =& $something;

/* assignByReferenceC */
$b .= &$something;

/* assignByReferenceD */
$myValue = &$obj->getValue();

/* assignByReferenceE */
$collection = &collector();

/* passByReferenceA */
functionCall(&$something, $somethingElse);

/* passByReferenceB */
functionCall($something, &$somethingElse);

/* passByReferenceC */
functionCall($something, &$this->somethingElse);

/* passByReferenceD */
functionCall($something, &self::$somethingElse);

/* passByReferenceE */
functionCall($something, &parent::$somethingElse);

/* passByReferenceF */
functionCall($something, &static::$somethingElse);

/* passByReferenceG */
functionCall($something, &SomeClass::$somethingElse);

/* passByReferenceH */
functionCall(&\SomeClass::$somethingElse);

/* passByReferenceI */
functionCall($something, &\SomeNS\SomeClass::$somethingElse);

/* passByReferenceJ */
functionCall($something, &namespace\SomeClass::$somethingElse);

/* newByReferenceA */
$foobar2 = &new Foobar();

/* newByReferenceB */
functionCall( $something , &new Foobar() );

/* useByReference */
$closure = function() use (&$var){};
