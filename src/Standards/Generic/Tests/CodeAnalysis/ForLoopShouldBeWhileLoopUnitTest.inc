<?php
for ($i = 0; $i < 10; $i++) {
    // Everything is fine
}

for (; $it->valid();) {
    $it->next();
}

for (;(($it1->valid() && $foo) || (!$it2->value && ($bar || false)));/*Could be ignored*/) {
    $it1->next();
    $it2->next();
}