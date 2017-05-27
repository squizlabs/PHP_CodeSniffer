<?php
// bad
$a = array(1, 2, 3);
$b = array(
    $a,
    [4, 5, 6],
);
$b = array(
    $a,
    array(4, 5, 6),
);

array
(
    array/* will be deleted */( ),
    array (1),  array   (3)
);

array; // evil syntax error

// good
/**
 * do array foo
 *
 * @param array $a
 */
function foo (array $a)
{
    //do stuff
}
