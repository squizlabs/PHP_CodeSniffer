<?php

function foo($a, $b) {
    return $a * 2;
}

function baz($a, $b) {
    echo "baz({$a});";
}

function bar($a, $b) {
    $x = $b;
    for ($i = 0; $i <$a; $i++) {
        $x += $a * $i;
    }
    return $x;
}

function foobar($a, &$b) {
    return (preg_match('/foo/', $a, $b) !== 0);
}

class Foo implements Bar {
    function barfoo($a, $b) {
        // Empty body means interface method in many cases.
    }

    function fooBar($a, $b) {
        return;
    }
}

function foo($bar)
{
    print <<<BAZ
    $bar
BAZ;
}

function foo( $parameter ) {
    $wango = <<<HERE
1 Must be a HEREdoc of at least one line
HERE;
    $x = $parameter; // This line must be immediately after the HERE; with no intervening blank lines.
    $tango = <<<HERE
1 Must be a HEREdoc
2
3
4
5
6
7
8
9 at least 9 lines long
HERE;
}

function foo( $parameter ) {
    return <<<HTML
<?xml version="1.0"?>
<value>$parameter</value>
HTML;
}

print foo( 'PARAMETER' );
print "\n";

function foo($bar)
{
    print "${bar} things\n";
}

function bar($x)
{
    return 2 * ${x};
}

$foo = function ($a, $b) {
    return $a * 2;
};

function foobar() {
    return;
}


/*
 * The function signature of methods in extended classes and implemented
 * interfaces has to mirror the parent class/interface.
 * The overloaded method may not use all params.
 */

class MyClass {
    public function something($a, $b) {
        return $a * 2;
    }
}

class MyExtendedClass extends SomeClass {
    public function something($a, $b) {
        return $a * 2;
    }
}

class MyExtendedClass implements SomeInterface {
    public function something($a, $b) {
        return $a * 2;
    }
}


/*
 * Functions may not use all params passed to them.
 * Report different violations for params *before* and *after* the last param used.
 */

function something($a) {
    return 'foobar';
}

function myCallback($a, $b, $c, $d) {
    return $a * $c;
}

fn ($a, $b, $c) => $b;

// phpcs:set Generic.CodeAnalysis.UnusedFunctionParameter ignoreTypeHints[] Exception

function oneParam(Exception $foo) {
    return 'foobar';
}

function moreParamFirst(Exception $foo, LogicException $bar) {
    return 'foobar' . $bar;
}

function moreParamSecond(LogicException $bar, Exception $foo) {
    return 'foobar' . $bar;
}
// phpcs:set Generic.CodeAnalysis.UnusedFunctionParameter ignoreTypeHints[]

class ConstructorPropertyPromotionNoContentInMethod {
    public function __construct(protected int $id) {}
}

class ConstructorPropertyPromotionWithContentInMethod {
    public function __construct(protected int $id, $toggle = true) {
        if ($toggle === true) {
            doSomething();
        }
    }
}

$found = in_array_cb($needle, $haystack, fn($array, $needle) => $array[2] === $needle);


/*
 * Don't adjust the error code for closures and arrow functions in extended classes/classes implementing interfaces.
 */
class MyExtendedClass extends SomeClass {
    public function something($a, $b) {
        $c = $a + $b;
        $closure = function ($c, $d) {
            return $c * 2;
        };
    }
}

class MyExtendedClass implements SomeInterface {
    public function something($a, $b) {
        $c = $a + $b;
        $fn = fn($c, $d) => $c[2];
    }
}


/**
 * Magic methods must match the function signature dictated by PHP.
 * Flagging unused parameters leads to notices which cannot be solved.
 */
class MagicMethodsWithParams {
    public function __set(string $name, mixed $value) {
        // Forbid dynamic properties & overloading inaccessible properties.
        throw new RuntimeException('Forbidden');
    }

    public function __get(string $name) {
        throw new RuntimeException('Forbidden');
    }

    public function __isset(string $name) {
        throw new RuntimeException('Forbidden');
    }

    public function __unset(string $name) {
        throw new RuntimeException('Forbidden');
    }

    public function __unserialize( array $data ) {
        // Prevent unserializing from a stored representation of the object for security reasons.
        $this->instance = new self();
    }

    public static function __set_state(array $properties) {
        return new self();
    }

    public function __call(string $name, array $arguments) {
        if (method_exists($this, $name)) {
            // None of the methods which can be called in this class take arguments, so not passing them.
            return $this->$name();
        }
    }

    public static function __callStatic(string $name, array $arguments) {
        if (method_exists($this, $name)) {
            // None of the methods which can be called in this class take arguments, so not passing them.
            return self::$name();
        }
    }
}

/**
 * Unused parameters in magic methods which have flexible function signatures should still be flagged.
 */
class MagicMethodsWithParamsNotDictatedByPHP {
    public $foo;
    public function __construct($foo, $bar, $baz) {
        $this->foo = $foo;
    }

    public function __invoke($foo, $bar, $baz) {
        $this->foo = $foo;
    }
}

/**
 * Unused parameters in magic methods which have flexible function signatures
 * where the method potentially overloads a parent method should still be flagged,
 * but should use the `FoundInExtendedClassAfterLastUsed` error code.
 */
class MagicMethodsWithParamsNotDictatedByPHPInChildClass extends SomeParent{
    public $foo;
    public function __construct($foo, $bar, $baz) {
        $this->foo = $foo;
    }

    public function __invoke($foo, $bar, $baz) {
        $this->foo = $foo;
    }
}
