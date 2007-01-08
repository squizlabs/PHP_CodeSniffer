<?php
/**
 * Parses and verifies the doc comments for functions.
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

require_once 'PHP/CodeSniffer/Sniff.php';
require_once 'PHP/CodeSniffer/CommentParser/FunctionCommentParser.php';

/**
 * Parses and verifies the doc comments for functions.
 *
 * Verifies that :
 * <ul>
 *  <li>A comment exists</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Parameter names represent those in the method.</li>
 *  <li>Parameter comments are in the correct order</li>
 *  <li>Parameter comments are complete</li>
 *  <li>A space is present before the first and after the last parameter</li>
 *  <li>A return type exists</li>
 *  <li>If a body comment exists, it must be one blank newline from the headline comment.</li>
 *  <li>Any throw tag must have a comment.</li>
 * </ul>
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
class PEAR_Sniffs_Commenting_FunctionCommentSniff implements PHP_CodeSniffer_Sniff
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
     * The position in the stack where the class token was found.
     *
     * @var int
     */
    private $_classToken = null;

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
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_phpcsFile = $phpcsFile;

        $tokens = $this->_phpcsFile->getTokens();

        $find = array(
                 T_COMMENT,
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentEnd = $phpcsFile->findPrevious($find, $stackPtr - 1);

        if ($commentEnd === false) {
            return;
        }

        // If the token that we found was a class or a function, then this
        // function has no doc comment.
        $code = $tokens[$commentEnd]['code'];

        if ($code === T_COMMENT) {
            $error = 'Consider using "/**" style comment for function comment';
            $phpcsFile->addError($error, $stackPtr);
            return;
        } else if ($code !== T_DOC_COMMENT) {
            $error = 'Missing function doc comment';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        $this->_functionToken = $stackPtr;
        $classToken = $phpcsFile->findPrevious(array(T_CLASS, T_INTERFACE), ($stackPtr - 1));
        if ($classToken !== false) {
            $this->_classToken = $classToken;
        }

        // Find the first doc comment.
        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT, $commentEnd - 1, null, true) + 1;
        $comment      = $phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
        $this->_methodName = $phpcsFile->getDeclarationName($stackPtr);

        try {
            $this->_fp = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment);
            $this->_fp->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = $e->getLineWithinComment() + $commentStart;
            $phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        $this->_processParams($commentStart);
        $this->_processReturn($commentStart, $commentEnd);
        $this->_processThrows($commentStart);

        // No extra newline before short description.
        $comment      = $this->_fp->getComment();
        $short        = $comment->getShortComment();
        $newlineCount = 0;
        $newlineSpan  = strspn($short, "\n");
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before function comment short description";
            $phpcsFile->addError($error, $commentStart + 1);
        }
        $newlineCount = substr_count($short, "\n") + 1;

        // Exactly one blank line between short and long description.
        $between        = $comment->getWhiteSpaceBetween();
        $long           = $comment->getLongComment();
        $newlineBetween = substr_count($between, "\n");
        if ($newlineBetween !== 2 && $long !== '') {
            $error = 'There must be exactly one blank line between descriptions in function comment';
            $phpcsFile->addError($error, $commentStart + $newlineCount + 1);
        }
        $newlineCount += $newlineBetween;

        // Exactly one blank line before params.
        $newlineSpan = $comment->getNewlineAfter();
        $parameters  = $this->_fp->getParams();
        if ($newlineSpan !== 2 && (empty($parameters) === false || $this->_fp->getReturn() !== null)) {
            $error = 'There must be exactly one blank line before the parameters in function comment';
            if ($long !== '') {
                $newlineCount += (substr_count($long, "\n") - $newlineSpan + 1);
            }
            $phpcsFile->addError($error, $commentStart + $newlineCount);
        }

        // Check for unknown/deprecated tags.
        $unknownTags = $this->_fp->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $error = ucfirst($errorTag['tag']).' tag is not allowed in function comment';
            $phpcsFile->addWarning($error, $commentStart + $errorTag['line']);
        }

    }//end process()


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

            if ($comment === '') {
                $error = 'Throw tag must contain a comment';
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
        // Skip constructor and destructor.
        $className = '';
        if ($this->_classToken !== null) {
            $className = $this->_phpcsFile->getDeclarationName($this->_classToken);
            $className = strtolower(ltrim($className, '_'));
        }
        $methodName      = strtolower(ltrim($this->_methodName, '_'));
        $isSpecialMethod = ($this->_methodName === '__construct' || $this->_methodName === '__destruct');

        if (!$isSpecialMethod && $methodName !== $className) {
            // Report missing return tag.
            if ($this->_fp->getReturn() === null) {
                $error = 'Missing return tag in function comment';
                $this->_phpcsFile->addError($error, $commentEnd);
            } else if (trim($this->_fp->getReturn()->getRawContent()) === '') {
                $error    = 'Return tag is empty in function comment';
                $errorPos = ($commentStart + $this->_fp->getReturn()->getLine());
                $this->_phpcsFile->addError($error, $errorPos);
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
                    $error = 'Expected 1 space before variable type';
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
                    $realName      = $realParams[$pos - 1]['name'];
                    $foundParams[] = $realName;
                    // Append ampersand to name if passing by reference
                    if ($realParams[$pos - 1]['pass_by_reference'] === true) {
                        $realName = '&'.$realName;
                    }
                    if ($realName !== $param->getVarName()) {
                        $error  = 'Doc comment var "'.$paramName;
                        $error .= '" does not match actual variable name "'.$realName;
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
                }
                $previousParam = $param;

            }//end foreach

            if ($spaceBeforeVar !== 1 && $spaceBeforeVar !== 10000 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest type';
                $this->_phpcsFile->addError($error, $longestType);
            }

            if ($spaceBeforeComment !== 1 && $spaceBeforeComment !== 10000) {
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
