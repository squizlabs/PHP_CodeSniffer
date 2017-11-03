<?php

// Valid.
for ($i = 0; $i < 10; $i++) {
}

// Invalid.
for ( $i = 0; $i < 10; $i++ ) {
}

for ($i = 0;  $i < 10;  $i++) {
}

for ($i = 0 ; $i < 10 ; $i++) {
}

for ($i = 0;$i < 10;$i++) {
}

// The works.
for ( $i = 0 ;  $i < 10 ;  $i++ ) {
}

for ($i = 0; $i < 10;) {
}

for ($i = 0; $i < 10; ) {
}

for ($i = 0; ; $i++) {
}
for ($i = 0;; $i++) {
}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for ($i = 0; $i < 10; $i++) {}
for ( $i = 0; $i < 10; $i++ ) {}
for (  $i = 0; $i < 10; $i++  ) {}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0
