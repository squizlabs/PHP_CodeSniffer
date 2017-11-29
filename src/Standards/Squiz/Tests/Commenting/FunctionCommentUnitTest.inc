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

    }//end missingDescription()


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
 * @param string &$str The string passed in by reference
 * @param string $foo  The string passed in by reference.
 *
 * @return void
 * @return
 */
public function functionOutsideClass(&$str, &$foo)
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
     * @return string[]
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
 * Cant use resource for type hint.
 *
 * @param resource $test An argument.
 *
 * @return mixed
 */
function test($test)
{
   return $test;

}//end test()

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

/**
 * Contains closure.
 *
 * @return void
 */
public function containsClosure()
{
    function ($e) {
        return new Event($e);
    },

}//end containsClosure()

/**
 * 这是一条测试评论.
 *
 * @return void
 */
public function test()
{

}//end variableArgs()

/**
 * Uses callable.
 *
 * @param callable $cb Test parameter.
 *
 * @return void
 */
public function usesCallable(callable $cb) {
    $cb();
}//end usesCallable()

/**
 * Creates a map of tokens => line numbers for each token.
 *
 * @param array  $tokens    The array of tokens to process.
 * @param object $tokenizer The tokenizer being used to process this file.
 * @param string $eolChar   The EOL character
 *                          to use for splitting strings.
 *
 * @return void
 * @throws Exception If something really bad
 *                   happens while doing foo.
 */
public function foo(array &$tokens, $tokenizer, $eolChar)
{

}//end foo()

/**
 * Some description.
 *
 * @param \Vendor\Package\SomeClass       $someclass  Some class.
 * @param \OtherVendor\Package\SomeClass2 $someclass2 Some class.
 * @param \OtherVendor\Package\SomeClass2 $someclass3 Some class.
 *
 * @return void
 */
public function foo(SomeClass $someclass, \OtherVendor\Package\SomeClass2 $someclass2, SomeClass3 $someclass3)
{
}

/**
 * Gettext.
 *
 * @return string
 */
public function _() {
    return $foo;
}

class Baz {
    /**
     * The PHP5 constructor
     *
     * No return tag
     */
    public function __construct() {

    }
}

/**
 * Test
 *
 * @return void
 * @throws E 
 */
function myFunction() {}

/**
 * Yield test
 *
 * @return integer
 */
function yieldTest()
{
    for ($i = 0; $i < 5; $i++) {
        yield $i;
    }
}

/**
 * Yield test
 *
 * @return void
 */
function yieldTest()
{
    for ($i = 0; $i < 5; $i++) {
        yield $i;
    }
}

/**
 * Using "array" as a type hint should satisfy a specified array parameter type.
 *
 * @param MyClass[] $values An array of MyClass objects.
 *
 * @return void
 */
public function specifiedArray(array $values) {

}// end specifiedArray()

/**
 * Using "callable" as a type hint should satisfy a "callback" parameter type.
 *
 * @param callback $cb A callback.
 *
 * @return void
 */
public function callableCallback(callable $cb) {

}// end callableCallback()

/**
 * PHP7 type hints.
 *
 * @param string  $name1 Comment.
 * @param integer $name2 Comment.
 * @param float   $name3 Comment.
 * @param boolean $name4 Comment.
 *
 * @return void
 */
public function myFunction (string $name1, int $name2, float $name3, bool $name4) {
}

/**
 * Variadic function.
 *
 * @param string $name1    Comment.
 * @param string ...$name2 Comment.
 *
 * @return void
 */
public function myFunction(string $name1, string ...$name2) {
}


/**
 * Variadic function.
 *
 * @param string $name1 Comment.
 * @param string $name2 Comment.
 *
 * @return void
 */
public function myFunction(string $name1, string ...$name2) {
}

/**
 * Return description function + correct type.
 *
 * @return integer This is a description.
 */
public function myFunction() {
    return 5;
}

/**
 * Return description function + incorrect type.
 *
 * @return int This is a description.
 */
public function myFunction() {
    return 5;
}

/**
 * Return description function + no return.
 *
 * @return void This is a description.
 */
public function myFunction() {
}

/**
 * Return description function + mixed return.
 *
 * @return mixed This is a description.
 */
public function myFunction() {
}

/**
 * Function comment.
 *
 * @param $bar
 *   Comment here.
 * @param ...
 *   Additional arguments here.
 *
 * @return
 *   Return value
 *
 */
function foo($bar) {
}

/**
 * Do something.
 *
 * @return void
 */
public function someFunc(): void
{
    $class = new class
    {
        /**
         * Do something.
         *
         * @return string
         */
        public function getString(): string
        {
            return 'some string';
        }
    };
}

/**
 * Return description function + mixed return types.
 *
 * @return bool|int This is a description.
 */
function returnTypeWithDescriptionA()
{
    return 5;

}//end returnTypeWithDescriptionA()


/**
 * Return description function + mixed return types.
 *
 * @return real|bool This is a description.
 */
function returnTypeWithDescriptionB()
{
    return 5;

}//end returnTypeWithDescriptionB()


/**
 * Return description function + lots of different mixed return types.
 *
 * @return int|object|string[]|real|double|float|bool|array(int=>MyClass)|callable And here we have a description
 */
function returnTypeWithDescriptionC()
{
    return 5;

}//end returnTypeWithDescriptionC()


/**
 * Return description function + lots of different mixed return types.
 *
 * @return array(int=>bool)|\OtherVendor\Package\SomeClass2|MyClass[]|void And here we have a description
 */
function returnTypeWithDescriptionD()
{

}//end returnTypeWithDescriptionD()

/**
 * Yield from test
 *
 * @return int[]
 */
function yieldFromTest()
{
    yield from foo();
}

/**
 * Audio
 *
 * Generates an audio element to embed sounds
 *
 * @param  mixed $src                  Either a source string or
 * an array of sources.
 * @param  mixed $unsupportedMessage   The message to display
 * if the media tag is not supported by the browser.
 * @param  mixed $attributes           HTML attributes.
 * @return string
 */
function audio(
    $src,
    $unsupportedMessage = '',
    $attributes = '',
)
{
    return 'test';
}

/**
 * Test function
 *
 * @return array
 */
function returnArrayWithClosure()
{
    function () {
        return;
    };

    return [];
}

/**
 * Test function
 *
 * @return array
 */
function returnArrayWithAnonymousClass()
{
    new class {
        /**
         * @return void
         */
        public function test() {
            return;
        }
    };

    return [];
}

/**
 * @return void
 */
function returnVoidWithClosure()
{
    function () {
        return 1;
    };
}

/**
 * @return void
 */
function returnVoidWithAnonymousClass()
{
    new class {
        /**
         * @return integer
         */
        public function test()
        {
            return 1;
        }
    };
}

class TestReturnVoid
{
    /**
     * @return void
     */
    public function test()
    {
        function () {
            return 4;
        };
    }
}

/**
 * Comment here.
 *
 * @param integer $a This is A.
 * @param ?array  $b This is B.
 *
 * @return void
 */
public static function foo(?int $a, ?array $b) {}

/**
 * Comment here.
 *
 * @param object $a This is A.
 * @param object $b This is B.
 *
 * @return void
 */
public function foo(object $a, ?object $b) {}
