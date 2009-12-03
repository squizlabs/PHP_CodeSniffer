<?php

function __construct()
{
}//end __construct()

function myFunction()
{
}//end myFunction()

function ourFunction()
{
}//end ourFunction

function yourFunction()
{
}//end myFunction()

class TestClass
{
    function __construct()
    {
    }//end __construct()

    function myFunction()
    {
    }//end myFunction()

    function yourFunction()
    {
    }//end myFunction()

}//end class

abstract class TestClass
{
    abstract function myFunction();

    function ourFunction()
    {
    }//end myFunction()

    function yourFunction()
    {
    }//end yourFunction()

}//end class

interface TestClass
{
    function myFunction();
    function ourFunction();
    function yourFunction();

}//end interface

class TestClass
{
}

abstract class TestClass
{
}

interface TestClass
{
}

class MyClass
{
    public function myFunction();
}//end class

// Closures don't need end comments.
echo preg_replace_callback('~-([a-z])~', function ($match) { return strtoupper($match[1]); }, 'hello-world');

?>