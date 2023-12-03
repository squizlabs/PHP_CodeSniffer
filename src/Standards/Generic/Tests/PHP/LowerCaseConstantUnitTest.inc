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
new \Zend\Log\Writer\NULL();

namespace False;

class True extends Null implements False {}

use True\Something;
use Something\True;
class MyClass
{
    public function myFunction()
    {
        $var = array('foo' => new True());
    }
}

$x = $f?FALSE:true;
$x = $f? FALSE:true;

class MyClass
{
    // Spice things up a little.
    const TRUE = false;
}

var_dump(MyClass::TRUE);

function tRUE() {}

$input->getFilterChain()->attachByName('Null', ['type' => Null::TYPE_STRING]);

// Issue #3332 - ignore type declarations, but not default values.
class TypedThings {
	const MYCONST = FALSE;

	public int|FALSE $int = FALSE;
	public Type|NULL $int = new MyObj(NULL);

	private function typed(int|FALSE $param = NULL, Type|NULL $obj = new MyObj(FALSE)) : string|FALSE|NULL
	{
		if (TRUE === FALSE) {
			return NULL;
        }
    }
}

$cl = function (int|FALSE $param = NULL, Type|NULL $obj = new MyObj(FALSE)) : string|FALSE|NULL {};
