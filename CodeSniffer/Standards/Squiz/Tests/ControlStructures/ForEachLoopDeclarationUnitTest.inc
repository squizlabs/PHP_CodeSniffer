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

foreach (${something}AS$blah=>$that) {
}

// The works.
Foreach (  $something   aS   $blah    =>   $that   ) {
}

?>