<?php
// Some code goes here.
$code = 'hello';

// This comment contains # multiple
// hash signs (#).
$code = 'hello';

/*
 * Here is a small function comment.
 */
function test()
{
    // Some code goes here.
    $code = 'hello';

    # This comment is banned.
    $code = 'hello';

}//end test()

/*
    A longer comment goes here.
    It spans multiple lines.
*/

# This is a long comment
# that is banned.



// some code goes here!

// This comment contains # multiple
// hash signs (#) but no
// full stop
$code = 'hello';

/*
 * Here is a small function comment
 */
function test()
{
    // Some code goes here

}//end test()

/*
    A longer comment goes here.
    It spans multiple lines!!
    Or does it?
*/

// 0This is a simple multi-line
// comment!
$code = 'hello';

//This is not valid.
$code = 'hello';

//  Neither is this!
$code = 'hello';

//
$code = 'hello';

/** Neither is this! **/
$code = 'hello';

class MyClass
{
    /**
     * Represents a left orientation for the widget.
     *
     * @var   integer
     * @since 4.0.0
     */
    const LEFT = 1;
}

/**
 * Comment should be ignored.
 *
 */
final class MyClass
{
    /**
     * Comment should be ignored.
     *
     */
    final public function test() {}
}

// 这是一条测试评论
//     -> One
//         -> One.One
//     -> Two

/*
    Here is some inline example code:
        -> One
            -> One.One
        -> Two
*/

/**
 * Comment should be ignored in PHP 5.4.
 *
 */
trait MyTrait {

}

$foo = 'foo'; // Var set to foo.

echo $foo;

//	Comment here.
echo $foo;

/**
 * Comments about the include
 */
include_once($blah);

// some comment without capital or full stop
echo $foo; // An unrelated comment.

// An unrelated comment.
echo $foo; // some comment without capital or full stop

class Foo
{
    // This is fine.

    /**
     * Spacing is ignored above.
     */
    function bar(){}
}

if ($foo) {
}//end if
// Another comment here.
$foo++;

if ($foo) {
}//end if
// another comment here.
$foo++;

/**
 * Comment should be ignored, even though there is an attribute between the docblock and the class declaration.
 */

#[AttributeA]

final class MyClass
{
    /**
     * Comment should be ignored, even though there is an attribute between the docblock and the function declaration
     */
    #[AttributeA]
    #[AttributeB]
    final public function test() {}
}

/**
 * Comment should be ignored.
 *
 */
enum MyEnum {

}

/*
 * N.B.: The below test line must be the last test in the file.
 * Testing that a new line after an inline comment when it's the last non-whitespace
 * token in a file, does *not* throw an error as this would conflict with the common
 * "new line required at end of file" rule.
 */

// For this test line having an empty line below it, is fine.
