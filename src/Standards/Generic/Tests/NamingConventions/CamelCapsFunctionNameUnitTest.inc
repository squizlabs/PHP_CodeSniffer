<?php

abstract class My_Class {

    public function __construct() {}
    public function My_Class() {}
    public function _My_Class() {}

    public function getSomeValue() {}
    public function parseMyDSN() {}
    public function get_some_value() {}
    public function GetSomeValue() {}
    public function getSomeValue_Again() {}

    protected function getSomeValue() {}
    protected function parseMyDSN() {}
    protected function get_some_value() {}

    private function _getSomeValue() {}
    private function parseMyDSN() {}
    private function _get_some_value() {}

    function getSomeValue() {}
    function parseMyDSN() {}
    function get_some_value() {}

}//end class

function getSomeValue() {}
function parseMyDSN() {}
function get_some_value() {}


/* Test for magic functions */

class Magic_Test {
    function __construct() {}
    function __destruct() {}
    function __call($name, $args) {}
    static function __callStatic($name, $args) {}
    function __get($name) {}
    function __set($name, $value) {}
    function __isset($name) {}
    function __unset($name) {}
    function __sleep() {}
    function __wakeup() {}
    function __toString() {}
    function __set_state() {}
    function __clone() {}
    function __autoload() {}
    function __invoke() {}
    function __myFunction() {}
    function __my_function() {}

}

function __construct() {}
function __destruct() {}
function __call() {}
function __callStatic() {}
function __get() {}
function __set() {}
function __isset() {}
function __unset() {}
function __sleep() {}
function __wakeup() {}
function __toString() {}
function __set_state() {}
function __clone() {}
function __autoload($class) {}
function __invoke() {}
function __myFunction() {}
function __my_function() {}


class Closure_Test {
    function test() {
        $foo = function() { echo 'foo'; };
    }
}

function test() {
    $foo = function() { echo 'foo'; };
}

/* @codingStandardsIgnoreStart */
class MyClass
{
    /* @codingStandardsIgnoreEnd */
    public function __construct() {}
}

trait Foo
{
    function __call($name, $args) {}
}

class Magic_Case_Test {
    function __Construct() {}
    function __isSet($name) {}
    function __tostring() {}
}
function __autoLoad($class) {}

class Foo extends \SoapClient
{
    public function __soapCall(
        $functionName,
        $arguments,
        $options = array(),
        $inputHeaders = null,
        &$outputHeaders = array()
    ) {
        // body
    }
}

function __debugInfo() {}
class Foo {
    function __debugInfo() {}
}

function ___tripleUnderscore() {} // Ok.

class triple {
    public function ___tripleUnderscore() {} // Ok.
}

/* Magic methods in anonymous classes. */
$a = new class {
    function __construct() {}
    function __destruct() {}
    function __call($name, $args) {}
    static function __callStatic($name, $args) {}
    function __get($name) {}
    function __set($name, $value) {}
    function __isset($name) {}
    function __unset($name) {}
    function __sleep() {}
    function __wakeup() {}
    function __toString() {}
    function __set_state() {}
    function __clone() {}
    function __autoload() {}
    function __invoke() {}
    function __myFunction() {}
    function __my_function() {}

};

class FooBar extends \SoapClient {
    public function __getCookies() {}
}

class Nested {
    public function getAnonymousClass() {
        return new class() {
            public function nested_function() {}
            function __something() {}
        };
    }
}

abstract class My_Class {
    public function my_class() {}
    public function _MY_CLASS() {}
}

enum Suit: string implements Colorful, CardGame {
    // Magic methods.
    function __call($name, $args) {}
    static function __callStatic($name, $args) {}
    function __invoke() {}

    // Valid Method Name.
    public function getSomeValue() {}

    // Double underscore non-magic methods not allowed.
    function __myFunction() {}
    function __my_function() {}

    // Non-camelcase.
    public function parseMyDSN() {}
    public function get_some_value() {}
}
