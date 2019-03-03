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

class Magic_Test extends \SoapClient {
    function __construct() {}
    function __set($name, $value) {}
    function __getCookies() {}
    function __autoload() {}
    function __myFunction() {}
    function __my_function() {}

}

function __destruct() {}
function __callStatic() {}
function __set_state() {}
function __autoload($class) {}
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

function ___tripleUnderscore() {} // Ok.

class triple {
    public function ___tripleUnderscore() {} // Ok.
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
