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
?>
