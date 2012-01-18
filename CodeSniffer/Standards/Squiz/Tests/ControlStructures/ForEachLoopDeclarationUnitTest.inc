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

foreach (${something}AS$blah=>$that) {
}

// The works.
foreach (  $something   aS   $blah    =>   $that   ) {
}

?>