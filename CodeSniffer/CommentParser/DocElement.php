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

/**
 * A DocElement represents a logical element within a Doc Comment.
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
 */
interface PHP_CodeSniffer_CommentParser_DocElement
{


    /**
     * Returns the name of the tag that this element represents, omitting the
     * @ symbol.
     *
     * @return string
     */
    public function getTag();


    /**
     * Returns the whitespace that exists before this element.
     *
     * @return string
     * @see getWhitespaceAfter()
     */
    public function getWhitespaceBefore();


    /**
     * Returns the whitespace that exists after this element.
     *
     * @return string
     * @see getWhitespaceBefore()
     */
    public function getWhitespaceAfter();


    /**
     * Returns the order that this element appears in the doc comment.
     *
     * The first element in the comment should have an order of 1.
     *
     * @return int
     */
    public function getOrder();


    /**
     * Returns the element that appears before this element.
     *
     * @return PHP_CodeSniffer_CommentParser_DocElement
     * @see getNextElement()
     */
    public function getPreviousElement();


    /**
     * Returns the element that appears after this element.
     *
     * @return PHP_CodeSniffer_CommentParser_DocElement
     * @see getPreviousElement()
     */
    public function getNextElement();


    /**
     * Returns the line that this element started on.
     *
     * @return int
     */
    public function getLine();


    /**
     * Returns the raw content of this element, ommiting the tag.
     *
     * @return string
     */
    public function getRawContent();


}//end interface

?>
