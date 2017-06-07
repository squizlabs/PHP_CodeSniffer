<?php
class MyClass
{
    private $var1 = null;
    protected $var2 = null;
    public $var3 = null;
    $var4 = null;

    public function method()
    {
        $anon = new class() {
            public $p1;
            protected $p2;
            private $p3;
            $p0 = null;
        };
    }
}

class foo
{
    const bar = <<<BAZ
qux
BAZ;
}

class Foo {
    public static function default(): ObjectA
    {
        return new self(1);
    }
}//end class
