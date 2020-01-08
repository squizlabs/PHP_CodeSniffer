<?php
$var = [1,2   ,3,];
$var = [
    1   ,
    2,
    3
];

// phpcs:set Generic.Arrays.ArrayCommaSpacing ignoreHereDocAndNowDoc true
// This should not be fixed in order to avoid syntax error.
$values = [
<<<END
a
b
c
END
,
'd e f'
];

// phpcs:set Generic.Arrays.ArrayCommaSpacing ignoreHereDocAndNowDoc false
// This should now be fixed.
$values = [
<<<END
a
b
c
END
,
'd e f'
];
