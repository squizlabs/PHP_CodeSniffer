<?php

/*
 * Test empty statement: two consecutive semicolons without executable code between them.
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

<input name="<?php ; ?>" />
