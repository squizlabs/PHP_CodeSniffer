<?php
function func1() {

}//end func1()


function func2() {

}//end func2()

function func3() {

}//end func3()


class MyClass
{
    function func1() {

    }//end func1()


    function func2() {

    }//end func2()

    function func3() {

    }//end func3()
}//end class


interface MyInterface
{
    function func1();


    function func2();

    function func3();
}//end interface


class MyClass
{


    function func1() {

    }//end func1()


    function func2() {

    }//end func2()



    function func3() {

    }//end func3()


}//end class


interface MyInterface
{


    function func1();


    function func2();



    function func3();


}//end interface

class MyClass
{
    function func1() {

    }//end func1()
}//end class


interface MyInterface
{
    function func1();
}//end interface


class MyClass
{

    /**
     * The tag that this element represents (omiting the @ symbol).
     *
     * @var string
     */
    protected $tag = '';


    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1() {

    }//end func1()

    /**
     * Function comment.
     *
     * @return boolean
     */
    function func2() {

    }//end func2()


    /**
     * Function comment.
     *
     * @return boolean
     */
    function func3() {

    }//end func3()

}//end class


interface MyInterface
{

    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1();



    /**
     * Function comment.
     *
     * @return boolean
     */
    function func2();


    /**
     * Function comment.
     *
     * @return boolean
     */
    function func3();

}//end interface

class MyClass
{

    protected $tag = '';

    protected $tag = '';


    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1() {

    }//end func1()


}//end class

// @codingStandardsChangeSetting Squiz.WhiteSpace.FunctionSpacing spacing 1

interface MyInterface
{

    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1();


    /**
     * Function comment.
     *
     * @return boolean
     */
    function func2();

    /**
     * Function comment.
     *
     * @return boolean
     */
    function func3();


}//end interface

class MyClass
{

    protected $tag = '';

    protected $tag = '';

    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1() {

    }//end func1()


}//end class

// @codingStandardsChangeSetting Squiz.WhiteSpace.FunctionSpacing spacing 2

// Closures should be ignored.
preg_replace_callback(
    '~-([a-z])~',
    function ($match) {
        return strtoupper($match[1]);
    },
    'hello-world'
);

$callback = function ($bar) use ($foo)
            {
                $bar += $foo;
            };
?>
