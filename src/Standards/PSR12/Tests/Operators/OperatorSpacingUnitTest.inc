<?php
if ($a    ===$b) {
    $foo = $bar ??$a?? $b;
} elseif ($a> $b) {
    $variable =$foo ? 'foo' :'bar';
    $variable.='text'.'text';
}

$foo+= $a&$b;
$foo = $a|$b;
$foo =$a^$b;
$foo = ~$a;
$foo   *=$a<<$b;
$foo = $a>>$b;

function foo(&$a,& $b) {}

$foo = $a   and$b;
$foo = $a or   $b;
$foo = $a xor$b;
$foo = !$a;
$foo = $a&&$b;
$foo = $a||$b;

$foo = $a instanceof Foo;
$foo = $a instanceof$b;

$foo .= 'hi'
     .= 'there';

$foo .= 'hi'
.= 'there';

$foo .= 'hi' // comment
.= 'there';

$foo/*comment*/=/*comment*/$a/*comment*/and/*comment*/$b;

$foo .=//comment
'string' .=/*comment*/
'string';

$foo = $foo ?: 'bar';
$foo = $foo?:'bar';

try {
} catch (ExceptionType1|ExceptionType2 $e) {
}
