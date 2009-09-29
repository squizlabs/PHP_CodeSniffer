<?php
$var = TRUE;
$var = ($foo === $bar);
$var = ($foo * $bar);
$var = !$foo;
$var = ($foo || $bar);
$var = ($foo === TRUE);
$var = ($foo === TRUE
        || $bar === FALSE);
$var = (!$foo);

$var = is_array($foo);
$var = myFunction($one, $two);
$var = myFunction(
        'one',
        'two'
       );

for ($i = ($stackPtr + 1); $i < $endStatement; $i++) {
}

// This condition is allowed by this sniff.
$var = myFunction(!$var);
?>
