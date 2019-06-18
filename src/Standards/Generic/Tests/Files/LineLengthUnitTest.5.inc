phpcs:set Generic.Files.LineLength ignoreComments true
phpcs:set Generic.Files.LineLength lineLimit 100
phpcs:set Generic.Files.LineLength absoluteLineLimit 120
<?php

/* This line is too long but will be ignored. This line is too long but will be ignored. */
if (($anotherReallyLongVarName === true) || (is_array($anotherReallyLongVarName) === false)) {} // phpcs:ignore Some.Sniff
