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
