<?php

/**
 * This class is short
 */
class Short {
    function shortFoo(){}
}

/**
 * This class shoud trigger a warning
 */
class Warning {
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
    function warnFoo(){}
}

/**
 * This class should cause an error
 */
class Error {
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
    function errFoo(){}
}


/**
 * This class has 8 methods and 8 closures. The sniff should ignore closures.
 */
class ClosureClass {
    function closureFoo(){
        $func = function($a){return $a;};
    }
    function closureFoo(){
        $func = function($a){return $a;};
    }
    function closureFoo(){
        $func = function($a){return $a;};
    }
    function closureFoo(){
        $func = function($a){return $a;};
    }
    function closureFoo(){
        $func = function($a){return $a;};
    }
    function closureFoo(){
        $func = function($a){return $a;};
    }
    function closureFoo(){
        $func = function($a){return $a;};
    }
    function closureFoo(){
        $func = function($a){return $a;};
    }
}