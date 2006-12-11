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
 * A class to represent single element doc tags.
 *
 * A single element doc tag contains only one value after the tag itself. An
 * example would be the \@package tag.
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
 */
class PHP_CodeSniffer_CommentParser_SingleElement extends PHP_CodeSniffer_CommentParser_AbstractDocElement
{

    /**
     * The content that exists after the tag.
     *
     * @var string
     * @see getContent()
     */
    protected $content = '';

    /**
     * The whitespace that exists before the content.
     *
     * @var string
     * @see getWhitespaceBeforeContent()
     */
    protected $contentWhitespace = '';


    /**
     * Constructs a SingleElement doc tag.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElement $previousElement The element before this one.
     * @param array                                    $tokens          The tokens that comprise this element.
     * @param string                                   $tag             The tag that this element represents.
     */
    public function __construct($previousElement, $tokens, $tag)
    {
        parent::__construct($previousElement, $tokens, $tag);

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
                'content',
               );

    }//end getSubElements()


    /**
     * Processes the sub element with the specified name.
     *
     * @param string $name             The name of the sub element to process.
     * @param string $content          The content of this sub element.
     * @param string $whitespaceBefore The whitespace that exists before the
     *                                 sub element.
     *
     * @return void
     * @see getSubElements()
     */
    protected function processSubElement($name, $content, $whitespaceBefore)
    {
        $this->content = $content;
        $this->contentWhitespace = $whitespaceBefore;

    }//end processSubElement()


    /**
     * Returns the content of this tag.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;

    }//end getContent()


    /**
     * Returns the witespace before the content of this tag.
     *
     * @return string
     */
    public function getWhitespaceBeforeContent()
    {
        return $this->contentWhitespace;

    }//end getWhitespaceBeforeContent()


    /**
     * Processes a content check for single doc element.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $commentStart The line number where the error occurs.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $commentStart)
    {
        if ($this->content === '') {
            $errorPos = $commentStart + $this->getLine();
            $error = "Content missing for $this->tag tag";
            $phpcsFile->addError($error, $errorPos);
        }

    }//end process()


}//end class

?>
