<?php

/*
 * Test empty statement: two consecutive semi-colons without executable code between them.
 */
function_call(); // OK.

// The below examples are all bad.
function_call();;

function_call();
;

function_call();
/* some comment */;

function_call();
/* some comment */     ;

?>
<input name="<?php ; something_else(); ?>" />
<input name="<?php something_else(); ; ?>" />

/*
 * Test empty statement: no code between PHP open and close tag.
 */
<input name="<?php something_else() ?>" /> <!-- OK. -->
<input name="<?php something_else(); ?>" /> <!-- OK. -->
<input name="<?php /* comment */ ?>" /> <!-- OK. -->

<input name="<?php ?>" /> <!-- Bad. -->

<input name="<?php


?>" /> <!-- Bad. -->

<!--
/*
 * Test detecting & fixing a combination of the two above checks.
 */
-->
<?php ; ?>

<input name="<?php ; ?>" /> <!-- Bad. -->

<!-- Tests with short open echo tag. -->
<input name="<?= 'some text' ?>" /> <!-- OK. -->
<input name="<?= ?>" /> <!-- Bad. -->
<input name="<?= ; ?>" /> <!-- Bad. -->

<?php
// Guard against false positives for two consecutive semi-colons in a for statement.
for ( $i = 0; ; $i++ ) {}

// Test for useless semi-colons
for ( $i = 0; ; $i++ ) {};

if (true) {};

while (true) {};

class ABC {}   ;  ;

switch ( $a ) {
    case 1:
        break;
    case 2:
        break;
    default:
        break;  ;
};

// Do not break closures and anonymous classes and curlies without scope owners.
$a = function () {};
$b = new class {};
echo $a{0};
