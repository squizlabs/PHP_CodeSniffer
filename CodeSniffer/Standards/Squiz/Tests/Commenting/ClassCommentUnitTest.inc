<?php
/** File doc comment */

class No_Comment
{

}//end class


//
// Sample class comment
//
//
//
class Invalid_Comment_Style1
{

}//end class


/**
 *
 *
 * Sample class comment.
 *
 *
 * Long description with extra blank line before and after
 *
 *
 * @version   Release: 1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Extra_Description_Newlines
{

}//end class


/**
 * Sample class comment...
 * @since a.b.c
 */
class Missing_Newlines_Before_Tags
{

}//end class


/**
 * Simple class comment
 *
 * @deprecated asd
 * @since      Class available since Release 1.2.0
 */
class Checking_Tags
{
    class Sub_Class {

    }//end class


}//end class


/**
 * Simple class comment
 * that spans multiple line
 * and not end with a full stop
 *
 * @since      Wrong indentation
 * @hello      Hello tag
 * @package    phpcs (only since tag is allowed)
 */
class Unknown_Hello_Tag
{

}//end class


/**
 * Three since tags.
 *
 * @since 4.0.1
 * @since 4.0.1-rc1
 * @since 4.0.1-rc2
 */
class Three_Since_Tags
{

}//end class


/**
 * Since tag no content.
 *
 * @since
 *
 *
 */
class Empty_Since
{

}//end class


/**
 *
 *
 *
 */
class Empty_Class_Doc
{

}//end class


/**
 * @since 4.0.0
 */
class Missing_Short_Desc
{

}//end class


/**
 * sample class comment.
 *
 * - long description with extra blank line before and after
 *
 * @since 4.0.0
 */
class Incorect_Case
{

}//end class


/**
 * 0sample class comment.
 *
 * long description with extra blank line before and after
 *
 * @since 4.0.0
 */
class Incorect_Case2
{

}//end class
?>
