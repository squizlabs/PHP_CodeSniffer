<?php
if ($var1) {
    $var2 = 0;
} else {
    $var2 = 1;
}

$var1 ? $var2 = 0 : $var2 = 1;

function foo(string $bar, array $baz, ?MyClass $object) : MyClass {}

class Example {
    public ?int $scalarType;
}

$a = fn(?\DateTime $x) : ?\DateTime => $x;

$b = fn ($b) => $b ? true : false;
