<?php

/* testUseGroupSingleLine */
use some\name\{ClassA, ClassB, ClassC as C};

/* testUseGroupMultiLineWithTrailingComma */
use Foo\Bar\{
    function foo,
    Bar,
    const BAZ as C,
};
