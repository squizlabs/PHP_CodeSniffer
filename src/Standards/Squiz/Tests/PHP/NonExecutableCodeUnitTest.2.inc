<?php

exit();


// Errors are thrown from here down from the exit() above.
foreach ($vars as $var) {
    if ($something === TRUE) {
        break;
        break;
    }
}

exit();

function test() {
    echo 'no error';
}

class myClass {
    function myFunc() {
        echo 'no error';
    }
}

function bar() {
    return function() {
        echo "1";
    };
}

class HttpStatus
{
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
}

interface ABC {
	public function noError($name, $var);
}

trait Something {
	function getReturnType() {
	    echo 'no error';
	}
}

enum Something {
    function getReturnType() {
        echo 'no error';
    }
}

$a = new class {
    public function log($msg)
    {
        echo 'no error';
    }
};

interface MyInterface {
