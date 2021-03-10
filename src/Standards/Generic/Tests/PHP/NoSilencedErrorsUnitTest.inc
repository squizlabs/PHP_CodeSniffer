<?php
/**
 * @see something
 */
if (@in_array($array, $needle))
{
    echo '@';
}

$hasValue = @in_array(haystack: $array, needle: $needle);
