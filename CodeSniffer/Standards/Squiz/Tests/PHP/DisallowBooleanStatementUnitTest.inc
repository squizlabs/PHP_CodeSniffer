<?php

trySomething() || trySomethingElse();
if (trySomething() === FALSE) {
    trySomethingElse();
}

$success || fail();
if ($success === FALSE) {
    fail();
}

$foo = ($bar || $foo);

doSomething() || die();

if ($something || somethingElse()) {
    while ($foo && $bar) {
    }

    do {
        // Code here.
    }
    while ($foo && $bar);
}

?>