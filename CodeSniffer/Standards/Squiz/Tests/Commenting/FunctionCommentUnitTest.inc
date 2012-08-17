<?php
class PHP_CodeSniffer_File
{

    /**
     * a simple function comment.
     *
     * long desc here
     *
     * @param bool   $stackPtr   The position in @ @unknown the stack of the token
     *                             that opened the scope
     * @param int    $detph    How many scope levels down we are.
     * @param string    $index    The index
     * @return
     * @throws
     */
    private function _functionCall($stackPtr, $depth=1, $index)
    {
        return $stackPtr;

    }//end _functionCall()

    //
    // Sample function comment
    //
    //
    //
    public function invalidCommentStyle()
    {

    }//end invalidCommentStyle()


    /**
     *
     * A simple function comment
     * Span multiple line
     *
     *
     * 0Long description with extra blank line before and after
     *
     *
     * @return void
     */
    public function extraDescriptionNewlines()
    {
        return true;
    }//end extraDescriptionNewlines()


    /**
     * -A simple function comment.
     * @return void
     */
    public function missingNewlinesBeforeTags()
    {
        return;
    }//end missingNewlinesBeforeTags()


    /**
     * Access tag should not be treated as a long description.
     *
     * @access public
     * @return void
     */
    public function accessTag()
    {

    }//end accessTag()

    /**
     * Constructor.
     *
     * No return tag
     */
    function PHP_CodeSniffer_File()
    {
        return;
    }


    /**
     * Destructor.
     *
     * No return tag too
     */
    function _PHP_CodeSniffer_File()
    {
        return;
    }


    /**
     * Destructor PHP5.
     */
    function __destruct()
    {
        return;
    }


    function missingComment()
    {
        return;
    }


    /**
     * no return tag.
     *
     */
    public function noReturn($one)
    {

    }//end noReturn()


    /**
     * Param not immediate.
     *
     * @return
     * @param   int   $threeSpaces
     * @param integer $superfluous
     * @param miss
     * @param
     */
    public function missingDescription($threeSpaces)
    {

    }//end missingDescroption()


    /**
     * Comments not capitalised.
     *
     * @param integer  $one    comment
     * @param array    $two    -comment
     * @param MyClas   $three  0comment
     *
     * @return void
     */
    public function oneSpaceAfterLongestVar($one, $two, MyClass $three)
    {

    }//end oneSpaceAfterLongestVar()


}//end class


/**
 * A simple function comment.
 *
 * @param string $str The string passed in by reference
 *
 * @return void
 * @return
 */
public function functionOutsideClass(&$str)
{
    return;
}//end functionOutsideClass()


function missingCommentOutsideClass()
{
    return;
}//end missingCommentOutsideClass()


?><?php
function tagBeforeComment()
{
    return;
}//end tagBeforeComment()


/**
 * no return tag.
 *
 * @see    FunctionCommentSniff.php
 */
public function noReturnOutsideClass()
{

}//end noReturnOutsideClass()


/**
 * Missing param comment.
 *
 * @param integer $one comment
 *
 * @see     wrong indent
 * @see
 * @return void
 * @extra  Invalid tag
 * @throws UnknownException unknown
 */
public function missingTwoParamComment($one, $two, $three)
{

}//end missingTwoParamComment()


/**
 * Missing return type.
 *
 * @throws ExceptionWithNoComment
 * @return
 */
public function missingReturnType()
{

}//end missingReturnType()


/**
 * Case-sensitive var type check with multiple return type.
 *
 * @param String  $a1 Comment here.
 * @param BoOL    $a2 Comment here.
 * @param INT     $a3 Comment here.
 * @param aRRay   $a4 Comment here.
 * @param real    $a5 Comment here.
 * @param double  $a6 Comment here.
 * @param Myclass $a7 Comment here.
 *
 * @return int|object|double|float|array(int=>MyClass)
 */
public function caseSensitive($a1, $a2, $a3, arRay $a4, $a5, $a6, myclas $a7)
{

}//end caseSensitive()


/**
 * More type hint check for custom type and array.
 *
 * @param array   $a1 Comment here.
 * @param array   $a2 Comment here.
 * @param MyClass $a3 Comment here.
 * @param MyClass $a4 Comment here.
 *
 * @return array(int => MyClass)
 */
public function typeHint(MyClass $a1, $a2, myclass $a3, $a4)
{
    return (3 => 'myclass obj');

}//end typeHint()


