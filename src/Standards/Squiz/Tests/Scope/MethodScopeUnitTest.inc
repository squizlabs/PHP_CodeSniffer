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

        function my_sort() {
            // ...
        }

        $a = array();
        usort($a, 'my_sort');
    }
}

function test() {
    $foo = function() { echo 'foo'; };
}

trait Trait_Test {
	function func1() {}
	public function func1() {}
	private function func1() {}
	protected function func1() {}
}

class Nested {
    public function getAnonymousClass() {
        return new class() {
            function __something() {}
        };
    }
}
