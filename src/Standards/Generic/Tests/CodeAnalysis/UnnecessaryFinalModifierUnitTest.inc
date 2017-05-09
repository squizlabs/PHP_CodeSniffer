<?php

class Foo {
    public final $FOOBAR = 23;
    protected final $FOO = 42;
    private final $BAR = 17;    
}

final class Foo_Bar {
    public $foobar;
    public final $FOOBAR = 23;
    
    protected $foo;
    protected final $FOO = 42;
    
    private $bar;
    private final $BAR = 17;
}

final class Bar_Foo {
    public $foobar;
    protected $foo;
    private $bar;    
}