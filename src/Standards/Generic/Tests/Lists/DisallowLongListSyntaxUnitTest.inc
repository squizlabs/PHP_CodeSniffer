<?php
list($a) = $array;
[$a] = $array;
list($a, list($b, $c, $d)) = $array;
[$a, [$b, $c, $d]] = $array;
list($a[], list($a[], $a[], $a[],),) = $array;
list(
    $a,
    list(
        $b,
        $c,
        $d,
    ),
) = $array;

list/*comment*/($a, $b, $c);

list = $array; // Intentional parse error.

// Intentional parse error. This has to be the last test in the file.
list(
