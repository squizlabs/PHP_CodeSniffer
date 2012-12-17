<?php
namespace Vendor\Model;

const CONSTANT1 = 1;
const CONSTANT2 = 2;

use Something;
use SomethingElse;

define("MAXSIZE", 100);
if (defined('MINSIZE') === false) {
    define("MINSIZE", 10);
}

// Class comment.
final class Foo
{
    public $myvar = 'foo';

    // Function comment.
    function Bar() {
        echo 'hi';
    }
}

function MyFunction() {
    echo 'hi';
}

if (!function_exists('YourFunction')) {
    // Define a function.
    function YourFunction() {
        echo 'hi';
    }
}

if (!class_exists('MyClass')) {
    // Define a class.
    class MyClass
    {
        function SomeFunction() {}
    }
} else if (!interface_exists('MyInterface')) {
    interface MyInterface {}
} elseif (!interface_exists('MyInterface')) {
    interface MyInterface {}
} else {
    interface MyInterface {}
}

if (!interface_exists('MyInterface')) {
    // Define an interface.
    interface MyInterface {}
}

namespace {
    class A {}
}

?>
