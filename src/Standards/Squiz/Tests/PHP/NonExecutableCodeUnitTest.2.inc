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

// Multiple statements are still one line of unreachable code, so should get
// only one complaint from this sniff. (Well, technically two here since there
// are two 'exit()' statements above, so one complaint from each of those. So,
// two here, but not six.)
echo 'one'; echo 'two'; echo 'three';

// A single statement split across multiple lines. Here we get complaints for
// each line, even though they're all part of one statement.
echo 'one' . 'two'
    . 'three' . 'four'
    . 'five' . 'six';

interface MyInterface {
