<?php

/* testNotAVariable */
return $something;

/* testNoParenthesis */
echo $something;

/* testNoParenthesisOwner */
echo ($something + 1);

/* testOwnerNotForeach */
if ($something + 1 > 10) {}

/* testForeachWithoutAs */
foreach($something) {} // Intentional parse error.

/* testForeachVarBeforeAs */
foreach($something as $k => $v) {}

/* testForeachVarAfterAs */
foreach($array as $something) {}

/* testForeachVarAfterAsKey */
foreach($array as $something => $value) {}

/* testForeachVarAfterAsValue */
foreach($array as $key => $something) {}

/* testForeachVarAfterAsList */
foreach($array as [$something, $else]) {}

/* testNestedForeachVarAfterAs */
add_action('something', function($array) {
    foreach($array as $something) {}
    return;
});

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
foreach ($array as $something {}
