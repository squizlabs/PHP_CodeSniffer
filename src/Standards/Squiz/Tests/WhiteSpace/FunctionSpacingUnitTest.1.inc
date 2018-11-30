<?php
/**
* Comment
*/
use Foo\{function bar, const BAZ};

include $blah;


/**
* Comment
*/


/**
* Comment
*/
function foo()
{
}//end foo()

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
     * The tag that this element represents (omitting the @ symbol).
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

// phpcs:set Squiz.WhiteSpace.FunctionSpacing spacing 1

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

// phpcs:set Squiz.WhiteSpace.FunctionSpacing spacing 2

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

class MyClass
{
    function func1() {
    }
}


function test() {

}//end test()


if ($foo) {
    /**
     * Comment
     */
    function foo() {
        // Code here
    }
    /**
     * Comment
     */
    function bar() {
        // Code here
    }
}

// foo
function foo() {
}


// phpcs:disable Standard.Category.Sniff -- for reasons.
function bar() {
}


// phpcs:set Squiz.WhiteSpace.FunctionSpacing spacingBeforeFirst 0
// phpcs:set Squiz.WhiteSpace.FunctionSpacing spacingAfterLast 0
class MyClass
{
    protected $tag = '';

    /**
     * Function comment.
     *
     * @return boolean
     */
    private static function func1() {

    }//end func1()

    /**
     * Function comment.
     *
     * @return boolean
     */
    private static function func2() {

    }//end func1()


}//end class

class MyClass {
    function a(){}
    function b(){}
    function c(){}
}

// phpcs:set Squiz.WhiteSpace.FunctionSpacing spacing 1
// phpcs:set Squiz.WhiteSpace.FunctionSpacing spacingBeforeFirst 1
// phpcs:set Squiz.WhiteSpace.FunctionSpacing spacingAfterLast 1

class MyClass {
    function a(){}
    function b(){}
    function c(){}
}

$util->setLogger(new class {
    public function a(){}
    private function b(){}
    protected function c(){}
});

?>

<?php
function functionInEmbeddedPHP() {

}

// Make sure the indentation for the function comment does not get removed.
class MyClass
{

    protected $tag = '';



    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1() {}

}//end class

class MyClass
{



    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1() {}

}//end class

class MyClass
{
    // Unrelated comment.
    /**
     * Function comment.
     *
     * @return boolean
     */
    function func1() {}
    // phpcs:disable Standard.Category.Sniff -- for reasons.
    /**
     * Function comment.
     *
     * @return boolean
     */
    function func2() {}
}//end class
