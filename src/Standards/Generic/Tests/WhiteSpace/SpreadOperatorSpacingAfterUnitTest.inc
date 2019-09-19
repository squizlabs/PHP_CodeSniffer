<?php

function foo( &...$spread ) {
    bar( ...$spread );

    bar(
        [ ...$foo ],
        ...array_values($keyedArray)
    );
}

function bar( &   ... $spread ) {
    bar(...


        $spread
    );

    bar(
        [...  $foo ],.../*comment*/array_values($keyedArray)
    );
}

// phpcs:set Generic.WhiteSpace.SpreadOperatorSpacingAfter ignoreNewlines true
    bar(...
        $spread
    );
// phpcs:set Generic.WhiteSpace.SpreadOperatorSpacingAfter ignoreNewlines false

// phpcs:set Generic.WhiteSpace.SpreadOperatorSpacingAfter spacing 1
function foo( &... $spread ) {
    bar( ... $spread );

    bar(
        [ ... $foo ],
        ... array_values($keyedArray)
    );
}

function bar( &   ...$spread ) {
    bar(...
        $spread
    );

    bar(
        [...  $foo ],.../*comment*/array_values($keyedArray)
    );
}

// phpcs:set Generic.WhiteSpace.SpreadOperatorSpacingAfter spacing 2
function foo( &...  $spread ) {
    bar( ...  $spread );

    bar(
        [ ...  $foo ],
        ...  array_values($keyedArray)
    );
}

function bar( &   ... $spread ) {
    bar(...
        $spread
    );

    bar(
        [...    $foo ],.../*comment*/array_values($keyedArray)
    );
}

// phpcs:set Generic.WhiteSpace.SpreadOperatorSpacingAfter spacing 0

// Intentional parse error. This has to be the last test in the file.
function bar( ...
