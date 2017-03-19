<?php
if ($value === TRUE || $other === FALSE) {
}

if ($value === TRUE OR $other === FALSE) {
}

if ($value === TRUE && $other === FALSE) {
}

if ($value === TRUE and $other === FALSE) {
}

if ($one === TRUE && ($two === TRUE || $three === TRUE)) {
}

if ($one === TRUE AND ($two === TRUE or $three === TRUE)) {
}

if ($a ^ $b) {
}

if ($a xor $b) {
}

if ($a XOR $b) {
}

$c = $a and $b;
$c += $a and $b;
$c -= $a and $b;
$c *= $a and $b;
$c **= $a and $b;
$c /= $a and $b;
$c .= $a and $b;
$c %= $a and $b;
$c &= $a and $b;
$c |= $a and $b;
$c ^= $a and $b;
$c ?? $a and $b;
$c ??= $a and $b;
$c <<= $a and $b;
$c >>= $a and $b;

$c = ($a and $b);
$c = ($a and $c ?? ($c or $d));
$c = ($a and ($b ?? $c));
$foo = ($a and ($b ?? $c) ?? $d);
$foo = ($a and ($b ?? $c) or $d);
$foo = ($a and ($b ?? $c) or $d ?? $e);

?>
