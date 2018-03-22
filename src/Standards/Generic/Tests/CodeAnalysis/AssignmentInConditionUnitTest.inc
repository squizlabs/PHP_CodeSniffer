<?php

// OK.
if ($a === 123) {
} elseif ($a == 123) {
} elseif ($a !== 123) {
} elseif ($a != 123) {}

function abc( $a = 'default' ) {}
if (in_array( $a, array( 1 => 'a', 2 => 'b' ) ) ) {}

switch ( $a === $b ) {}
switch ( true ) {
	case $sample == 'something':
		break;
}

for ( $i = 0; $i == 100; $i++ ) {}
for ( $i = 0; $i >= 100; $i++ ) {}
for ( $i = 0; ; $i++ ) {}
for (;;) {}

do {
} while ( $sample == false );

while ( $sample === false ) {}

// Silly, but not an assignment.
if (123 = $a) {}
if (strtolower($b) = $b) {}
if (array( 1 => 'a', 2 => 'b' ) = $b) {}

if (SOME_CONSTANT = 123) {
} else if(self::SOME_CONSTANT -= 10) {}

if ( $a() = 123 ) {
} else if ( $b->something() = 123 ) {
} elseif ( $c::something() = 123 ) {}

switch ( true ) {
	case 'something' = $sample:
		break;
}

// Assignments in condition.
if ($a = 123) {
} elseif ($a = 'abc') {
} else if( $a += 10 ) {
} else if($a -= 10) {
} else if($a *= 10) {
} else if($a **= 10) {
} else if($a /= 10) {
} else if($a .= strtolower($b)) {
} else if($a %= SOME_CONSTANT) {
} else if($a &= 2) {
} else if($a |= 2) {
} else if($a ^= 2) {
} else if($a <<= 2) {
} else if($a >>= 2) {
} else if($a ??= $b) {
} elseif( $a = 'abc' && $b = 'def' ) {
} elseif(
    $a = 'abc'
	&& $a .= 'def'
) {}

if ($a[] = 123) {
} elseif ($a['something'] = 123) {
} elseif (self::$a = 123) {
} elseif (parent::$a *= 123) {
} elseif (static::$a = 123) {
} elseif (MyClass::$a .= 'abc') {
} else if( $this->something += 10 ) {}

switch ( $a = $b ) {}
switch ( true ) {
	case $sample = 'something':
		break;

	case $sample = 'something' && $a = $b:
		break;
}

for ( $i = 0; $i = 100; $i++ ) {}
for ( $i = 0; $i = 100 && $b = false; $i++ ) {}

do {
} while ( $sample = false );

while ( $sample = false ) {}

if ($a = 123) :
endif;
