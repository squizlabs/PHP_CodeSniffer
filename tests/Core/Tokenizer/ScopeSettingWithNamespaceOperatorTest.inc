<?php

/* testClassExtends */
class Foo extends namespace\Bar {}

/* testClassImplements */
$anon = new class implements namespace\Foo {}

/* testInterfaceExtends */
interface FooBar extends namespace\BarFoo {}

/* testFunctionReturnType */
function foo() : namespace\Baz {}

/* testClosureReturnType */
$closure = function () : namespace\Baz {}

/* testArrowFunctionReturnType */
$fn = fn() : namespace\Baz => new namespace\Baz;
