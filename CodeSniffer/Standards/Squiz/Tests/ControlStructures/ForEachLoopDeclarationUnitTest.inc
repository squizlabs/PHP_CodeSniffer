<?php

// Valid.
foreach ($something as $blah => $that) {
}

// Invalid.
Foreach ( $something as $blah => $that ) {
}

foreach ($something   as   $blah => $that) {
}

foreach ($something as $blah   =>  $that) {
}

foreach (${something}as$blah=>$that) {
}

// The works.
Foreach (  $something   as   $blah    =>   $that   ) {
}

?>