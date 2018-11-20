<?php
if (! $someVar || ! $x instanceOf stdClass) {}
if (!$someVar || !$x instanceOf stdClass) {}
if (!     $someVar || !   $x instanceOf stdClass) {}
if (!foo() && (!$x || true)) {}
$var = !($x || $y);
$var = !           ($x || $y);
$var = ! /*comment*/ ($x || $y);

$baz = function () {
    return !  $bar;
};

if ( !
    ($x || $y)
) {
    return !$bar;
}

if ( ! // phpcs:ignore Standard.Cat.SniffName -- for reasons.
    ($x || $y)
) {}

// phpcs:set Generic.Formatting.SpaceAfterNot ignoreNewlines true
if ( !
    ($x || $y)
) {
    return !$bar;
}

if ( ! // phpcs:ignore Standard.Cat.SniffName -- for reasons.
    ($x || $y)
) {}
// phpcs:set Generic.Formatting.SpaceAfterNot ignoreNewlines false

// phpcs:set Generic.Formatting.SpaceAfterNot spacing 2
if (!  $someVar || !  $x instanceOf stdClass) {}
if (!$someVar || !$x instanceOf stdClass) {}
if (! $someVar || !       $x instanceOf stdClass) {}
if (!foo() && (!  $x || true)) {}
$var = ! ($x || $y);
$var = !           ($x || $y);

$baz = function () {
    return !  $bar;
};

if ( !
    ($x || $y)
) {
    return !$bar;
}

// phpcs:set Generic.Formatting.SpaceAfterNot spacing 0
if (!$someVar || !$x instanceOf stdClass) {}
if (! $someVar || !    $x instanceOf stdClass) {}
if (!   foo() && (!$x || true)) {}
$var = ! ($x || $y);
$var = ! /*comment*/ ($x || $y);

$baz = function () {
    return !  $bar;
};

if ( !
    ($x || $y)
) {
    return ! $bar;
}

if ( ! // phpcs:ignore Standard.Cat.SniffName -- for reasons.
    ($x || $y)
) {}

// phpcs:set Generic.Formatting.SpaceAfterNot ignoreNewlines true
if ( !
    ($x || $y)
) {
    return !    $bar;
}

if ( ! // phpcs:ignore Standard.Cat.SniffName -- for reasons.
    ($x || $y)
) {}
// phpcs:set Generic.Formatting.SpaceAfterNot ignoreNewlines false
// phpcs:set Generic.Formatting.SpaceAfterNot spacing 1
