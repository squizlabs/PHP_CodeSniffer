<?php

/* testGlobalFunction */
function something()
{
    /* testNestedFunction */
    function nested() {}

    /* testNestedClosure */
    $c = function() {};
}

abstract class MyClass {
    /* testClassMethod */
    public function something() {
        /* testClassNestedFunction */
        function nested() {}

        /* testClassNestedClosure */
        $c = function() {};
    }

    /* testClassAbstractMethod */
    abstract protected function somethingAbstract();
}

$a = new class {
    /* testAnonClassMethod */
    final public function something() {}
};

interface MyInterface {
    /* testInterfaceMethod */
    function something();
}

trait MyTrait {
    /* testTraitMethod */
    public function something() {}
}
