<?php
if (
    $expr1
    && $expr2
    && ($expr3
    || $expr4)
    && $expr5
) {
    // if body
} elseif (
    $expr1 &&
    ($expr3 || $expr4)
    && $expr5
) {
    // elseif body
} elseif (
    $expr1
    && ($expr3 || $expr4) &&
    $expr5
) {
    // elseif body
}

if ($expr1 || $expr2) {
}

do {
} while (
    $expr1 || $expr2
    || $expr3 ||
    $expr4
);

switch (
    $expr1
    && $expr2 &&
    $expr3 || $expr4 || $expr5 && $expr6 &&
    $expr7
) {
    // structure body
}
