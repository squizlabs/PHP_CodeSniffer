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

require_once 'PHP/CodeSniffer/CommentParser/AbstractParser.php';
require_once 'PHP/CodeSniffer/CommentParser/ParameterElement.php';
require_once 'PHP/CodeSniffer/CommentParser/PairElement.php';
require_once 'PHP/CodeSniffer/CommentParser/SingleElement.php';

/**
 * Parses function doc comments.
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
 */
class PHP_CodeSniffer_CommentParser_FunctionCommentParser extends PHP_CodeSniffer_CommentParser_AbstractParser
{

    /**
     * The parameter elements within this function comment.
     *
     * @var array(PHP_CodeSniffer_CommentParser_ParameterElement)
     */
    private $_params = array();

    /**
     * The return element in this function comment.
     *
     * @var PHP_CodeSniffer_CommentParser_PairElement.
     */
    private $_return = null;


    /**
     * The throws element list for this function comment.
     *
     * @var array(PHP_CodeSniffer_CommentParser_PairElement)
     */
    private $_throws = array();


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_FunctionCommentParser.
     *
     * @param string $comment The comment to parse.
     */
    public function __construct($comment)
    {
        parent::__construct($comment);

    }//end __construct()


    /**
     * Parses parameter elements.
     *
     * @param array(string) $tokens The tokens that conmpise this sub element.
     *
     * @return PHP_CodeSniffer_CommentParser_ParameterElement
     */
    protected function parseParam($tokens)
    {
        $param = new PHP_CodeSniffer_CommentParser_ParameterElement($this->previousElement, $tokens);
        $this->_params[] = $param;

        return $param;

    }//end parseParam()


    /**
     * Parses return elements.
     *
     * @param array(string) $tokens The tokens that conmpise this sub element.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    protected function parseReturn($tokens)
    {
        $return = new PHP_CodeSniffer_CommentParser_PairElement($this->previousElement, $tokens, 'return');
        $this->_return = $return;

        return $return;

    }//end parseReturn()


    /**
     * Parses throws elements.
     *
     * @param array(string) $tokens The tokens that conmpise this sub element.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    protected function parseThrows($tokens)
    {
        $throws = new PHP_CodeSniffer_CommentParser_PairElement($this->previousElement, $tokens, 'throws');
        $this->_throws[] = $throws;

        return $throws;

    }//end parseThrows()


    /**
     * Returns the parameter elements that this function comment contains.
     *
     * Returns an empty array if no parameter elements are contained within
     * this function comment.
     *
     * @return array(PHP_CodeSniffer_CommentParser_ParameterElement)
     */
    public function getParams()
    {
        return $this->_params;

    }//end getParams()


    /**
     * Returns the return element in this fucntion comment.
     *
     * Returns null if no return element exists in the comment.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    public function getReturn()
    {
        return $this->_return;

    }//end getReturn()


    /**
     * Returns the throws elements in this fucntion comment.
     *
     * Returns empty array if no throws elements in the comment.
     *
     * @return array(PHP_CodeSniffer_CommentParser_PairElement)
     */
    public function getThrows()
    {
        return $this->_throws;

    }//end getThrows()


    /**
     * Returns the allowed tags that can exist in a function comment.
     *
     * @return array(string => boolean)
     */
    protected function getAllowedTags()
    {
        return array(
                'param'  => false,
                'return' => true,
                'throws' => false,
               );

    }//end getAllowedTags()


}//end class

?>