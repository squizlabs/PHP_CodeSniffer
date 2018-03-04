<?php
class MyClass
{
    public static $var = null;
    protected $var = null;

    public   static  $var = null;
    protected  $var = null;

    private function myFunction() {}
    public static function myFunction() {}

    private   function myFunction() {}
    public  static function myFunction() {}
    private static   function myFunction() {}

    private  static
        function myFunction() {}

    public static function output()
    {
        // New in PHP 5.3
        static::bar();
    }

    public static$var = null;

    public  
    static
    $var = null;
}

abstract class Foo
{
    public static function getInstance()
    {
        return new static();
    }
}

if ($geometry instanceof static) {
    echo 'foo';
}

class MyClass1 {
    use HelloWorld { sayHello as private; }
}
