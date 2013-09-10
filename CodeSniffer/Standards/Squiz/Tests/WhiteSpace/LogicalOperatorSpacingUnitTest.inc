<?php

if ($foo || $bar) {}
if ($foo||$bar && $baz) {}
if ($foo|| $bar&&$baz) {}
if ($foo  ||   $bar   && $baz) {}

// Spacing after || below is ignored as it is EOL whitespace.
if ($foo ||    
    $bar
    && $baz
) {
}

if ($foo||
    $bar
    &&$baz
) {
}

?>
