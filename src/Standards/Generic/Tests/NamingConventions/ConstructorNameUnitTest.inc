<?php
class TestClass extends MyClass
{

    function TestClass() {
        parent::MyClass();
        parent::__construct();
    }

    function __construct() {
        parent::MYCLASS();
        parent::__construct();
    }

}

class MyClass
{

    function MyClass() {
        parent::YourClass();
        parent::__construct();
    }

    function __construct() {
        parent::YourClass();
        parent::__construct();
    }

}

class MyOtherClass extends \MyNamespace\SomeClass
{
    function __construct() {
        something::MyNamespace();
    }

}

abstract class MyAbstractClass extends \MyNamespace\SomeClass
{
    abstract public function __construct();
}

class OldClass
{
    function OldClass()
    {

    }
}

foo(new class extends MyClass
{

    function TestClass() {
        parent::MyClass();
        parent::__construct();
    }

    function __construct() {
        parent::MyClass();
        parent::__construct();
    }

});

class OlderClass
{
    function OlderClass() {}

    function __CONSTRUCT() {}
}


// Issue #2178.
class Nested extends Another {
    public function getAnonymousClass() {
        return new class() extends Something {
            public function nested() {
                echo 'In method nested!';
                parent::Another(); // OK.
            }

            public function __construct() {
                parent::Another(); // OK.
            }
        };
    }

    abstract public function nested();
}

interface CustomChildCreator
{
    public function customChildCreator($elementName, Project $project);
}