/**
 * Mixed variable type separated by a '|'.
 *
 * @param array|string $a1 Comment here.
 * @param mixed        $a2 Comment here.
 * @param string|array $a3 Comment here.
 * @param MyClass|int  $a4 Comment here.
 *
 * @return bool
 */
public function mixedType($a1, $a2, $a3, $a4)
{
    return true;

}//end mixedType()


/**
 * Array type.
 *
 * @param array(MyClass)         $a1 OK.
 * @param array()                $a2 Invalid type.
 * @param array(                 $a3 Typo.
 * @param array(int)             $a4 Use 'array(integer)' instead.
 * @param array(int => integer)  $a5 Use 'array(integer => integer)' instead.
 * @param array(integer => bool) $a6 Use 'array(integer => boolean)' instead.
 * @param aRRay                  $a7 Use 'array' instead.
 * @param string                 $a8 String with unknown type hint.
 *
 * @return int
 */
public function mixedArrayType($a1, $a2, array $a3, $a4, $a5, $a6, $a7, unknownTypeHint $a8)
{
    return 1;

}//end mixedArrayType()


/**
 */
function empty1()
{
}//end empty1()


/**
 *
 */
function empty2()
{
}//end empty2()


/**
 *
 *
 *
 */
function empty3()
{
}//end empty3


/**
 * @return boolean
 */
public function missingShortDescriptionInFunctionComment()
{
    return true;

}//end missingShortDescriptionInFunctionComment()


class Another_Class
{

    /**
     * Destructor should not include a return tag.
     *
     * @return void
     */
    function __destruct()
    {
        return;
    }

    /**
     * Constructor should not include a return tag.
     *
     * @return void
     */
    function __construct()
    {
        return;
    }

}//end class


/**
 * Comment param alignment test.
 *
 * @param string $varrr1 Comment1..
 * @param string  $vr2    Comment2.
 * @param string  $var3  Comment3..
 *
 * @return void
 */
public static function paramAlign($varrr1, $vr2, $var3)
{

}//end paramAlign()


/**
 * Comment.
 *
 * @param string $id    Comment.
 * @param array $design Comment.
 *
 * @return void
 */
public static function paint($id, array $design)
{

}//end paint()


/**
 * Adds specified class name to class attribute of this widget.
 *
 * @since  4.0.0
 * @return string
 */
public function myFunction()
{
    if ($condition === FALSE) {
        echo 'hi';
    }

}//end myFunction()


/**
 * Adds specified class name to class attribute of this widget.
 *
 * @return string
 */
public function myFunction()
{
    if ($condition === FALSE) {
        echo 'hi';
        return;
    }

    return 'blah';

}//end myFunction()


/**
 * Adds specified class name to class attribute of this widget.
 *
 * @return string
 */
public function myFunction()
{
    if ($condition === FALSE) {
        echo 'hi';
    }

    return 'blah';

}//end myFunction()

/**
 * Test function.
 *
 * @param string $arg1 An argument
 *
 * @access public
 * @return bool
 */

echo $blah;

function myFunction($arg1) {}

class MyClass() {
    /**
     * An abstract function.
     *
     * @return array(string)
     */
    abstract final protected function myFunction();
}

/**
 * Comment.
 *
 * @param mixed $test An argument.
 *
 * @return mixed
 */
function test($test)
{
   if ($test === TRUE) {
       return;
   }

   return $test;

}//end test()


/** Comment.
 *
 * @return mixed
 *
 */
function test()
{

}//end test()

/**
 * Comment.
 *
 * @param \other\ns\item $test An argument.
 *
 * @return mixed
 */
function test(\other\ns\item $test)
{
   return $test;

}//end test()

/**
 * Comment.
 *
 * @param item $test An argument.
 *
 * @return mixed
 */
function test(\other\ns\item $test)
{
   return $test;

}//end test()

/**
 * Comment.
 *
 * @param \first\ns\item $test An argument.
 *
 * @return mixed
 */
function test(\first\ns\item $test = \second\ns::CONSTANT)
{
   return $test;

}//end test()

/**
 * Comment.
 *
 * @param \first\item $test An argument.
 *
 * @return mixed
 */
function test(\first\ns\item $test = \second\ns::CONSTANT)
{
   return $test;

}//end test()

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

/**
 * Comment should end with '*', not '**' before the slash.
 **/
function test123() {

}

/**
 * Variable number of args.
 *
 * @param string $a1     Comment here.
 * @param string $a2     Comment here.
 * @param string $a2,... Comment here.
 *
 * @return boolean
 */
public function variableArgs($a1, $a2)
{
    return true;

}//end variableArgs()

?>
