<?php
function getWidget($type)
{
    eval('$obj = new '.$type.'();');
    return $obj;

}//end getWidget()

function getWidget2($type)
{
    $string = '$obj = new '.$type.'();';
    eval($string);
    eval('$string = "";');
    return $obj;

}//end getWidget2()

function getWidget3($type)
{
    $string = '$obj = new ';
    eval($string.$type.'();');
    return $obj;

}//end getWidget3()

?>