<?php

// Valid.
foreach ($something as $blah => $that) {
}

// Invalid.
foreach ( $something as $blah => $that ) {
}

foreach ($something   as   $blah => $that) {
}

foreach ($something as $blah   =>  $that) {
}

foreach (${something}as$blah=>$that) {
}

// The works.
foreach (  $something   as   $blah    =>   $that   ) {
}

?>