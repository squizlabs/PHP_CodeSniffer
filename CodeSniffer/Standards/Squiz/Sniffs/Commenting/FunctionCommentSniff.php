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

require_once 'PHP/CodeSniffer/CommentParser/FunctionCommentParser.php';
require_once 'PHP/CodeSniffer/Standards/AbstractScopeSniff.php';

/**
 * Parses and verifies the doc comments for functions.
 *
 * Verifies that :
 * <ul>
 *  <li>A comment exists</li>
 *  <li>There is a blank newline after the short description</li>
 *  <li>There is a blank newline between the long and short description</li>
 *  <li>There is a blank newline between the long description and tags</li>
 *  <li>Parameter names represent those in the method</li>
 *  <li>Parameter comments are in the correct order</li>
 *  <li>Parameter comments are complete</li>
 *  <li>A type hint is provided for array and custom class</li>
 *  <li>Type hint matches the actual variable/class type</li>
 *  <li>A blank line is present before the first and after the last parameter</li>
 *  <li>A return type exists</li>
 *  <li>Any throw tag must have a comment</li>
 *  <li>The tag order and indentation are correct</li>
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
class Squiz_Sniffs_Commenting_FunctionCommentSniff implements PHP_CodeSniffer_Sniff
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
     * The index of the current tag we are processing.
     *
     * @var int
     */
    private $_tagIndex = 0;

    /**
     * The function comment parser for the current method.
     *
     * @var PHP_CodeSniffer_Comment_Parser_FunctionCommentParser
     */
    protected $commentParser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentFile = null;


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
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();

        $find = array(
                 T_COMMENT,
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1));

        if ($commentEnd === false) {
            return;
        }

        // If the token that we found was a class or a function, then this
        // function has no doc comment.
        $code = $tokens[$commentEnd]['code'];

        if ($code === T_COMMENT) {
            $error = 'You must use "/**" style comments for a function comment';
            $phpcsFile->addError($error, $stackPtr);
            return;
        } else if ($code !== T_DOC_COMMENT) {
            $error = 'Missing function doc comment';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        $this->_functionToken = $stackPtr;
        $classToken           = $phpcsFile->findPrevious(array(T_CLASS, T_INTERFACE), ($stackPtr - 1));
        if ($classToken !== false) {
            $this->_classToken = $classToken;
        }

        // Find the first doc comment.
        $commentStart      = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $comment           = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));
        $this->_methodName = $phpcsFile->getDeclarationName($stackPtr);

        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Function doc comment is empty';
            $phpcsFile->addError($error, $commentStart);
            return;
        }

        $this->processParams($commentStart, $commentEnd);
        $this->processSince($commentStart, $commentEnd);
        $this->processSees($commentStart);
        $this->processReturn($commentStart, $commentEnd);
        $this->processThrows($commentStart);

        // Check for a comment description.
        $short = $comment->getShortComment();
        if (trim($short) === '') {
            $error = 'Missing short description in function doc comment';
            $phpcsFile->addError($error, $commentStart);
            return;
        }

        // No extra newline before short description.
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before function comment short description";
            $phpcsFile->addError($error, ($commentStart + 1));
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in function comment';
                $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
            }

            $newlineCount += $newlineBetween;
        }

        // Exactly one blank line before tags.
        $params = $this->commentParser->getTagOrders();
        if (count($params) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in function comment';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                $phpcsFile->addError($error, ($commentStart + $newlineCount));
                $short = rtrim($short, $phpcsFile->eolChar.' ');
            }
        }

        // Short description must be single line and end with a full stop.
        $lastChar = $short[(strlen($short) - 1)];
        if (substr_count($short, $phpcsFile->eolChar) !== 0) {
            $error = 'Function comment short description must be on a single line';
            $phpcsFile->addError($error, ($commentStart + 1));
        }

        if ($lastChar !== '.') {
            $error = 'Function comment short description must end with a full stop';
            $phpcsFile->addError($error, ($commentStart + 1));
        }

        // Check for unknown/deprecated tags.
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $error = "@$errorTag[tag] tag is not allowed in function comment";
            $phpcsFile->addWarning($error, ($commentStart + $errorTag['line']));
        }

    }//end process()


    /**
     * Process the since tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processSince($commentStart, $commentEnd)
    {
        $since = $this->commentParser->getSince();
        if ($since !== null) {
            $errorPos = ($commentStart + $since->getLine());
            $tagOrder = $this->commentParser->getTagOrders();
            $firstTag = 0;

            while ($tagOrder[$firstTag] === 'comment' || $tagOrder[$firstTag] === 'param') {
                $firstTag++;
            }

            $this->_tagIndex = $firstTag;
            $index           = array_keys($this->commentParser->getTagOrders(), 'since');
            if (count($index) > 1) {
                $error = 'Only 1 @since tag is allowed in function comment';
                $this->currentFile->addError($error, $errorPos);
                return;
            }

            if ($index[0] !== $firstTag) {
                $error = 'The order of @since tag is wrong in function comment';
                $this->currentFile->addError($error, $errorPos);
            }

            $content = $since->getContent();
            if (empty($content) === true) {
                $error = 'Version number missing for @since tag in function comment';
                $this->currentFile->addError($error, $errorPos);
                return;
            } else if ($content !== '%release_version%') {
                if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $content) === 0) {
                    $error = 'Expected version number to be in the form x.x.x in @since tag';
                    $this->currentFile->addError($error, $errorPos);
                }
            }

            $spacing        = substr_count($since->getWhitespaceBeforeContent(), ' ');
            $return         = $this->commentParser->getReturn();
            $throws         = $this->commentParser->getThrows();
            $correctSpacing = ($return !== null || empty($throws) === false) ? 2 : 1;

            if ($spacing !== $correctSpacing) {
                $error  = '@since tag indented incorrectly. ';
                $error .= "Expected $correctSpacing spaces but found $spacing.";
                $this->currentFile->addError($error, $errorPos);
            }
        } else {
            $error = 'Missing @since tag in function comment';
            $this->currentFile->addError($error, $commentEnd);
        }//end if

    }//end processSince()


    /**
     * Process the see tags.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processSees($commentStart)
    {
        $sees = $this->commentParser->getSees();
        if (empty($sees) === false) {
            $tagOrder = $this->commentParser->getTagOrders();
            $index    = array_keys($this->commentParser->getTagOrders(), 'see');
            foreach ($sees as $i => $see) {
                $errorPos = ($commentStart + $see->getLine());
                $since    = array_keys($tagOrder, 'since');
                if (count($since) === 1 && $this->_tagIndex !== 0) {
                    $this->_tagIndex++;
                    if ($index[$i] !== $this->_tagIndex) {
                        $error = 'The order of @see tag is wrong in function comment';
                        $this->currentFile->addError($error, $errorPos);
                    }
                }

                $content = $see->getContent();
                if (empty($content) === true) {
                    $error = 'Content missing for @see tag in function comment';
                    $this->currentFile->addError($error, $errorPos);
                    continue;
                }

                $spacing = substr_count($see->getWhitespaceBeforeContent(), ' ');
                if ($spacing !== 4) {
                    $error  = '@see tag indented incorrectly. ';
                    $error .= "Expected 4 spaces but found $spacing.";
                    $this->currentFile->addError($error, $errorPos);
                }
            }//end foreach
        }//end if

    }//end processSees()


    /**
     * Process the return comment of this function comment.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processReturn($commentStart, $commentEnd)
    {
        // Skip constructor and destructor.
        $className = '';
        if ($this->_classToken !== null) {
            $className = $this->currentFile->getDeclarationName($this->_classToken);
            $className = strtolower(ltrim($className, '_'));
        }

        $methodName      = strtolower(ltrim($this->_methodName, '_'));
        $isSpecialMethod = ($this->_methodName === '__construct' || $this->_methodName === '__destruct');
        $return          = $this->commentParser->getReturn();

        if ($isSpecialMethod === false && $methodName !== $className) {
            if ($return !== null) {
                $tagOrder = $this->commentParser->getTagOrders();
                $index    = array_keys($tagOrder, 'return');
                $errorPos = ($commentStart + $return->getLine());
                $content  = trim($return->getRawContent());

                if (count($index) > 1) {
                    $error = 'Only 1 @return tag is allowed in function comment';
                    $this->currentFile->addError($error, $errorPos);
                    return;
                }

                $since = array_keys($tagOrder, 'since');
                if (count($since) === 1 && $this->_tagIndex !== 0) {
                    $this->_tagIndex++;
                    if ($index[0] !== $this->_tagIndex) {
                        $error = 'The order of @return tag is wrong in function comment';
                        $this->currentFile->addError($error, $errorPos);
                    }
                }

                if (empty($content) === true) {
                    $error = 'Return type missing for @return tag in function comment';
                    $this->currentFile->addError($error, $errorPos);
                } else {
                    // Check return type (can be multiple, separated by '|').
                    $typeNames      = explode('|', $content);
                    $suggestedNames = array();
                    foreach ($typeNames as $i => $typeName) {
                        $suggestedName = PHP_CodeSniffer::suggestType($typeName);
                        if (in_array($suggestedName, $suggestedNames) === false) {
                            $suggestedNames[] = $suggestedName;
                        }
                    }

                    $suggestedType = implode('|', $suggestedNames);
                    if ($content !== $suggestedType) {
                        $error = "Function return type \"$content\" is invalid";
                        $this->currentFile->addError($error, $errorPos);
                    }

                    // If the return type is void, make sure there is
                    // no return statement in the function.
                    if ($content === 'void') {
                        $tokens   = $this->currentFile->getTokens();
                        if (isset($tokens[$this->_functionToken]['scope_closer']) === true) {
                            $endToken = $tokens[$this->_functionToken]['scope_closer'];
                            $return   = $this->currentFile->findNext(T_RETURN, $this->_functionToken, $endToken);
                            if ($return !== false) {
                                // If the function is not returning anything, just
                                // exiting, then there is no problem.
                                $semicolon = $this->currentFile->findNext(T_WHITESPACE, ($return + 1), null, true);
                                if ($tokens[$semicolon]['code'] !== T_SEMICOLON) {
                                    $error = 'Function return type is void, but function contains return statement';
                                    $this->currentFile->addError($error, $errorPos);
                                }
                            }
                        }
                    }
                }//end if
            } else {
                $error = 'Missing @return tag in function comment';
                $this->currentFile->addError($error, $commentEnd);
            }//end if

        } else {
            // No return tag for constructor and destructor.
            if ($return !== null) {
                $errorPos = ($commentStart + $return->getLine());
                $error    = '@return tag is not required for constructor and destructor';
                $this->currentFile->addError($error, $errorPos);
            }
        }//end if

    }//end processReturn()


    /**
     * Process any throw tags that this function comment has.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processThrows($commentStart)
    {
        if (count($this->commentParser->getThrows()) === 0) {
            return;
        }

        $tagOrder = $this->commentParser->getTagOrders();
        $index    = array_keys($this->commentParser->getTagOrders(), 'throws');

        foreach ($this->commentParser->getThrows() as $i => $throw) {
            $exception = $throw->getValue();
            $content   = trim($throw->getComment());
            $errorPos  = ($commentStart + $throw->getLine());
            if (empty($exception) === true) {
                $error = 'Exception type and comment missing for @throws tag in function comment';
                $this->currentFile->addError($error, $errorPos);
            } else if (empty($content) === true) {
                $error = 'Comment missing for @throws tag in function comment';
                $this->currentFile->addError($error, $errorPos);
            } else {
                // Starts with a capital letter and ends with a fullstop.
                $firstChar = $content{0};
                if (strtoupper($firstChar) !== $firstChar) {
                    $error = '@throws tag comment must start with a capital letter';
                    $this->currentFile->addError($error, $errorPos);
                }

                $lastChar = $content[(strlen($content) - 1)];
                if ($lastChar !== '.') {
                    $error = '@throws tag comment must end with a full stop';
                    $this->currentFile->addError($error, $errorPos);
                }
            }

            $since = array_keys($tagOrder, 'since');
            if (count($since) === 1 && $this->_tagIndex !== 0) {
                $this->_tagIndex++;
                if ($index[$i] !== $this->_tagIndex) {
                    $error = 'The order of @throws tag is wrong in function comment';
                    $this->currentFile->addError($error, $errorPos);
                }
            }
        }//end foreach

    }//end processThrows()


    /**
     * Process the function parameter comments.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     * @param int $commentEnd   The position in the stack where
     *                          the comment ended.
     *
     * @return void
     */
    protected function processParams($commentStart, $commentEnd)
    {
        $realParams  = $this->currentFile->getMethodParameters($this->_functionToken);
        $params      = $this->commentParser->getParams();
        $foundParams = array();

        if (empty($params) === false) {

            if (substr_count($params[(count($params) - 1)]->getWhitespaceAfter(), $this->currentFile->eolChar) !== 2) {
                $error    = 'Last parameter comment requires a blank newline after it';
                $errorPos = ($params[(count($params) - 1)]->getLine() + $commentStart);
                $this->currentFile->addError($error, $errorPos);
            }

            // Parameters must appear immediately after the comment.
            if ($params[0]->getOrder() !== 2) {
                $error    = 'Parameters must appear immediately after the comment';
                $errorPos = ($params[0]->getLine() + $commentStart);
                $this->currentFile->addError($error, $errorPos);
            }

            $previousParam      = null;
            $spaceBeforeVar     = 10000;
            $spaceBeforeComment = 10000;
            $longestType        = 0;
            $longestVar         = 0;

            foreach ($params as $param) {

                $paramComment = trim($param->getComment());
                $errorPos     = ($param->getLine() + $commentStart);

                // Make sure that there is only one space before the var type.
                if ($param->getWhitespaceBeforeType() !== ' ') {
                    $error = 'Expected 1 space before variable type';
                    $this->currentFile->addError($error, $errorPos);
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
                $pos       = $param->getPosition();
                $paramName = ($param->getVarName() !== '') ? $param->getVarName() : '[ UNKNOWN ]';

                if ($previousParam !== null) {
                    $previousName = ($previousParam->getVarName() !== '') ? $previousParam->getVarName() : 'UNKNOWN';

                    // Check to see if the parameters align properly.
                    if ($param->alignsWith($previousParam) === false) {
                        $error = 'Parameters '.$previousName.' ('.($pos - 1).') and '.$paramName.' ('.$pos.') do not align';
                        $this->currentFile->addError($error, $errorPos);
                    }
                }

                // Variable must be one of the supported standard type.
                $typeNames = explode('|', $param->getType());
                foreach ($typeNames as $typeName) {
                    $suggestedName = PHP_CodeSniffer::suggestType($typeName);
                    if ($typeName !== $suggestedName) {
                        $error = "Expected \"$suggestedName\"; found \"$typeName\" for $paramName at position $pos";
                        $this->currentFile->addError($error, $errorPos);
                    } else if (count($typeNames) === 1) {
                        // Check type hint for array and custom type.
                        $suggestedTypeHint = '';
                        if (strpos($suggestedName, 'array') !== false) {
                            $suggestedTypeHint = 'array';
                        } else if (in_array($typeName, PHP_CodeSniffer::$allowedTypes) === false) {
                            $suggestedTypeHint = $suggestedName;
                        }

                        if ($suggestedTypeHint !== '' && isset($realParams[($pos - 1)]) === true) {
                            $typeHint = $realParams[($pos - 1)]['type_hint'];
                            if ($typeHint === '') {
                                $error = "Type hint \"$suggestedTypeHint\" missing for $paramName at position $pos";
                                $this->currentFile->addError($error, ($commentEnd + 2));
                            } else if ($typeHint !== $suggestedTypeHint) {
                                $error = "Expected type hint \"$suggestedTypeHint\"; found \"$typeHint\" for $paramName at position $pos";
                                $this->currentFile->addError($error, ($commentEnd + 2));
                            }
                        } else if ($suggestedTypeHint === '' && isset($realParams[($pos - 1)]) === true) {
                            $typeHint = $realParams[($pos - 1)]['type_hint'];
                            if ($typeHint !== '') {
                                $error = "Unknown type hint \"$typeHint\" found for $paramName at position $pos";
                                $this->currentFile->addError($error, ($commentEnd + 2));
                            }
                        }
                    }//end if
                }//end foreach

                // Make sure the names of the parameter comment matches the
                // actual parameter.
                if (isset($realParams[($pos - 1)]) === true) {
                    $realName      = $realParams[($pos - 1)]['name'];
                    $foundParams[] = $realName;

                    // Append ampersand to name if passing by reference.
                    if ($realParams[($pos - 1)]['pass_by_reference'] === true) {
                        $realName = '&'.$realName;
                    }

                    if ($realName !== $param->getVarName()) {
                        $error  = 'Doc comment var "'.$paramName;
                        $error .= '" does not match actual variable name "'.$realName;
                        $error .= '" at position '.$pos;
                        $this->currentFile->addError($error, $errorPos);
                    }
                } else {
                    // We must have an extra parameter comment.
                    $error = 'Superfluous doc comment at position '.$pos;
                    $this->currentFile->addError($error, $errorPos);
                }

                if ($param->getVarName() === '') {
                    $error = 'Missing parameter name at position '.$pos;
                     $this->currentFile->addError($error, $errorPos);
                }

                if ($param->getType() === '') {
                    $error = 'Missing type at position '.$pos;
                    $this->currentFile->addError($error, $errorPos);
                }

                if ($paramComment === '') {
                    $error = 'Missing comment for param "'.$paramName.'" at position '.$pos;
                    $this->currentFile->addError($error, $errorPos);
                } else {
                    // Param comments must start with a capital letter and
                    // end with the full stop.
                    $firstChar = $paramComment{0};
                    if (strtoupper($firstChar) !== $firstChar) {
                        $error = 'Param comment must start with a capital letter';
                        $this->currentFile->addError($error, $errorPos);
                    }

                    $lastChar = $paramComment[(strlen($paramComment) - 1)];
                    if ($lastChar !== '.') {
                        $error = 'Param comment must end with a full stop';
                        $this->currentFile->addError($error, $errorPos);
                    }
                }

                $previousParam = $param;

            }//end foreach

            if ($spaceBeforeVar !== 1 && $spaceBeforeVar !== 10000 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest type';
                $this->currentFile->addError($error, $longestType);
            }

            if ($spaceBeforeComment !== 1 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest variable name';
                $this->currentFile->addError($error, $longestVar);
            }

        }//end if

        $realNames = array();
        foreach ($realParams as $realParam) {
            $realNames[] = $realParam['name'];
        }

        // Report missing comments.
        $diff = array_diff($realNames, $foundParams);
        foreach ($diff as $neededParam) {
            if (count($params) !== 0) {
                $errorPos = ($params[(count($params) - 1)]->getLine() + $commentStart);
            } else {
                $errorPos = $commentStart;
            }

            $error = 'Doc comment for "'.$neededParam.'" missing';
            $this->currentFile->addError($error, $errorPos);
        }

    }//end processParams()


}//end class

?>