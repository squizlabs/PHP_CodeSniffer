<?php

function test()
{
    $string = 'hello';
    $string = 'hello'; // Set string to hello.
    // Valid comment.
}

function test() // This is a function
{

}//end test()


class TestClass
{
    public $good = true; // Indeed.

}//end class

if (true || -1 == $b) { /* test */
}
$y = 10 + /* test */ -2;
