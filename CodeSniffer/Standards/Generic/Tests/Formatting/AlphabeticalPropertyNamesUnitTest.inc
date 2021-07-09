<?php

$b = 0;
$a = 0;

class Foo
{
    protected $_a;
    protected $a;
    protected $b;
    public function foo()
    {
        $b = 0;
        $a = 0;
    }
    protected $c;
    public function bar()
    {
        $b = 0;
        $a = 0;
    }
    protected $d;
}

$b = 0;
$a = 0;

abstract class Bar
{
    protected $a;
    protected $_a;
    protected $b;
    protected $e;
    public function foo()
    {
        $b = 0;
        $a = 0;
    }
    protected $d;
    protected $c;
}

$b = 0;
$a = 0;

interface FooInterface
{
    public $a;
    public $c;
    public $b;
}

$b = 0;
$a = 0;

trait FooTrait
{
    public $a;
    public $c;
    public $b;
}

?>
