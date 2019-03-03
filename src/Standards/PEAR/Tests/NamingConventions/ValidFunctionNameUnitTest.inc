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

class Magic_Test extends \SoapClient {
    static function __callStatic($name, $args) {}
    function __wakeup() {}
    function __getTypes() {}
    function __autoload() {}
    function __myFunction() {}
    function __my_function() {}
}

function __call() {}
function __set() {}
function __set_state() {}
function __autoload($class) {}
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

function _() {}

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
