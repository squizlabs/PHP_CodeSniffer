<?php
namespace MyProject;

use BarClass as Bar;
use My\Full\Classname as Another;


use Something;
use SomethingElse;

// Comment here.
use LastThing;

class Foo {
}

$var = new MyClass(
    function () use ($foo, $bar) {
        return true;
    }
);

class Container extends Component implements IContainer
{
    use TContainer;
}

trait HelloWorld
{
    use Hello, World;
}

enum SomeEnum
{
    use Hello, World;
}

$x = $foo ? function ($foo) use /* comment */ ($bar): int {
    return 1;
} : $bar;

// Testcase must be on last line in the file.
use
