<?php

test();
test($arg, $arg2);
test ();
test( );
test() ;
test( $arg);
test( $arg );
test ( $arg );

if (is_array($arg) === true) {

}

$something = get($arg1, $arg2);
$something = get($arg1, $arg2) ;
$something = get($arg1, $arg2)   ;

// No errors as this test only checks for function calls.
class TestClass extends MyClass
{

    const const1 = 'hello';
    const CONST2 = 'hello';

    public function test () { }
}

make_foo($string/*the string*/, true/*test*/);
make_foo($string/*the string*/, true/*test*/ );
make_foo($string /*the string*/, true /*test*/);
make_foo(/*the string*/$string, /*test*/true);
make_foo( /*the string*/$string, /*test*/true);

// No errors should be throw here because
// this is not a function call per se.
throw new Exception(
    'Exception text'
);

// Or here.
$obj = new TestClass( );

// Heredocs dont need to be indented.
method_call(
<<<EOH
Anyone want to recomment parse errors?

EOH
);

fputs(
    STDOUT,
    'Examples:
    $ {$app} --all
    $ {$app} --all');

fputs(STDOUT,
    "Examples:
    $ {$app} --all
    $ {$app} --all",
$something
    );

// This is not a function call.
function &testFunction($arg1,
    $arg2,
) {
}

$array = array();
array_map(
    function($x)
    {
        return trim($x);
    },
    $array
);

array_map(
     function($x)
     {
         return trim($x);
     },
    $array
);
?>
