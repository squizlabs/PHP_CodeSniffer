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

function new
?>
