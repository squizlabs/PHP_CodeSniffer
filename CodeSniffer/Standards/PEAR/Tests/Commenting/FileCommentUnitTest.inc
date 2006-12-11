<?php

/**
* Short description for fileasdasd.
*
* asdasd
* long description for file (if any)
* asdasdadada
* more desss
*
* PHP versio
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category   _wrong_category
* @package    PHP_CodeSniffer
* @author     Original Author <author@example.com>
* @author       Rayn Ong rong@squiz.net
* @author
* @copyright  1997~1994 The PHP Group
* @license    http://www.php.net/license/3_0.txt
* @version    CVS:
* @link       sdfsdf
* @see        CVS: $Id:
* @see        NetOther, Net_Sample::Net_Sample()
* @deprecated asd
* @since      Class available since Release 1.2.0
*/

require_once 'PHP/CodeSniffer.php';
require_once 'PHP/CodeSniffer/Tokens.php';
require_once 'PHP/CodeSniffer/Exception.php';

/**
 * A PHP_CodeSniffer_File object represents a PHP source file and the tokens
 * associated with it.
 *
 * @category CategoryName
 * @package  PHP_CodeSniffer
 * @author   Squiz Pty Ltd <squiz@squiz.net>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  CVS: $Id$
 * @link     sdfsdf
 */
class PHP_CodeSniffer_File
{

    /**
     * Constructs a PHP_CodeSniffer_File.
     *
     * long desc here
     *
     * @param int $stackPtr The position in the stack of the token that opened
     *                      the scope (eg. an IF token or FOR token).
     * @param int $depth    How many scope levels down we are.
     *
     * @return int The position in the stack that closed the scope.
     */
    private function _recurseScopeMap($stackPtr, $depth=1)
    {
        $this->_recurseScopeMap($stackPtr, $depth);
        return $stackPtr;

    }//end _recurseScopeMap()

}//end class

?>
