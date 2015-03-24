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

// @codingStandardsChangeSetting Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// @codingStandardsChangeSetting Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for ($i = 0; $i < 10; $i++) {}
for ( $i = 0; $i < 10; $i++ ) {}
for (  $i = 0; $i < 10; $i++  ) {}
// @codingStandardsChangeSetting Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// @codingStandardsChangeSetting Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0
