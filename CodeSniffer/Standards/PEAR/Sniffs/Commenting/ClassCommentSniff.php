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
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer/Sniff.php';
require_once 'PHP/CodeSniffer/CommentParser/ClassCommentParser.php';
require_once 'PHP/CodeSniffer/Standards/PEAR/Sniffs/Commenting/FileCommentSniff.php';

/**
 * Parses and verifies the doc comments for classes.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>Short description must start with a capital letter and end with a period.</li>
 *  <li>There must be one blank newline after the short description.</li>
 *  <li>A PHP version is specified.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */

class PEAR_Sniffs_Commenting_ClassCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_phpcsFile = $phpcsFile;

        // Extract the class comment docblock.
        $commentEnd   = $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr - 1);
        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT, $commentEnd - 1, null, true) + 1;
        $tokens       = $this->_phpcsFile->getTokens();

        if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT) {
            $this->_phpcsFile->addError('Missing class doc comment', $stackPtr + 1);
            return;
        }
        $comment = $this->_phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);

        // Parse the class comment.docblock.
        try {
            $this->_fp = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment);
            $this->_fp->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = $e->getLineWithinComment() + $commentStart;
            $this->_phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        // Validate the generic comment.
        PHP_CodeSniffer_Standards_GeneralDocCommentHelper::validate($this->_fp, $this->_phpcsFile, $commentStart);

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);

    }//end process()


}//end class

?>
