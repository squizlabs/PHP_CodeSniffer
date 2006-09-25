<?php

test();
test($arg, $arg2);
test ();
test( );
test() ;
test( $arg);
test( $arg );
test ( $arg );

if (is_array($arg) === true) {

}

$something = get($arg1, $arg2);
$something = get($arg1, $arg2) ;
$something = get($arg1, $arg2)   ;

// No errors as this test only checks for function calls.
public function test ()
{

}
?>
