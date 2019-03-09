<?php
// Short array, not short list.
$var = [1,2,3];

list($a) = $array;
[$a] = $array;
list($a, list($b, $c, $d)) = $array;
[$a, [$b, $c, $d]] = $array;
[$a[], [$a[], $a[], $a[],],] = $array;
[
    $a,
    [
        $b,
        $c,
        $d,
    ],
] = $array;
