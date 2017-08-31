@codingStandardsChangeSetting Generic.Files.LineLength ignoreComments true
@codingStandardsChangeSetting Generic.Files.LineLength lineLimit 80
@codingStandardsChangeSetting Generic.Files.LineLength absoluteLineLimit 120
<?php

/* This line is fine. */
if($thisIsOk === true) {}

/* This line is too long but will be ignored. This line is too long but will be ignored. */
if (($anotherReallyLongVarName === true) || (is_array($anotherReallyLongVarName) === false)) {
