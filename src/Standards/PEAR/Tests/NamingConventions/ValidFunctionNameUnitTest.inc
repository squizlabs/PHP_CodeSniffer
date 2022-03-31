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
    public function My_Package_getSomeValue() {}
    public function _getSomeValue() {}
    public function _parseMyDSN() {}
    public function _get_some_value() {}
    public function _GetSomeValue() {}
    public function _getSomeValue_Again() {}
    public function _My_Package_getSomeValue() {}

    protected function getSomeValue() {}
    protected function parseMyDSN() {}
    protected function get_some_value() {}
    protected function GetSomeValue() {}
    protected function getSomeValue_Again() {}
    protected function My_Package_getSomeValue() {}
    protected function _getSomeValue() {}
    protected function _parseMyDSN() {}
    protected function _get_some_value() {}
    protected function _GetSomeValue() {}
    protected function _getSomeValue_Again() {}
    protected function _My_Package_getSomeValue() {}

    private function getSomeValue() {}
    private function parseMyDSN() {}
    private function get_some_value() {}
    private function GetSomeValue() {}
    private function getSomeValue_Again() {}
    private function My_Package_getSomeValue() {}
    private function _getSomeValue() {}
    private function _parseMyDSN() {}
    private function _get_some_value() {}
    private function _GetSomeValue() {}
    private function _getSomeValue_Again() {}
    private function _My_Package_getSomeValue() {}

    function getSomeValue() {}
    function parseMyDSN() {}
    function get_some_value() {}
    function GetSomeValue() {}
    function getSomeValue_Again() {}
    function My_Package_getSomeValue() {}
    function _getSomeValue() {}
    function _parseMyDSN() {}
    function _get_some_value() {}
    function _GetSomeValue() {}
    function _getSomeValue_Again() {}
    function _My_Package_getSomeValue() {}

}//end class

interface My_Interface {

    public function getSomeValue();
    public function parseMyDSN();
    public function get_some_value();
    public function GetSomeValue();
    public function getSomeValue_Again();
    public function My_Package_getSomeValue();
    public function _getSomeValue();
    public function _parseMyDSN();
    public function _get_some_value();
    public function _GetSomeValue();
    public function _getSomeValue_Again();
    public function _My_Package_getSomeValue();

    function getSomeValue();
    function parseMyDSN();
    function get_some_value();
    function GetSomeValue();
    function getSomeValue_Again();
    function My_Package_getSomeValue();
    function _getSomeValue();
    function _parseMyDSN();
    function _get_some_value();
    function _GetSomeValue();
    function _getSomeValue_Again();
    function _My_Package_getSomeValue();

}//end interface

function My_Package_getSomeValue() {}
function My_Package_parseMyDSN() {}
function My_Package_get_some_value() {}
function My_PackagegetSomeValue() {}
function My_Package_getSomeValue_Again() {}
function My_Package() {}
function _My_Package_getSomeValue() {}
function _My_Package_parseMyDSN() {}
function _My_Package_get_some_value() {}
function _My_PackagegetSomeValue() {}
function _My_Package_getSomeValue_Again() {}
function _My_Package() {}


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

function my_package_function() {}
function Package_() {}
function Package() {}

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
function _() {}

function __debugInfo() {}
class Foo {
    function __debugInfo() {}
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

function send_system_email__to_user($body, $recipient){}

class Nested {
    public function getAnonymousClass() {
        return new class() {
            public function nested_function() {}
            function __something() {}
            private function something() {}
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
    public function parseMyDSN() {}
    private function _getAnotherValue() {}

    // Double underscore non-magic methods not allowed.
    function __myFunction() {}
    function __my_function() {}

    // Non-camelcase.
    public function get_some_value() {}

	// Private without underscore prefix.
    private function getMe() {}
}
