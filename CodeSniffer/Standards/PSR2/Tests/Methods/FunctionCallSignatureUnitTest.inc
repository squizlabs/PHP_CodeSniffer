<?php
somefunction($foo, $bar, [
    // ...
], $baz);

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
}, array(
    '1',
    '2',
    '3',
));

somefunction($foo, $bar, [
    // ...
    ],
$baz);

somefunction(
    $foo,
    $bar,
    [
        // ...
    ],
    $baz
);


?>