<?php
class TestClass extends MyClass
{

    function TestClass() {
        parent::MyClass();
        parent::__construct();
    }

    function __construct() {
        parent::MyClass();
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

class MyClass extends \MyNamespace\SomeClass
{
    function __construct() {
        something::MyNamespace();
    }

}
?>
