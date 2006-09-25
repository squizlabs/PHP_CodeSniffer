<?php

$result = myFunction();
$result = myFunction($arg1, $arg2);
$result = myFunction($arg1,$arg2);
$result = myFunction($arg1 , $arg2);
$result = myFunction($arg1 ,  $arg2);
$result = myFunction($arg1, $arg2, $arg3,$arg4, $arg5);
$result = myFunction($arg1, $arg2, $arg3, $arg4, $arg5);
$result = myFunction($arg1, $arg2 = array());
$result = myFunction($arg1 , $arg2 =array());
$result = myFunction($arg1 , $arg2= array());
$result = myFunction($arg1 , $arg2=array());

$result = myFunction($arg1, 
                     $arg2 = array(), 
                     $arg3, 
                     $arg4, 
                     $arg5);

throw new Exception("This is some massive string for a message", 
                         $cause);

// Function definitions are ignored
function myFunction($arg1,$arg2)
{
}

function myFunction    ($arg1,$arg2)
{
}

function myFunction($arg1=1,$arg2=2)
{
}


function myFunction($arg1 = 1,$arg2 = 2)
{
}

$key = array_search($this->getArray(),$this->arrayMap);
$this->error($obj->getCode(),$obj->getMessage(),$obj->getFile(),$obj->getLine());

?>
