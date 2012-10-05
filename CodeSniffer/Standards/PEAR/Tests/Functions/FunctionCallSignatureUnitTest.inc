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
// this is multi-line.
throw new Exception(
    'Exception text'
);

// Objects are the same as a normal call.
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

$bar = new stdClass(
    4,
    5,
    6
   
);

$bar = new stdClass(
    4,
    5,
    6

);

$foo = new stdClass(
    1,
    2,
    3);

public function doSomething()
{
    return $this->getFoo()
        ->doBar(
            $this->getX() // no comma here
                ->doY()  // this is still the first method argument
                ->doZ()  // this is still the first method argument
        );
}

$var = myFunction(
$foo,
$bar
);

// @codingStandardsChangeSetting PEAR.Functions.FunctionCallSignature allowMultipleArguments false

fputs(
    STDOUT,
    "Examples:
    $ {$app} , --all
    $ {$app} --all", $something
);

$array = array();
array_map(
    function($x)
    {
        return trim($x, $y);
    }, $foo
    $array
);

$bar = new stdClass(
    4, 5, 6
);

public function doSomething()
{
    return $this->getFoo()
        ->doBar(
            $this->getX() // no comma here
                ->doY()  // this is still the first method argument
                ->doZ()  // this is still the first method argument
        );
}

doError(
    404, // status code
    'Not Found', // error name
    'Check your id' // fix
);

// @codingStandardsChangeSetting PEAR.Functions.FunctionCallSignature allowMultipleArguments true

// Don't report errors for closing braces. Leave that to other sniffs.
foo(
    [
        'this',
        'is',
        'an',
        'array'
    ],
[
        'this',
        'is',
        'an',
        'array'
     ],
    array(
        'this',
        'is',
        'an',
        'array'
    ),
    array(
        'this',
        'is',
        'an',
        'array'
     ),
    function($x)
    {
         return trim($x);
   }
);

function foo()
{
    myFunction(
        'string'.
        // comment
        // comment 
        'string'.
        /* comment
         * comment
        */
        'string'.
    );
}
?>
