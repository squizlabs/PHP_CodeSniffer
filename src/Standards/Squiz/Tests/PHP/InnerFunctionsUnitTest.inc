<?php
function outer()
{
    if (!function_exists('inner')) {
        function inner() {
        }
    }
}

// Closures are allowed.
function myFunc($foo)
{
    $callback = function ($bar) use ($foo)
                {
                    $bar += $foo;
                };
}

// Anon class methods are allowed.
function test()
{
    return new class {

        public function foo()
        {
            // do something
        }
    };
}

new class {
    public function valueObject(): object
    {
         return new class {
            public function string(): string {
                 return 'string';
            }
        };
    }
};

new class {
    public function outer()
    {
        if (!function_exists('inner')) {
            function inner() {
            }
        }
    }
};

$outerClosure = function ()
{
    // Functions inside closures are not allowed.
    function innerFunction() {
    }
};

// Allow methods in classes/traits/interfaces defined inside functions
function foo() {
    if (class_exists('MyClass') === false) {
        class MyClass {
            function foo() {}
        }
    }

    if (trait_exists('MyTrait') === false) {
        trait MyTrait {
            function foo() {}
        }
    }

    if (interface_exists('MyInterface') === false) {
        interface MyInterface {
            function foo();
        }
    }

    // But disallow functions nested inside those methods
    if (class_exists('NestedFunctionInMethod') === false) {
        class NestedFunctionInMethod {
            function foo() {
                function innerFunction() {}
            }
        }
    }
}
