<?php
$obj = new MyClass();
$obj =& new MyClass();
$obj = &new MyClass();
new MyClass();

$objects = array('one' => new MyClass());
$object->myFunction(new MyClass());

throw new MyException($msg);

function foo() { return new MyClass(); }

$doodad = $x ? new Foo : new Bar;

function returnFn() {
    $fn = fn($x) => new MyClass();
}

function returnMatch() {
    $match = match($x) {
        0 => new MyClass()
    }
}

// Issue 3333.
$time2 ??= new \DateTime();
$time3 = $time1 ?? new \DateTime();
$time3 = $time1 ?? $time2 ?? new \DateTime();

function_call($time1 ?? new \DateTime());
$return = function_call($time1 ?? new \DateTime()); // False negative depending on interpretation of the sniff.

function returnViaTernary() {
    return ($y == false ) ? ($x === true ? new Foo : new Bar) : new FooBar;
}

function nonAssignmentTernary() {
    if (($x ? new Foo() : new Bar) instanceof FooBar) {
        // Do something.
    }
}

// Intentional parse error. This must be the last test in the file.
function new
?>
