<?php
if ($value === TRUE) {
} else if ($value === FALSE) {
}

if ($value == TRUE) {
} else if ($value == FALSE) {
}

if ($value) {
} else if (!$value) {
}

if (is_array($array) === TRUE) {
} else if (myFunction($value) === FALSE) {
}

if (is_array($array) == TRUE) {
} else if (myFunction($value) == FALSE) {
}

if (is_array($array)) {
} else if (!myFunction($value)) {
}

if ($value === TRUE || $other === FALSE) {
}

if ($value == TRUE || $other == FALSE) {
}

if ($value || !$other) {
}

if ($one === TRUE || $two === TRUE || $three === FALSE || $four === TRUE) {
}

if ($one || $two || !$three || $four) {
}

if ($var instanceof PHP_CodeSniffer) {
}

if (($var instanceof PHP_CodeSniffer) === false) {
}

if ($good && ($var instanceof PHP_CodeSniffer) === false && $good) {
}

if ($good === true && ($var instanceof PHP_CodeSniffer) === false) {
}

// Without brackets around inline IF condition.
$var1 === TRUE
    ? $var2 = 0
    : $var2 = 1;

$var1 === TRUE ? $var2 = 0 : $var2 = 1;
?>
<?php
$var1 === TRUE ? $var2 = 0 : $var2 = 1;

if ($var2 === TRUE) {
    $var1 === TRUE ? $var2 = 0 : $var2 = 1;
}
$var1 === TRUE ? $var2 = 0 : $var2 = 1;

$var1
    ? $var2 = 0
    : $var2 = 1;

$var1 ? $var2 = 0 : $var2 = 1;


$var1 ? $var2 = 0 : $var2 = 1;

if ($var2 === TRUE) {
    $var1 ? $var2 = 0 : $var2 = 1;
}
$var1 ? $var2 = 0 : $var2 = 1;

if ($value) {
} elseif (!$value) {
}

if (false === ($parent instanceof Foo) && ($parent instanceof Bar) === false) {
}

if (false === ($parent instanceof Foo) && $foo) {
}

while ($var1) {
}

while ($var1 === TRUE) {
}

do {

} while ($var1);

do {

} while ($var1 === TRUE);

for ($var1 = 10; $var1; $var1--) {
}

for ($var1 = 10; $var1 !== 0; $var1--) {
}

for ($var1 = ($var2 === 10); $var1; $var1--) {
}

while (TRUE) {
}

while (FALSE) {
}

$var = ($var1 === true) ? $var1 : "foobar";

$var = ($var1 == true) ? $var1 : "foobar";

$var = ($var1 === false) ? $var1 : "foobar";

$var = ($var1 == false) ? $var1 : "foobar";

$var = ($var1 === 0) ? $var1 : "foobar";

$var = ($var1 == 0) ? $var1 : "foobar";

function foo(string $bar, array $baz, ?MyClass $object) : MyClass {}

if (empty($argTags > 0)) {
}

myFunction($var1 === true ? "" : "foobar");
