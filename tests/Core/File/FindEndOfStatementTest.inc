<?php

/* testSimpleAssignment */
$a = false;

/* testControlStructure */
while(true) {}
$a = 1;

/* testClosureAssignment */
$a = function($b=false;){};

/* testHeredocFunctionArg */
myFunction(<<<END
Foo
END
, 'bar');

/* testSwitch */
switch ($a) {
    case 1: {break;}
    default: {break;}
}

/* testStatementAsArrayValue */
$a = [new Datetime];
$a = array(new Datetime);
$a = new Datetime;

/* testUseGroup */
use Vendor\Package\{ClassA as A, ClassB, ClassC as C};

$a = [
    /* testArrowFunctionArrayValue */
    'a' => fn() => return 1,
    'b' => fn() => return 1,
];

/* testStaticArrowFunction */
static fn ($a) => $a;

/* testArrowFunctionReturnValue */
fn(): array => [a($a, $b)];

return 0;
