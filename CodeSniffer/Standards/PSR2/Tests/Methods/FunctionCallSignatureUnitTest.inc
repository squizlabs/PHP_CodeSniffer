<?php

// ok
somefunction1($foo, $bar, [
    // ...
], $baz);

// ok
$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
}, array(
    '1',
    '2',
    '3',
));

// error
somefunction2($foo, $bar, [
    // ...
    ],
$baz);

// ok
somefunction3(
    $foo,
    $bar,
    [
        // ...
    ],
    $baz
);

// ok
somefunction4('
    this should not
    give an error
    because it\'s actually
    one line call
    with multi-line string
');

// ok
somefunction5("hey,
multi-line string with some
extra args", $foo, 12);

// error
somefunction6('
    but args in a new line
    are not ok…
    ',
    $foo
);

?>
