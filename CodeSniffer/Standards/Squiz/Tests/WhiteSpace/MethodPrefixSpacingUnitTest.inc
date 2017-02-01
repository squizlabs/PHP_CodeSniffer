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

abstract class MyClass
{
    abstract protected function myFunction();
    final public function myFunction() {}
}

abstract  class MyClass
{
    abstract private function myFunction();
    abstract  protected function myFunction();
    abstract   public function myFunction();
    abstract
    private function myFunction();
    abstract
        private function myFunction();

    final public function myFunction() {}
    final  private function myFunction() {}
    final   protected function myFunction() {}
    final
    public function myFunction() {}
    final
        public function myFunction() {}
    
    abstract private static function myFunction();
    abstract  protected static  function myFunction();
    abstract   public  static  function myFunction();
    abstract
    private static  function myFunction();
    abstract
        private  static function myFunction();

    final public static function myFunction() {}
    final  private  static function myFunction() {}
    final   protected   static   function myFunction() {}
    final
    public
    static function myFunction() {}
    final
        public
            static  function myFunction() {}
}
