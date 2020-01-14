<?php
$var = [1,2,3];
$var = [
    1,
    2,
    3
];
$var = [
    1,
    2,
    /* three */ 3,
];
$var = [
1,
        2,
    /* three */ 3,
 ];

$var = [
    1 => 'one',
    2 => 'two',
    3 => 'three'
];
$var = [
    1 => 'one',
    2 => 'two',
    /* three */ 3 => 'three',
];
$var = [
1 => 'one',
        2 => 'two',
    /* three */ 3 => 'three',
    ];

$var = array(
    'one' => function() {
        $foo = [1,2,3];
        $bar = [
            1,
            2,
            3];
    },
    'two' => 2,
);

return [
    [
        'foo' => true,
    ]
];

$array = [
    'foo' => 'foo',
    'bar' => $baz ?
        ['abc'] :
        ['def'],
    'hey' => $baz ??
        ['one'] ??
        ['two'],
    'fn' =>
        fn ($x) => yield 'k' => $x,
  $a ?? $b,
  $c ? $d : $e,
];

// phpcs:set Generic.Arrays.ArrayIndent indent 2

$var = [
1 => 'one',
        2 => 'two',
    /* three */ 3 => 'three',
    ];
