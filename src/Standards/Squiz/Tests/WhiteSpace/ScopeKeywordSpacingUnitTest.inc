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

// Issue #3188 - static as return type.
public static function fCreate($attributes = []): static
{
    return static::factory()->create($attributes);
}

public static function fCreate($attributes = []): ?static
{
    return static::factory()->create($attributes);
}

// Also account for static used within union types.
public function fCreate($attributes = []): object|static
{
}

// Ensure that static as a scope keyword when preceeded by a colon which is not for a type declaration is still handled.
$callback = $cond ? get_fn_name() : static  function ($a) { return $a * 10; };

class TypedProperties {
    public
        int $var;

    protected string $stringA, $stringB;

    private   bool
        $boolA,
        $boolB;
}

// PHP 8.0 constructor property promotion.
class ConstructorPropertyPromotionTest {
    public function __construct(
        public    $x = 0.0,
        protected $y = '',
        private   $z = null,
        $normalParam,
    ) {}
}

class ConstructorPropertyPromotionWithTypesTest {
    public function __construct(protected   float|int $x, public?string &$y = 'test', private mixed $z) {}
}
