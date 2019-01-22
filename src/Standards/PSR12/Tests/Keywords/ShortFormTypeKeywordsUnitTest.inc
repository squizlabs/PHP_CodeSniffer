<?php
$bar = (bool) $foo;
$bar = (boolean) $foo;
$bar = (Bool) $foo;
$bar = (BOOLEAN) $foo;
$bar = (int) $foo;
$bar = (integer) $foo;
$bar = (INT) $foo;

// Test recognition with whitespace within the cast.
$bar = (  bool  ) $foo;
$bar = ( int ) $foo;
$bar = (     boolean   	    ) $foo;
$bar = (  integer) $foo;
