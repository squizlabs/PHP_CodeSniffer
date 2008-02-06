<?php
define('OK', 1);

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
 * Sample class comment
 *
 *
 * Long description with extra blank line before and after
 *
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Extra_Description_Newlines
{

}//end class


/**
 * Sample class comment
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Missing_Newlines_Before_Tags
{

}//end class


/**
 * Simple class comment
 *
 * @category   _wrong_category
 * @package    PHP_CodeSniffer
 * @package    ADDITIONAL PACKAGE TAG
 * @subpackage SUBPACKAGE TAG
 * @author     Original Author <author@example.com>
 * @author       Rayn Ong rong@squiz.net
 * @author
 * @copyright  1997~1994 The PHP Group
 * @license    http://www.php.net/license/3_0.txt
 * @version    INVALID VERSION CONTENT
 * @see
 * @see
 * @link       sdfsdf
 * @see        Net_Sample::Net_Sample()
 * @see        Net_Other
 * @deprecated asd
 * @unknown    Unknown tag
 * @since      Class available since Release 1.2.0
 */
class Checking_Tags
{
    class Sub_Class {

    }//end class


}//end class


/**
 *
 *
 */
class Empty_Class_Doc
{

}//end class


/**
 *
 *
 */
interface Empty_Interface_Doc
{

}//end interface
?>
