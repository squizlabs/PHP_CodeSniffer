<?php
/* phpcs:ignoreFile */

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
