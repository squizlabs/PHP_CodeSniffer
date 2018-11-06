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

    private function getSomeValue() {}
    private function parseMyDSN() {}
    private function get_some_value() {}

    function getSomeValue() {}
    function parseMyDSN() {}
    function get_some_value() {}
    function o_toString() {}

}//end class

function getSomeValue() {}
function parseMyDSN() {}
function get_some_value() {}

class Closure_Test {
    function test() {
        $foo = function() { echo 'foo'; };
    }
}

trait Foo
{
    function __call() {}
}

class Magic_Case_Test {
    function __Construct() {}
    function __isSet() {}
    function __tostring() {}
    function __set_state() {}
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

    function __() {}
}

function ___tripleUnderscore() {} // Ok.

class triple {
    public function ___tripleUnderscore() {} // Ok.
}

class Nested {
    public function getAnonymousClass() {
        return new class() {
            public function nested_function() {}
        };
    }
}

