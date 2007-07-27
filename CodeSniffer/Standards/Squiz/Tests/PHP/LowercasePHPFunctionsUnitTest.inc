<?php
Include $path;
Include_Once $path;
Require $path;
Require_Once $path;

List($one, $two) = $array;
if (IsSet($one)) {
    Echo 'hi';
    Print('hi');
    if (Empty($two)) {
        Break;
    }
    Unset($array);
    Continue;
}

$obj = New MyObj();
Eval($code);
Return false;
Exit();
Die();

Declare(ticks=1) {
}

if (Function_Exists('myFunction') === TRUE) {
    $retval = MyFunction(true);
    $keys = Array_Keys($array);
}

function getType() {}

?>
