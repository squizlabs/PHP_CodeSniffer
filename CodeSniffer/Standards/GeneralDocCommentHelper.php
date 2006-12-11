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
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */

/**
 * A class for checking general doc comment standards.
 *
 * Checks:
 * <ul>
 *  <li>
 *     That the short description and long description have a newline between
 *     them.
 *   </li>
 *   <li>If link tags exists, they must be complete</li>
 *   <li>
 * </ul>
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */
class PHP_CodeSniffer_Standards_GeneralDocCommentHelper
{


    /**
     * You cannot instantiate this object.
     */
    private function __construct()
    {

    }//end __construct()


    /**
     * Validates the comment.
     *
     * @param PHP_CodeSniffer_CommentParser_AbstractParser $parser    The document parser.
     * @param PHP_CodeSniffer_File                         $phpcsFile The file where the
     *                                                                comment exists.
     * @param int                                          $stackPtr  The position where the
     *                                                                comment started.
     *
     * @return void
     */
    public static function validate(PHP_CodeSniffer_CommentParser_AbstractParser $parser, PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        self::checkComment($parser, $phpcsFile, $stackPtr);
        self::checkLinks($parser, $phpcsFile, $stackPtr);

    }//end validate()


    /**
     * Validates elements comments to ensure they begin with a capital letter
     * and end with a full stop.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the comment occured.
     * @param string               $comment   The comment to validate.
     * @param int                  $stackPtr  The position in the stack of the comment.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If the comment is empty.
     */
    public static function validateElementComment(PHP_CodeSniffer_File $phpcsFile, $comment, $stackPtr)
    {
        if (trim($comment) === '') {
            throw new PHP_CodeSniffer_Exception('comment must not be empty');
        }

        if ($comment{0} !== strtoupper($comment{0})) {
            $error = 'Comment must start with a capital letter.';
            $phpcsFile->addError($error, $stackPtr);
        }

        $validChars = array(
                       '.',
                       ')',
                      );

        if (in_array($comment{strlen($comment) - 1}, $validChars) === false) {
            $error = 'Comment must end with a full stop or a close parenthesis.';
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end validateElementComment()


    /**
     * Checks the comment within the doc comment.
     *
     * @param PHP_CodeSniffer_CommentParser_AbstractParser $parser    The document parser.
     * @param PHP_CodeSniffer_File                         $phpcsFile The file where the
     *                                                                commet exists.
     * @param int                                          $stackPtr  The position where the
     *                                                                comment started.
     *
     * @return void
     */
    protected static function checkComment(PHP_CodeSniffer_CommentParser_AbstractParser $parser, PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $comment = $parser->getComment();
        if ($comment === null || $comment->isEmpty() === true) {
            $error = 'Missing comment';
            $phpcsFile->addError($error, $stackPtr);
        } else {

            $headline    = trim($comment->getHeadlineComment());
            $bodyComment = trim($comment->getBodyComment());


            if ($bodyComment !== '') {
                if (substr_count($comment->getWhitespaceAfterHeadline(), "\n") !== 2) {
                    $error = 'Expected a blank newline after short comment description.';
                    $phpcsFile->addError($error, $comment->getLine() + $stackPtr);
                }
            }

            if ($headline !== '') {
                if ($headline{strlen($headline) - 1} !== '.') {
                    $error = 'Short Description must end with a period.';
                    $phpcsFile->addError($error, $comment->getLine() + $stackPtr);
                }

                if ($headline{0} !== strtoupper($headline{0})) {
                    $error = 'Short Description must start with a capital letter.';
                    $phpcsFile->addError($error, $comment->getLine() + $stackPtr);
                }

                if (substr_count($comment->getWhitespaceAfter(), "\n") !== 2) {
                    $error = 'Expected one blank line after comment';
                    $phpcsFile->addError($error, $comment->getLine() + $stackPtr);
                }
            }

        }//end else

    }//end checkComment()


    /**
     * Checks link tags to ensure that they are not empty.
     *
     * @param PHP_CodeSniffer_CommentParser_AbstractParser $parser    The document parser.
     * @param PHP_CodeSniffer_File                         $phpcsFile The file where the
     *                                                                comment exists.
     * @param int                                          $stackPtr  The position where the
     *                                                                comment started.
     *
     * @return void
     */
    protected static function checkLinks(PHP_CodeSniffer_CommentParser_AbstractParser $parser, PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $links = $parser->getLinks();

        if (empty($links) !== false) {
            foreach ($links as $link) {
                if ($link->getContent() === '') {
                    $error = 'Link tags cannot be empty';
                    $phpcsFile->addError($error, $link->getLine() + $stackPtr);
                }
            }
        }

    }//end checkLinks()


}//end class

?>
