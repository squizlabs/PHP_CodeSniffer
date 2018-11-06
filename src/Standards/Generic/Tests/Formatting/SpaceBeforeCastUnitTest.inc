<?php

$a = $a+(bool) $b;
$a = function_call((bool) $b);
if ((bool) $a ===(bool) $b) {}

$var = (int) $var2;
$var =(int) $var2;
$var =     (int) $var2;

$var = (integer) $var2;
$var =(integer) $var2;
$var =    (integer) $var2;

$var = (string) $var2;
$var =(string) $var2;
$var =	 (string) $var2;

$var = (float) $var2;
$var =(float) $var2;
$var =			 (float) $var2;

$var = (double) $var2;
$var =(double) $var2;
$var =  (double) $var2;

$var = (real) $var2;
$var =(real) $var2;
$var =    (real) $var2;

$var = (array) $var2;
$var =(array) $var2;
$var =   (array) $var2;

$var = (bool) $var2;
$var =(bool) $var2;
$var =    (bool) $var2;

$var = (boolean) $var2;
$var =(boolean) $var2;
$var =  (boolean) $var2;

$var = (object) $var2;
$var =(object) $var2;
$var =  (object) $var2;

$var = (unset) $var2;
$var =(unset) $var2;
$var =   (unset) $var2;

$var = b"binary $foo";
$var =b"binary string";
$var =   b'binary string';
$var = (binary) $string;
$var =(binary) $string;
$var =    (binary) $string;

$var = array(
	(bool) $a,
	array(
        (int) $b,
	),
);

(bool) $a ? echo $b : echo $c;
