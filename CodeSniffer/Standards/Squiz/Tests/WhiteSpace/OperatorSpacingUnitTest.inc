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
if ($result&4) {
    if ($result |4) {
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

// This is all valid.
$boo = -$foo;
function foo($boo = -1) {}
$foo = array('boo' => -1);
?>
