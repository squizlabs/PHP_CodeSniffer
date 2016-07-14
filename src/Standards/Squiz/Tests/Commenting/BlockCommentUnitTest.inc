<?php

/*
    Comments need to be indented 4 space.
*/

/*
    same with multi-line
    comments.
*/

/*
    But they can:
        - be indented more than 4
        - but not less than 4
*/

/*
    - This is valid:
   Not indented correctly.
*/

/*
   Comments need to be indented 4 space.
*/

/*
    Comments need to be indented 4 space.
     Comments need to be indented 4 space.
   Comments need to be indented 4 space.
  Comments need to be indented 4 space.

    Comments need to be indented 4 space.
   Comments need to be indented 4 space.
*/

/*
    Block comments require a blank
    line after them.
*/
$code = 'should not be here';

/*
    Closing comment not aligned
    */

/*
    Closing comment not aligned
 */

/* Not allowed */

/*  Not allowed
    either.
*/

/* */

$code = 'should not be here';
/*

    Block comments require a blank
    line before them. */

/*
*/

/** Not allowed */

/**  Not allowed
    either.
**/

/*
    no capital
    letter.
*/

echo 'hi';

function func()
{
    echo 1;
    /**
       test
       here
    **/
    echo 'test';
    /**
        Test
        here
       **/

}//end func()

public static function test()
{
    /*
        Block comments do not require a blank line before them
        if they are after T_OPEN_CURLY_BRACKET.
    */

    $code = '';

}//end test()


public static function test()
{

    /*
        Block comments do not require a blank line before them
        if they are after T_OPEN_CURLY_BRACKET.
    */

    $code = '';

}//end test()

class MyClass
{

    /**
     * Comment should be ignored.
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

switch ($var) {
    case 'foo':
        /*
            Foo comment.
            This is a multiple
            line comment for Foo.
        */

        echo 'Foo';
    break;

    default:

        /*
            Foo comment.
            This is a multiple
            line comment for Foo.
        */

        echo 'Default';
    break;
}//end switch

/**
 * Comment should be ignored in PHP < 5.4.
 *
 */
trait MyTrait {

}

/*
    这是一条测试评论.
*/

/*Channels::includeSystem('Permission');
if (Permission::hasPermission($projectid, 'project.metadata.add') === FALSE) {
    throw new PermissionException(_('You do not have permission to add metadata field'));
}*/

/*
    Comment goes here
*/
$two = (1 + 1);  // I'm not a comment closer!

class Foo
{

    /**
     * Comment here.
     *
     * @var array
     */
    var $bar = array();

}

class TabTest {
	public function bar() {
		/*
		 * Comment line 1.
		 * Comment line 2.
		*/

		if ($foo) {
			echo 'foo';
		}

		/* Comment line 1.
			Comment line 2.
		*/

		if ($foo) {
			echo 'foo';
		}
	}
}
