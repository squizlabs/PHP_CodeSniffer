<?php

class MyClass
{
    function _myFunction() {}
    private function myFunction() {}
    function __myFunction() {}
    public static function myFunction() {}
    static public /*comment*/ function myFunction() {}
    final public function myFunction() {}
    public /*comment*/ final function myFunction() {}
    abstract private function myFunction();
    private /*comment*/ abstract function myFunction();
    final public /*comment*/ static function myFunction() {}
    static protected final abstract function myFunction();
    public function _() {}
}

interface MyInterface
{
    function _myFunction();
    function __myFunction();
    public static function myFunction();
    static public function myFunction();
    public function _();
}

trait MyTrait
{
    function _myFunction() {}
    private function myFunction() {}
    function __myFunction() {}
    public static function myFunction() {}
    static public function myFunction() {}
    final /*comment*/ public function myFunction() {}
    public final function myFunction() {}
    abstract private function myFunction();
    private abstract function myFunction();
    final public static function myFunction() {}
    static /*comment*/ protected final abstract function myFunction();
    public function _() {}
}
