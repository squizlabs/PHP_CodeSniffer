<?php

/* testNotAnAcceptedToken */
interface NotAFunctionCallOrArray {}

class Foo {
    public function Bar() {
        /* testNotACallToConstructor */
        $a = self::some_method();
    }
}

/* testNotAVariableFunctionCall */
if ($a === $b) {}

/* testNotAClosureDeclarationWithCall1 */
(new MyClass)->methodName();

// Function calls: no parameters.

/* testNoParamsFunctionCall1 */
some_function();

/* testNoParamsFunctionCall2 */
some_function(     );

/* testNoParamsFunctionCall3 */
some_function( /*nothing here*/ );

/* testNoParamsFunctionCall4 */
$closure(/*nothing here*/);

// Function calls: has parameters.

/* testHasParamsFunctionCall1 */
some_function( 1 );

/* testHasParamsFunctionCall2 */
$closure(1,2,3);

class Bar {
    public static function getInstance() {
        /* testHasParamsFunctionCall3 */
        return new self(true);
    }
}

/* testHasParamsFunctionCall4 */
${$functionname}(1,2,3);

/* testHasParamsFunctionCall5 */
$obj->{$var}('foo');

/* testHasParamsFunctionCall6 */
$this->{self::$methodname}([]);

/* testHasParamsFunctionCall7 */
${$this->functionname}(array(1));

/* testHasParamsFunctionCall8 */
${FUNCTION_NAME}(false);

$closureCall = (function ($a, $b) {
	/* testHasParamsFunctionCall9 */
	})('a','b');

/* testNotAClosureDeclarationWithCall2 */
(function ($a, $b) {
    /* testNotAClosureDeclarationWithCall3 */
    return function ($c, $d)
        /* testNotAClosureDeclarationWithCall4 */
	    use ($a, $b)
	{
        echo $a, $b, $c, $d;
    };
/* testHasParamsFunctionCall10 */
})
    /* testHasParamsFunctionCall11 */
    ('a','b')
    /* testNotAClosureDeclarationWithCall5 */
	('c','d');

// Arrays: no parameters.

/* testNoParamsLongArray1 */
$foo = array();

/* testNoParamsLongArray2 */
$foo = array(     );

/* testNoParamsLongArray3 */
$foo = array( /*nothing here*/ );

/* testNoParamsLongArray4 */
$foo = array(/*nothing here*/);

/* testNoParamsShortArray1 */
$bar = [];

/* testNoParamsShortArray2 */
$bar = [     ];

/* testNoParamsShortArray3 */
$bar = [ /*nothing here*/ ];

/* testNoParamsShortArray4 */
$bar = [/*nothing here*/];

// Arrays: has parameters.

/* testHasParamsLongArray1 */
$foo = array( 1 );

/* testHasParamsLongArray2 */
$foo = array(1,2,3);

/* testHasParamsLongArray3 */
$foo = array(true);

/* testHasParamsShortArray1 */
$bar = [ 1 ];

/* testHasParamsShortArray2 */
$bar = [1,2,3];

/* testHasParamsShortArray3 */
$bar = [true];

/* testNoParamsLongList */
list() = $array; // Intentional fatal error.

/* testHasParamsLongList */
list($a) = $array;

/* testNoParamsShortList */
[
   // phpcs:ignore Standard.Cat.Sniff -- for reasons
] = $array; // Intentional fatal error.

/* testHasParamsShortList */
[$this->prop] = $array;

/* testNoParamsIsset */
$a = isset( /* comment */ ); // Intentional parse error.

/* testHasParamsIsset */
$a = isset( $array[$key] );

/* testNoParamsUnset */
unset(


); // Intentional parse error.

/* testHasParamsUnset */
unset(

      $hello,

);

// Intentional parse error.
/* testNoCloseParenthesis */
$array = array(1, 2, 3

// Intentional parse error.
/* testNoOpenParenthesis */
$array = function_call[];

// Intentional parse error. This has to be the last test in the file without a new line after it.
/* testLiveCoding */
$array = array