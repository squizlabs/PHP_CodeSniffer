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

require_once 'PHP/CodeSniffer/CommentParser/FunctionCommentParser.php';
require_once 'PHP/CodeSniffer/Standards/GeneralDocCommentHelper.php';
require_once 'PHP/CodeSniffer/Standards/AbstractScopeSniff.php';

/**
 * Parses and verifies the doc comments for functions.
 *
 * Verifies that :
 * <ul>
 *  <li>A comment exists</li>
 *  <li>Parameter names represent those in the method.</li>
 *  <li>Parameter comments are in the correct order</li>
 *  <li>Parameter comments are complete</li>
 *  <li>A space is present before the first and after the last parameter</li>
 *  <li>A return type exists</li>
 *  <li>If a body comment exists, it must be one blank newline from the headline comment.</li>
 *  <li>Any throw tag must have a comment.</li>
 * </ul>
 *
 * @package  PHP_CodeSniffer
 * @category Squiz_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class PEAR_Sniffs_Commenting_FunctionCommentSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{

    /**
     * The name of the method that we are currently processing.
     *
     * @var string
     */
    private $_methodName = '';

    /**
     * The position in the stack where the fucntion token was found.
     *
     * @var int
     */
    private $_functionToken = null;

    /**
     * The function comment parser for the current method.
     *
     * @var PHP_CodeSniffer_Comment_Parser_FunctionCommentParser
     */
    private $_fp = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    private $_phpcsFile = null;


    /**
     * Constructs a Squiz_Sniffs_Commenting_FunctionCommentSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_FUNCTION));

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param array                $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $this->_phpcsFile = $phpcsFile;

        $tokens = $this->_phpcsFile->getTokens();

        $find = array(
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentEnd = $phpcsFile->findPrevious($find, $stackPtr - 1);

        $error = 'Missing function doc comment';
        if ($commentEnd === false) {
            $this->_phpcsFile->addError($error, $stackPtr);
            return;
        }

        // If the token that we found was a class or a function, then this
        // function has no doc comment.
        $code = $tokens[$commentEnd]['code'];

        if ($code !== T_DOC_COMMENT) {
            $this->_phpcsFile->addError($error, $stackPtr);
            return;
        }

        $this->_functionToken = $stackPtr;

        // Find the first doc comment.
        $commentStart = $this->_phpcsFile->findPrevious(T_DOC_COMMENT, $commentEnd - 1, null, true) + 1;
        $comment      = $this->_phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
        $this->_methodName = $this->_phpcsFile->getDeclarationName($stackPtr);

        try {
            $this->_fp = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment);
            $this->_fp->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = $e->getLineWithinComment() + $commentStart;
            $this->_phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        $this->_processParams($commentStart);
        $this->_processReturn($commentStart, $commentEnd);
        $this->_processThrows($commentStart);

        // Validate all the generic comment.
        PHP_CodeSniffer_Standards_GeneralDocCommentHelper::validate($this->_fp, $phpcsFile, $commentStart);

    }//end processTokenWithinScope()


    /**
     * Process any throw tags that this function comment has.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    private function _processThrows($commentStart)
    {
        if (count($this->_fp->getThrows()) === 0) {
            return;
        }

        foreach ($this->_fp->getThrows() as $throw) {

            $comment  = $throw->getComment();
            $errorPos = ($commentStart + $throw->getLine());

            if ($comment !== '') {
                PHP_CodeSniffer_Standards_GeneralDocCommentHelper::validateElementComment($this->_phpcsFile, $comment, $errorPos);
            } else {
                $error = 'Throw tag must contain a comment.';
                $this->_phpcsFile->addError($error, $errorPos);
            }
        }

    }//end _processThrows()


    /**
     * Process the return comment of this function comment.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    private function _processReturn($commentStart, $commentEnd)
    {
        if ($this->_methodName !== '__construct') {
            // Report missing return tag.
            if ($this->_fp->getReturn() === null) {
                $error = 'Missing return tag';
                $this->_phpcsFile->addError($error, $commentEnd);
            } else if (trim($this->_fp->getReturn()->getRawContent()) === '') {
                $error    = 'Return tag empty';
                $errorPos = ($commentStart + $this->_fp->getReturn()->getLine());
                $this->_phpcsFile->addError($error, $errorPos);
            } else {
                $comment = $this->_fp->getReturn()->getComment();

                if ($comment !== '') {
                    $errorPos = ($commentStart + $this->_fp->getReturn()->getLine());
                    PHP_CodeSniffer_Standards_GeneralDocCommentHelper::validateElementComment($this->_phpcsFile, $comment, $errorPos);
                }
            }
        }

    }//end _processReturn()


    /**
     * Process the function parameter comments.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     *
     * @return void
     */
    private function _processParams($commentStart)
    {
        $realParams = $this->_phpcsFile->getMethodParameters($this->_functionToken);

        $params      = $this->_fp->getParams();
        $foundParams = array();

        if (empty($params) === false) {

            if (substr_count($params[count($params) - 1]->getWhitespaceAfter(), "\n") !== 2) {
                $error    = 'Last parameter comment requires a blank newline after it';
                $errorPos = $params[count($params) - 1]->getLine() + $commentStart;
                $this->_phpcsFile->addError($error, $errorPos);
            }

            // Parameters must appear immediately after the comment.
            if ($params[0]->getOrder() !== 2) {
                $error    = 'Parameters must appear immediately after the comment';
                $errorPos = $params[0]->getLine() + $commentStart;
                $this->_phpcsFile->addError($error, $errorPos);
            }

            $previousParam      = null;
            $spaceBeforeVar     = 10000;
            $spaceBeforeComment = 10000;
            $longestType        = 0;
            $longestVar         = 0;

            foreach ($params as $param) {

                $paramComment = trim($param->getComment());
                $errorPos     = $param->getLine() + $commentStart;

                // Make sure that there is only one space before the var type.
                if ($param->getWhitespaceBeforeType() !== ' ') {
                    $error = 'Expected one space before variable type';
                    $this->_phpcsFile->addError($error, $errorPos);
                }

                $spaceCount = substr_count($param->getWhitespaceBeforeVarName(), ' ');
                if ($spaceCount < $spaceBeforeVar) {
                    $spaceBeforeVar = $spaceCount;
                    $longestType    = $errorPos;
                }

                $spaceCount = substr_count($param->getWhitespaceBeforeComment(), ' ');

                if ($spaceCount < $spaceBeforeComment && $paramComment !== '') {
                    $spaceBeforeComment = $spaceCount;
                    $longestVar         = $errorPos;
                }

                // Make sure they are in the correct order, and have the correct name.
                $pos = $param->getPosition();

                $paramName = ($param->getVarName() !== '') ? $param->getVarName() : '[ UNKNOWN ]';

                if ($previousParam !== null) {
                    $previousName = ($previousParam->getVarName() !== '') ? $previousParam->getVarName() : 'UNKNOWN';

                    // Check to see if the parameters align properly.
                    if ($param->alignsWith($previousParam) === false) {
                        $error = 'Parameters '.$previousName.' ('.($pos - 1).') and '.$paramName.' ('.$pos.') do not align';
                        $this->_phpcsFile->addError($error, $errorPos);
                    }
                }

                // Make sure the names of the parameter comment matches the
                // actual parameter.
                if (isset($realParams[$pos - 1]) === true) {
                    $foundParams[] = $realParams[$pos - 1]['name'];
                    if ($realParams[$pos - 1]['name'] !== $param->getVarName()) {
                        $error  = 'Doc comment var "'.$paramName;
                        $error .= '" does not match actual variable name "'.$realParams[$pos - 1]['name'];
                        $error .= '" at position '.$pos;

                        $this->_phpcsFile->addError($error, $errorPos);
                    }
                } else {
                    // We must have an extra parameter comment.
                    $error = 'Superfluous doc comment at position '.$pos;
                    $this->_phpcsFile->addError($error, $errorPos);
                }

                if ($param->getVarName() === '') {
                    $error = 'Missing parameter name at position '.$pos;
                     $this->_phpcsFile->addError($error, $errorPos);
                }

                if ($param->getType() === '') {
                    $error = 'Missing type at position '.$pos;
                    $this->_phpcsFile->addError($error, $errorPos);
                }

                if ($paramComment === '') {
                    $error = 'Missing comment for param "'.$paramName.'" at position '.$pos;
                    $this->_phpcsFile->addError($error, $errorPos);
                } else {
                    // Check to ensure that the parameter comment is capilized and
                    // has a full stop or other valid punctuation mark.
                    PHP_CodeSniffer_Standards_GeneralDocCommentHelper::validateElementComment($this->_phpcsFile, $paramComment, $errorPos);
                }
                $previousParam = $param;

            }//end foreach

            if ($spaceBeforeVar !== 1) {
                $error = 'Expected 1 space after the longest type';
                $this->_phpcsFile->addError($error, $longestType);
            }

            if ($spaceBeforeComment !== 1) {
                $error = 'Expected 1 space after the longest variable name';
                $this->_phpcsFile->addError($error, $longestVar);
            }

        }//end if

        $realNames = array();
        foreach ($realParams as $realParam) {
            $realNames[] = $realParam['name'];
        }

        // Report and missing comments.
        $diff = array_diff($realNames, $foundParams);
        foreach ($diff as $neededParam) {
            if (count($params) !== 0) {
                $errorPos = $params[count($params) - 1]->getLine() + $commentStart;
            } else {
                $errorPos = $commentStart;
            }

            $error = 'Doc comment for "'.$neededParam.'" missing';
            $this->_phpcsFile->addError($error, $errorPos);
        }

    }//end _processParams()


}//end class

?>
