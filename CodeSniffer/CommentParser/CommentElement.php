<?php
/**
 * +------------------------------------------------------------------------+
 * | BSD Licence                                                            |
 * +------------------------------------------------------------------------+
 * | This software is available to you under the BSD license,               |
 * | available in the LICENSE file accompanying this software.              |
 * | You may obtain a copy of the License at                                |
 * |                                                                        |
 * | http://matrix.squiz.net/developer/tools/php_cs/licence                 |
 * |                                                                        |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS    |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT      |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR  |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT   |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,  |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT       |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,  |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY  |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT    |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   |
 * +------------------------------------------------------------------------+
 * | Copyright (c), 2006 Squiz Pty Ltd (ABN 77 084 670 600).                |
 * | All rights reserved.                                                   |
 * +------------------------------------------------------------------------+
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer/CommentParser/SingleElement.php';

/**
 * A class to represent Comments of a doc comment.
 *
 * Comments are in the following format.
 * <code>
 * /** <--this is the start of the comment.
 *  * This is a headline comment.
 *  *
 *  * this is a body comment.
 *  * <-- this is the end of the comment
 *  * @return something
 *  {@/}
 *  </code>
 *
 * Note that if there is no period to end the headline, the sentence before two
 * newlines is assumed the headline comment.
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
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
     * Returns the headline comment.
     *
     * @return string
     * @see getBodyComment()
     */
    public function getHeadlineComment()
    {
        $pos = $this->_getEndHeadlinePos();
        return implode('', array_slice($this->tokens, 0, $pos + 1));

    }//end getHeadlineComment()


    /**
     * Returns the last token position of the headline.
     *
     * @return int The last token position of the headline.
     * @see _getStartBodyPos()
     */
    private function _getEndHeadlinePos()
    {
        foreach ($this->tokens as $pos => $token) {
            if ($token{strlen($token) - 1} === '.') {
                if ($this->tokens[$pos + 1]{0} === ' ' || $this->tokens[$pos + 1] === "\n") {
                    return $pos;
                }
            }

            if ($token === "\n") {
                if ($this->tokens[$pos + 1] === "\n") {
                    return ($pos - 1);
                }
            }
        }

        return count($this->tokens) - 1;

    }//end _getEndHeadlinePos()


    /**
     * Returns the start position of the body content in $this->tokens.
     *
     * Returns -1 if there is no body comment.
     *
     * @return int The start position of the body comment.
     * @see _getEndHeadlinePos()
     */
    private function _getStartBodyPos()
    {
        $headlinePos = $this->_getEndHeadLinePos() + 1;
        if ($headlinePos === count($this->tokens) - 1) {
            return -1;
        }

        $count = count($this->tokens);
        for ($i = $headlinePos; $i < $count; $i++) {
            if (trim($this->tokens[$i]) !== '') {
                return $i;
            }
        }

        return -1;

    }//end _getStartBodyPos()


    /**
     * Returns the whitespace that exists between the headline and the comment.
     *
     * @return string
     */
    public function getWhitespaceAfterHeadline()
    {
        $endHeadline = $this->_getEndHeadLinePos() + 1;
        $startBody   = $this->_getStartBodyPos() - 1;
        if ($startBody === -1) {
            return '';
        }

        return implode('', array_slice($this->tokens, $endHeadline, $startBody - $endHeadline));

    }//end getWhitespaceAfterHeadline()


    /**
     * Returns the body comment.
     *
     * @return string
     * @see getHeadlineComment()
     */
    public function getBodyComment()
    {
        $start = $this->_getStartBodyPos();
        return implode('', array_slice($this->tokens, $start));

    }//end getBodyComment()


    /**
     * Returns true if there is no comment.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return trim($this->getContent() === '');

    }//end isEmpty()


}//end class

?>
