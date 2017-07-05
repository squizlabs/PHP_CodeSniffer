<?php

$result = myFunction();
$result = myFunction($arg1, $arg2);
$result = myFunction($arg1,$arg2);
$result = myFunction($arg1 , $arg2);
$result = myFunction($arg1 ,  $arg2);
$result = myFunction($arg1, $arg2, $arg3,$arg4, $arg5);
$result = myFunction($arg1, $arg2, $arg3, $arg4, $arg5);
$result = myFunction($arg1, $arg2 = array());
$result = myFunction($arg1 , $arg2 =array());
$result = myFunction($arg1 , $arg2= array());
$result = myFunction($arg1 , $arg2=array());

$result = myFunction($arg1, 
                     $arg2 = array(), 
                     $arg3, 
                     $arg4, 
                     $arg5);

throw new Exception("This is some massive string for a message", 
                         $cause);

// Function definitions are ignored
function myFunction($arg1,$arg2)
{
}

function myFunction    ($arg1,$arg2)
{
}

function myFunction($arg1=1,$arg2=2)
{
}


function myFunction($arg1 = 1,$arg2 = 2)
{
}

$key = array_search($this->getArray($one,  $two,$three),$this->arrayMap);
$this->error($obj->getCode(),$obj->getMessage(),$obj->getFile(),$obj->getLine());

make_foo($string /*the string*/ , true /*test*/);
make_foo($string/*the string*/ ,   /*test*/ true);
make_foo($string /*the string*/, /*test*/ true);

class MyClass {
    function myFunction() {
        blah($foo, "{{$config['host']}}", "{$config}", "hi there{}{}{{{}{}{}}");
    }
}

// Function definition, not function call, so should be ignored
function &myFunction($arg1=1,$arg2=2)
{
}

return array_udiff(
    $foo,
    $bar,
    function($a, $b) {
        $foo='bar';
        return $foo;
    }
);

var_dump(<<<FOO
foo
FOO
,
<<<BAR
bar
BAR
, <<<BAZ
baz
BAZ
,<<<'NOW'
now
NOW
,  <<<'THEN'
then
THEN
);

if (in_array($arg1, ['foo','bar'])) {}
if (in_array($arg1, array('foo','bar'))) {}

$b = foo(
    "1", // this is a comment
    "2",  // this is a comment
    "3",// this is a comment
    "4"
);

var_dump(
    <<<TEXT
foo
TEXT
    ,
    'bar'
);

unset($foo,$bar);

$closure($foo,$bar);
$var = $closure() + $closure($foo,$bar) + self::$closure($foo,$bar);

class Test
{
    public static function baz($foo, $bar)
    {
        $a = new self($foo,$bar);
        $b = new static($foo,$bar);
    }
}

$obj->{$var}($foo,$bar);

(function ($a, $b) {
    return function ($c, $d) use ($a, $b) {
        echo $a, $b, $c, $d;
    };
})('a','b')('c','d');
