<?php

class Foo {
    public final function fooBar() {}
    protected final function fool() {}
    private final function Bar() {}
}

final class Foo_Bar {
    public $foobar;
    public final $FOOBAR = 23; // Parse error, but that's not the concern of this sniff, so report it.
    public final function fooBar() {}

    protected function foo() {}
    protected final function fool() {}

    private function Bar() {}
    private final function Bard() {}
}

final class Bar_Foo {
    public $foobar;
    protected $foo;
    private $bar;

    public function fooBar() {}
    protected function foo() {}
    private function Bar() {}
}

final readonly class Foo_Bar {
    public final function fooBar() {}
    final protected function fool() {}
}
