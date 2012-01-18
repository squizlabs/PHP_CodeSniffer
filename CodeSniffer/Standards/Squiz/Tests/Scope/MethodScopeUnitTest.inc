<?php
function func1() {}

class MyClass
{
    function func1() {}
    public function func1() {}
    private function func1() {}
    protected function func1() {}
}

class Closure_Test {
    public function test() {
        $foo = function() { echo 'foo'; };
    }
}

function test() {
    $foo = function() { echo 'foo'; };
}
?>