<?php
/* @codingStandardsIgnoreFile */

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
