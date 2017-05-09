<?php

class FooBar {
    public function __construct($a, $b) {
        parent::__construct($a, $b);
    }
}

class BarFoo {
    public function __construct($a, $b) {
        parent::__construct($a, 'XML', $b);
    }
}

class Foo {
    public function export($a, $b = null) {
        return parent::export($a, $b);
    }
}

class Bar {
    public function export($a, $b = null) {
        return parent::export($a);
    }
}