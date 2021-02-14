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

// Intentional parse error. This must be the last test in the file.
function new
?>
