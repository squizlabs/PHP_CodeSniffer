<?php
if (! $foo !== $bar) {
    return !$bar;
}

$foo = ! (1 === 3);

$baz = function () {
    return !  $bar;
};
