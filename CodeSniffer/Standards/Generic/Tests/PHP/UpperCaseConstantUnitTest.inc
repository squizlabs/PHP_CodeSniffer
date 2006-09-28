<?php

// True
function myFunction($arg1, $arg2=TRUE)
{
}
function myFunction($arg1, $arg2=true)
{
}
function myFunction($arg1, $arg2=True)
{
}

if ($variable === TRUE) { }
if ($variable === true) { }
if ($variable === True) { }


// False
function myFunction($arg1, $arg2=FALSE)
{
}
function myFunction($arg1, $arg2=false)
{
}
function myFunction($arg1, $arg2=False)
{
}

if ($variable === FALSE) { }
if ($variable === false) { }
if ($variable === False) { }


// Null
function myFunction($arg1, $arg2=NULL)
{
}
function myFunction($arg1, $arg2=null)
{
}
function myFunction($arg1, $arg2=Null)
{
}

if ($variable === NULL) { }
if ($variable === null) { }
if ($variable === Null) { }

?>
