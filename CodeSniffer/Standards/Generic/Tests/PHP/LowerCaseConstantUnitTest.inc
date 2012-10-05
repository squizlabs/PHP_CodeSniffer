<?php

// True
function myFunction($arg1, $arg2=true)
{
}
function myFunction($arg1, $arg2=TRUE)
{
}
function myFunction($arg1, $arg2=True)
{
}

if ($variable === true) { }
if ($variable === TRUE) { }
if ($variable === True) { }


// False
function myFunction($arg1, $arg2=false)
{
}
function myFunction($arg1, $arg2=FALSE)
{
}
function myFunction($arg1, $arg2=False)
{
}

if ($variable === false) { }
if ($variable === FALSE) { }
if ($variable === False) { }


// Null
function myFunction($arg1, $arg2=null)
{
}
function myFunction($arg1, $arg2=NULL)
{
}
function myFunction($arg1, $arg2=Null)
{
}

if ($variable === null) { }
if ($variable === NULL) { }
if ($variable === Null) { }

$x = new stdClass();
$x->NULL = 7;

use Zend\Log\Writer\NULL as NullWriter;
new \Zend\Log\Writer\NULL()

class True extends Null implements False {}
?>
