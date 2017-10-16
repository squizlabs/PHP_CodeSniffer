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

if ($geometry instanceof static      || $geometry instanceof static) {
    echo 'foo';
}

class MyClass1 {
    use HelloWorld { sayHello as private; }
}

abstract class Foo
{
    public static function getInstance()
    {
        return new /* comment */   static();
    }

    public static function output()
    {
        static   /* comment */ :: bar();
    }
}

class MyOtherClass
{
    public
        $varK = array( 'a', 'b' );

    protected    $varK,
        $varL,
        $varM;

    protected static
        $varK, $varL, $varM;

    private
        $varO = true,
        $varP = array( 'a' => 'a', 'b' => 'b' ),
        $varQ = 'string',
        $varR = 123;

    // Intentionally missing a semi-colon for testing.
    public
        $varS,
        $varT
}
