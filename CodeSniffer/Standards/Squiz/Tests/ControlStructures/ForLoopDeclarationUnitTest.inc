<?php

// Valid.
for ($i = 0; $i < 10; $i++) {
}

// Invalid.
For ( $i = 0; $i < 10; $i++ ) {
}

for ($i = 0;  $i < 10;  $i++) {
}

for ($i = 0 ; $i < 10 ; $i++) {
}

for ($i = 0;$i < 10;$i++) {
}

// The works.
For ( $i = 0 ;  $i < 10 ;  $i++ ) {
}

?>