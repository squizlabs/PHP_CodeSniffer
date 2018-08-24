<?php

// Good
function foo() {
	// no return
}

// Bad - only returns within if
function bar(){
	if(false) {
		return "ok";
	}
}

// Bad - only returns within if
$x = function($foo) {
	if($foo) {
		return $foo;
	}
};

// Good - throws or returns
$x = function($foo) {
	if($foo) {
		return $foo;
	}

	throw new \Exception;
};

// Bad - returns with a switch
function($foo) {
	switch($foo) {
		case "a":
			return 1;
		case "b":
			return 2;
	}
}

// Good
function($foo) {
	switch($foo) {
		case "a":
			return 1;
		case "b":
			return 2;
		default:
			return 0;
	}
}

// Good, throws exception at root
function($foo) {
	switch($foo) {
		case "a":
			return 1;
		case "b":
			return 2;
	}

	throw new \Exception;
}

// Bad - only returns within an else
function($foo) {
	if($a) {

	}else{
		return 2;
	}
}

// Bad - only returns value within an else
function($foo) {
	if($a) {
		return;
	}else{
		return 2;
	}
}