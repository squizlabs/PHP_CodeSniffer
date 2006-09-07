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

require_once 'PHP/CodeSniffer/CommentParser/AbstractDocElement.php';

/**
 * A class to represent param tags within a function comment.
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
 */
class PHP_CodeSniffer_CommentParser_ParameterElement extends PHP_CodeSniffer_CommentParser_AbstractDocElement
{

    /**
     * The variable name of this parameter name, including the $ sign.
     *
     * @var string
     */
    private $_varName = '';

    /**
     * The comment of this parameter tag.
     *
     * @var string
     */
    private $_comment = '';

    /**
     * The variable type of this parameter tag.
     *
     * @var string
     */
    private $_type = '';

    /**
     * The whitespace that exists before the variable name.
     *
     * @var string
     */
    private $_varNameWhitespace = '';

    /**
     * The whitespace that exists before the comment.
     *
     * @var string
     */
    private $_commentWhitespace = null;

    /**
     * The whitespace that exists before the variable type.
     *
     * @var string
     */
    private $_typeWhitespace = '';


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_ParameterElement.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElement $previousElement The element previous to this one.
     * @param array                                    $tokens          The tokens that make up this element.
     */
    public function __construct($previousElement, $tokens)
    {
        parent::__construct($previousElement, $tokens, 'param');

    }//end __construct()


    /**
     * Returns the element names that this tag is comprised of, in the order
     * that they appear in the tag.
     *
     * @return array(string)
     * @see processSubElement()
     */
    protected function getSubElements()
    {
        return array(
                'type',
                'varName',
                'comment',
               );

    }//end getSubElements()


    /**
     * Processes the sub element with the specified name.
     *
     * @param string $name             The name of the sub element to process.
     * @param string $content          The content of this sub element.
     * @param string $beforeWhitespace The whitespace that exists before the
     *                                 sub element.
     *
     * @return void
     * @see getSubElements()
     */
    protected function processSubElement($name, $content, $beforeWhitespace)
    {
        $element           = '_'.$name;
        $whitespace        = $element.'Whitespace';
        $this->$element    = $content;
        $this->$whitespace = $beforeWhitespace;

    }//end processSubElement()


    /**
     * Returns the variable name that this parameter tag represents.
     *
     * @return string
     */
    public function getVarName()
    {
        return $this->_varName;

    }//end getVarName()


    /**
     * Returns the variable type that this string represents.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;

    }//end getType()


    /**
     * Returns the comment of this comment for this parameter.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_comment;

    }//end getComment()


    /**
     * Returns the whitespace before the variable type.
     *
     * @return stirng
     * @see getWhiteSpaceBeforeVarName()
     * @see getWhiteSpaceBeforeComment()
     */
    public function getWhiteSpaceBeforeType()
    {
        return $this->_typeWhitespace;

    }//end getWhiteSpaceBeforeType()


    /**
     * Returns the whitespace before the variable name.
     *
     * @return string
     * @see getWhiteSpaceBeforeComment()
     * @see getWhiteSpaceBeforeType()
     */
    public function getWhiteSpaceBeforeVarName()
    {
        return $this->_varNameWhitespace;

    }//end getWhiteSpaceBeforeVarName()


    /**
     * Returns the whitespace before the comment.
     *
     * @return string
     * @see getWhiteSpaceBeforeVarName()
     * @see getWhiteSpaceBeforeType()
     */
    public function getWhiteSpaceBeforeComment()
    {
        return $this->_commentWhitespace;

    }//end getWhiteSpaceBeforeComment()


    /**
     * Returns the postition of this parameter are it appears in the comment.
     *
     * This method differs from getOrder as it is only relative to method
     * parameters.
     *
     * @return int
     */
    public function getPosition()
    {
        if (($this->getPreviousElement() instanceof PHP_CodeSniffer_CommentParser_ParameterElement) === false) {
            return 1;
        } else {
            return $this->getPreviousElement()->getPosition() + 1;
        }

    }//end getPosition()


    /**
     * Returns true if this parameter aligns with the other parameter.
     *
     * @param PHP_CodeSniffer_CommentParser_ParameterElement $other The other parameter to check alignment
     *                                                              with.
     *
     * @return boolean
     */
    public function alignsWith(PHP_CodeSniffer_CommentParser_ParameterElement $other)
    {
        $otherLength  = strlen($other->_typeWhitespace) + strlen($other->_type);
        $otherLength += strlen($other->_varNameWhitespace) + strlen($other->_varName);
        $otherLength += strlen($other->_commentWhitespace);

        $myLength  = strlen($this->_typeWhitespace) + strlen($this->_type);
        $myLength += strlen($this->_varNameWhitespace) + strlen($this->_varName);
        $myLength += strlen($this->_commentWhitespace);

        return ($myLength === $otherLength);

    }//end alignsWith()


}//end class


?>
