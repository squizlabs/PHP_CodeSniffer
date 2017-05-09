<?php
$var = 'hello';
$var2 = 'hello';
$GLOBALS['var'] = 'hello';

function func1()
{
    global $var;
    global $var, $var2;
    echo $var;
    echo $GLOBALS['var'];
}
?>
