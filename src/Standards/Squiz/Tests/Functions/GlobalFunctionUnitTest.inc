<?php
function func1() {}

class MyClass
{
    function func1() {}
}

interface MyInterface
{
    function func1() {}
}

function __autoload($class) {}

echo preg_replace_callback('~-([a-z])~', function ($match) { return strtoupper($match[1]); }, 'hello-world');
?>
