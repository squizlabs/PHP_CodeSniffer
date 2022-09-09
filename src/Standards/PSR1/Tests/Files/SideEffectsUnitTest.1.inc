<?php
namespace Vendor\Model;

const CONSTANT1 = 1;
const CONSTANT2 = 2;

use Something;
use SomethingElse;
use function interface_exists;

declare(ticks=1);

declare(ticks=1) {
    echo $i;
}

declare(ticks=1) :
    echo $i;
enddeclare;

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

if (!interface_exists('MyEnum')) {
    // Define an enum.
    enum MyEnum {}
}

#[\Attribute]
namespace {
    class A {}
}

defined('APP_BASE_URL') or define('APP_BASE_URL', '/');

readonly class Foo {
    public function __construct(
        private string $foo,
        private string $bar,
    ) {
    }
}

?>
