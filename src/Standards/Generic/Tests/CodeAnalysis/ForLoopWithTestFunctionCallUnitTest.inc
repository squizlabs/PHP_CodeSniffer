<?php

$a = array(1, 2, 3, 4);
for ($i = 0; $i < count($a); $i++) {
    $a[$i] *= $i;
}

for ($i = 0, $c = sizeof($a); $i < $c; ++$i) {
    $a[$i] *= $i;
}

$it = new ArrayIterator($a);
for ($it->rewind(); $it->valid(); $it->next()) {
    echo $it->current();
}