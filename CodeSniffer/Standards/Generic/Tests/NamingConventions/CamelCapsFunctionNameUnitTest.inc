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
    function __autoload() {}
    function __invoke() {}
    function __myFunction() {}
    function __my_function() {}
    function __call() {}
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
function __autoload() {}
function __invoke() {}
function __myFunction() {}
function __my_function() {}
function __call() {}

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
    function __call() {}
}

class Magic_Case_Test {
    function __Construct() {}
    function __isSet() {}
    function __tostring() {}
}
function __autoLoad() {}

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
    function __autoload() {}
    function __invoke() {}
    function __myFunction() {}
    function __my_function() {}
    function __call() {}
}
