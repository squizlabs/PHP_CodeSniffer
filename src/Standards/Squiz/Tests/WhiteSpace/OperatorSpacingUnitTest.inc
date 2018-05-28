<?php

$result = 1 + 2;
$result = 1  + 2;
$result = 1  +   2;
$result = 1 +2;
$result = 1+ 2;
$result = 1+2;

$result = 1 - 2;
$result = 1  - 2;
$result = 1  -   2;
$result = 1 -2;
$result = 1- 2;
$result = 1-2;

$result = 1 * 2;
$result = 1  * 2;
$result = 1  *   2;
$result = 1 *2;
$result = 1* 2;
$result = 1*2;

$result = 1 / 2;
$result = 1  / 2;
$result = 1  /   2;
$result = 1 /2;
$result = 1/ 2;
$result = 1/2;

$result = 1 % 2;
$result = 1  % 2;
$result = 1  %   2;
$result = 1 %2;
$result = 1% 2;
$result = 1%2;
$result = '100%';

$result += 4;
$result+=4;
$result -= 4;
$result-=4;
$result /= 4;
$result/=4;
$result *=4;
$result*=4;

$result =& $thing;
if ($result & 4) {
    if ($result | 4) {
    }
}
if ($result&4 && $result  &  4) {
    if ($result |4 || $result  |  4) {
    }
}

$result = ((1 + 2) - (3 * 4 / 5) % 6);
$result = ((1+  2)  -  (3*4/5)  %  6);
return -1;
$file = '...'.substr($file, (($padding * -1) + 3));

$totalErrors   += $errors['errors'];
$totalWarnings +=  $errors['warnings'];

if (substr($line, 0, 3) === '/**') {
    $line = substr($line, 3);
} else if (substr($line, -2, 2) === '*/') {
    $line = substr(-2, 0, -2);
} else if ($line{0} === '*') {
    $line = substr($line, 1);
}

if ($pos === -1) {
}

for ($i=0; $i<=5; $i++) {
    $j^= $i;
    $k %=$i;
    $l&=$i;
    $m.= 'Hello ';
}

$z = ($a+ $b- $c* $d/ $e% $f^ $g);
$z = ($a +$b -$c *$d /$e %$f ^$g);

$a== $b && $c!= $d && $e=== $f && $g!== $h;
$i> $j && $k< $l && $m>= $n && $o<= $p && $q<> $r;

$a ==$b && $c !=$d && $e ===$f && $g !==$h;
$i >$j && $k <$l && $m >=$n && $o <=$p && $q <>$r;

function myFunction($variable=0, $var2='string') {}

if (index > -1) {
}

array_walk_recursive($array, function(&$item) use (&$something) {
});

$var = saveFile(&$model, &$foo);

// This is all valid.
$boo = -$foo;
function foo($boo = -1) {}
$foo = array('boo' => -1);
$x = $test ? -1 : 1;
$y = $test ? 1 : -1;
$z = $test ?: false;

$closureWithDefaultParameter = function (array $testArray=array()) {};

switch ($foo) {
	case -1:
		break;
}

$y = 1 * -1;
$y = -1 * 1;
$y = -1 * $var;
$y = 10 / -2;
$y = -10 / 2;
$y = (-10 / 2);
$y = (-10 / $var);
$y = 10 + -2;
$y = -10 + 2;

$a = $x?$y:$z;
$a = $x ? $y : $z;

$y = 1
   + 2  
   - 3;

$y = 1 + 
     2 - 
     3;

$y = 1
+ 2
- 3;

// phpcs:set Squiz.WhiteSpace.OperatorSpacing ignoreNewlines true
$y = 1
   + 2
   - 3;

$y = 1 +
     2 -
     3;

$y = 1
+ 2
- 3;
// phpcs:set Squiz.WhiteSpace.OperatorSpacing ignoreNewlines false

if (true || -1 == $b) {
}

$var = array(-1);
$var = [-1];
$var = [0, -1, -2];

$y = array(&$x);
$y = [&$x];
$y = array(&$a, &$b, &$c);
$y = [&$a, &$b, &$c];
$y = array(&$a => 1, 2 => &$b, &$c);
$y = [&$a => 1, 2 => &$b, &$c];

if ($a <=> $b) {
}

if ($a <=>$b) {
}

$a |=  $b;
$a **=  $b;
$a ??=  $b;

$a = +1;

function bar($boo = +1) {}

$username = $_GET['user']??'nobody';

function foo(string $bar, array $baz, ?MyClass $object) : MyClass {}

declare(strict_types=1);

function foo($c = ((BAR)?10:100)) {}

$res = $a ?: $b;
$res = $a ?:  $b;
$res = $a  ?: $b;
$res = $a  ?:  $b;

$res = $a ? : $b;
$res = $a ? :  $b;
$res = $a  ? : $b;
$res = $a  ? :  $b;

function foo(string $a = '', ?string $b = ''): ?string {}

// Issue #1605.
$text = preg_replace_callback(
		self::CHAR_REFS_REGEX,
		[ 'Sanitizer', 'decodeCharReferencesCallback' ],
		$text, /* limit */ -1, $count );

if (true || /* test */ -1 == $b) {}
$y = 10 + /* test */ -2;

// Issue #1604.
Hooks::run( 'ParserOptionsRegister', [
	&self::$defaults,
	&self::$inCacheKey,
	&self::$lazyOptions,
] );

$x = $foo ? function (): int {
    return 1;
} : $bar;

$x = $foo ? function ($foo)
    // comment
    : int {
    return 1;
} : $bar;

$x = $foo ? function ($foo) use /* comment */ ($bar): int {
    return 1;
} : $bar;

$x = !$foo ? $bar : function (): int {
    return 1;
};

$a =
    // Comment.
    [
        'a',
        'b',
    ];

$a =
// phpcs:ignore Standard.Category.Sniff -- for reasons.
[
    'a',
    'b',
];
