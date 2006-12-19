<?php
/**
 * A class to represent Comments of a doc comment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/CommentParser/SingleElement.php';

/**
 * A class to represent Comments of a doc comment.
 *
 * Comments are in the following format.
 * <code>
 * /** <--this is the start of the comment.
 *  * This is a short comment description
 *  *
 *  * This is a long comment description
 *  * <-- this is the end of the comment
 *  * @return something
 *  {@/}
 *  </code>
 *
 * Note that the sentence before two newlines is assumed
 * the short comment description.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_CommentParser_CommentElement extends PHP_CodeSniffer_CommentParser_SingleElement
{


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_CommentElement.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElemement $previousElement The element that
     *                                                                    appears before this
     *                                                                    element.
     * @param array                                      $tokens          The tokens that
     *                                                                    make up this element.
     */
    public function __construct($previousElement, $tokens)
    {
        parent::__construct($previousElement, $tokens, 'comment');

    }//end __construct()


    /**
     * Returns the short comment description.
     *
     * @return string
     * @see getLongComment()
     */
    public function getShortComment()
    {
        $pos = $this->_getShortCommentEndPos();
        if ($pos === -1) {
            return '';
        }
        return implode('', array_slice($this->tokens, 0, $pos + 1));

    }//end getShortComment()


    /**
     * Returns the last token position of the short comment description
     *
     * @return int The last token position of the short comment description
     * @see _getLongCommentStartPos()
     */
    private function _getShortCommentEndPos()
    {
        $found      = false;
        $whiteSpace = array(' ', "\t");

        foreach ($this->tokens as $pos => $token) {
            $token = str_replace($whiteSpace, '', $token);
            if ($token === "\n") {
                if ($found === false) {
                    // Include newlines before short description.
                    continue;
                } else if ($this->tokens[$pos + 1] === "\n") {
                    return ($pos - 1);
                }
            } else {
                $found = true;
            }
        }//end foreach

        return count($this->tokens) - 1;

    }//end _getShortCommentEndPos()


    /**
     * Returns the long comment description.
     *
     * @return string
     * @see getShortComment
     */
    public function getLongComment()
    {
        $start = $this->_getLongCommentStartPos();
        if ($start === -1) {
            return '';
        }

        return implode('', array_slice($this->tokens, $start));

    }//end getLongComment()


    /**
     * Returns the start position of the long comment description
     *
     * Returns -1 if there is no long comment.
     *
     * @return int The start position of the long comment description.
     * @see _getShortCommentEndPos()
     */
    private function _getLongCommentStartPos()
    {
        $pos = $this->_getShortCommentEndPos() + 1;
        if ($pos === count($this->tokens) - 1) {
            return -1;
        }

        $count = count($this->tokens);
        for ($i = $pos; $i < $count; $i++) {
            if (trim($this->tokens[$i]) !== '') {
                return $i;
            }
        }

        return -1;

    }//end _getLongCommentStartPos()


    /**
     * Returns the whitespace that exists between
     * the short and the long comment description.
     *
     * @return string
     */
    public function getWhiteSpaceBetween()
    {
        $endShort  = $this->_getShortCommentEndPos() + 1;
        $startLong = $this->_getLongCommentStartPos() - 1;
        if ($startLong === -1) {
            return '';
        }

        return implode('', array_slice($this->tokens, $endShort, $startLong - $endShort));

    }//end getWhiteSpaceBetween()


    /**
     * Returns the newline(s) that exist before the tags.
     *
     * @return int
     */
    public function getNewlineAfter()
    {
        $long = $this->getLongComment();
        if ($long !== '') {
            return strspn((strrev(rtrim($long, ' '))), "\n");
        } else {
            $endShort = $this->_getShortCommentEndPos() + 1;
            $after    = implode('', array_slice($this->tokens, $endShort));
            return strspn((trim($after, ' ')), "\n");
        }

    }//end getNewlineAfter()


    /**
     * Returns true if there is no comment.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (trim($this->getContent()) === '');

    }//end isEmpty()


}//end class

?>
