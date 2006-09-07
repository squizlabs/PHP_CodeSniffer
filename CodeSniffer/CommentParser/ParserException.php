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
 * An exception to be thrown when a DocCommentParser finds an anomilty in a
 * doc comment.
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
 */
class PHP_CodeSniffer_CommentParser_ParserException extends Exception
{

    /**
     * The line where the exception occured, in relation to the doc comment.
     *
     * @var int
     */
    private $_line = 0;


    /**
     * Constructs a DocCommentParserException.
     *
     * @param string $message The message of the exception.
     * @param int    $line    The position in comment where the error occured.
     *                        A position of 0 indicates that the error occured
     *                        at the opening line of the doc comment.
     */
    public function __construct($message, $line)
    {
        parent::__construct($message);
        $this->_line = $line;

    }//end __construct()


    /**
     * Returns the line number within the comment where the exception occured.
     *
     * @return int
     */
    public function getLineWithinComment()
    {
        return $this->_line;

    }//end getLineWithinComment()


}//end class

?>
