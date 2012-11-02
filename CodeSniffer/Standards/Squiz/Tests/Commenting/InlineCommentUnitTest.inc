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

// Here is some inline example code:
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
?>
