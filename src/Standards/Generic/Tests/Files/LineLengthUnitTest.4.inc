phpcs:set Generic.Files.LineLength ignoreComments true
phpcs:set Generic.Files.LineLength lineLimit 80
phpcs:set Generic.Files.LineLength absoluteLineLimit 120
<?php

/* This line is fine. */
if($thisIsOk === true) {}

/* This line is too long but will be ignored. This line is too long but will be ignored. */
if (($anotherReallyLongVarName === true) || (is_array($anotherReallyLongVarName) === false)) {}

if (($anotherReallyLongVarName === true) {} // This comment makes the line too long, but should be ignored.

if (($anotherReallyLongVarName === true) || (is_array($anotherReallyLongVarName) === false)) {} // Comment goes here.

if (($anotherReallyLongVarName === true) {} // phpcs:ignore Standard.Category.Sniff.ErrorCode1 -- for reasons
