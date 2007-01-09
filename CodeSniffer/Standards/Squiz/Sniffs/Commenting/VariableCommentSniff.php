<?php
/**
 * Parses and verifies the variable doc comment.
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

require_once 'PHP/CodeSniffer/Standards/AbstractVariableSniff.php';
require_once 'PHP/CodeSniffer/CommentParser/MemberCommentParser.php';

/**
 * Parses and verifies the variable doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A variable doc comment exists.</li>
 *  <li>Short description ends with a full stop.</li>
 *  <li>There is a blank line after the short description.</li>
 *  <li>There is a blank line between the description and the tags.</li>
 *  <li>Check the order, indentation and content of each tag.</li>
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

class Squiz_Sniffs_Commenting_VariableCommentSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{

    /**
     * Called to process class member vars.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_phpcsFile = $phpcsFile;
        $tokens = $this->_phpcsFile->getTokens();

        // Extract the var comment docblock.
        $commentEnd = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 3, null, true);
        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $this->_phpcsFile->addError('You must use "/**" style comments for a variable comment', $stackPtr);
            return;
        } else if ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
            $this->_phpcsFile->addError('Missing variable doc comment', $stackPtr);
            return;
        }
        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT, $commentEnd - 1, null, true) + 1;
        $comment      = $this->_phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);

        // Parse the header comment docblock.
        try {
            $this->_fp = new PHP_CodeSniffer_CommentParser_MemberCommentParser($comment);
            $this->_fp->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = $e->getLineWithinComment() + $commentStart;
            $this->_phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        // No extra newline before short description.
        $comment      = $this->_fp->getComment();
        $short        = $comment->getShortComment();
        $newlineCount = 0;
        $newlineSpan  = strspn($short, "\n");
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before variable comment short description";
            $phpcsFile->addError($error, $commentStart + 1);
        }
        $newlineCount = substr_count($short, "\n") + 1;

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (!empty($long)) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, "\n");
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in variable comment';
                $phpcsFile->addError($error, $commentStart + $newlineCount + 1);
            }
            $newlineCount += $newlineBetween;
        }

        // Exactly one blank line before tags.
        $tags = $this->_fp->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in variable comment';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, "\n") - $newlineSpan + 1);
                }
                $phpcsFile->addError($error, $commentStart + $newlineCount);
                $short = rtrim($short, "\n ");
            }
        }

        // Short description must be single line and end with a full stop.
        $lastChar = $short[strlen($short)-1];
        if (substr_count($short, "\n") !== 0) {
            $error = "Variable comment short description must be on a single line";
            $phpcsFile->addError($error, $commentStart + 1);
        }
        if ($lastChar !== '.') {
            $error = "Variable comment short description must end with a full stop";
            $phpcsFile->addError($error, $commentStart + 1);
        }

        // Check for unknown/deprecated tags.
        $unknownTags = $this->_fp->getUnknown();
        foreach ($unknownTags as $errorTag) {
            // Unknown tags are not parsed, do not process further.
            $error = ucfirst($errorTag['tag']).' tag is not allowed in variable comment';
            $phpcsFile->addWarning($error, $commentStart + $errorTag['line']);
            return;
        }

        // Check each tag.
        $this->_processVar($commentStart, $commentEnd);
        $this->_processSince($commentStart, $commentEnd);
        $this->_processSees($commentStart);

    }//end process()


    /**
     * Process the var tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    private function _processVar($commentStart, $commentEnd)
    {
        $var = $this->_fp->getVar();
        if ($var !== null) {
            $errorPos = $commentStart + $var->getLine();
            $index    = array_keys($this->_fp->getTagOrders(), 'var');
            if (count($index) > 1) {
                $error = 'Only 1 var tag is allowed in variable comment';
                $this->_phpcsFile->addError($error, $errorPos);
                return;
            }
            if ($index[0] !== 1) {
                $error = 'The order of var tag is wrong in variable comment';
                $this->_phpcsFile->addError($error, $errorPos);
            }
            $content = $var->getContent();
            if (empty($content)) {
                $error = 'Var type missing for var tag in variable comment';
                $this->_phpcsFile->addError($error, $errorPos);
                return;
            }
            $spacing = substr_count($var->getWhitespaceBeforeContent(), ' ');
            if ($spacing !== 3) {
                $error  = 'Var tag indented incorrectly. ';
                $error .= "Expected 3 spaces but found $spacing.";
                $this->_phpcsFile->addError($error, $errorPos);
            }

        } else {
            $error = 'Missing required var tag in variable comment';
            $this->_phpcsFile->addError($error, $commentEnd);
        }

    }//end _processVar()


    /**
     * Process the since tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    private function _processSince($commentStart, $commentEnd)
    {
        $since = $this->_fp->getSince();
        if ($since !== null) {
            $errorPos  = $commentStart + $since->getLine();
            $foundTags = $this->_fp->getTagOrders();
            $index     = array_keys($foundTags, 'since');
            $var       = array_keys($foundTags, 'var');
            if (count($index) > 1) {
                $error = 'Only 1 since tag is allowed in variable comment';
                $this->_phpcsFile->addError($error, $errorPos);
                return;
            }
            if (count($var) === 1 && $index[0] !== 2) {
                $error = 'The order of since tag is wrong in variable comment';
                $this->_phpcsFile->addError($error, $errorPos);
            }
            $content = $since->getContent();
            if (empty($content)) {
                $error = 'Version number missing for since tag in variable comment';
                $this->_phpcsFile->addError($error, $errorPos);
                return;
            } else if ($content !== '%release_version%') {
                if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $content) === 0) {
                    $error = 'Expected version number to be in the form x.x.x in since tag';
                    $this->_phpcsFile->addError($error, $errorPos);
                }
            }
            $spacing = substr_count($since->getWhitespaceBeforeContent(), ' ');
            if ($spacing !== 1) {
                $error  = 'Since tag indented incorrectly. ';
                $error .= "Expected 1 space but found $spacing.";
                $this->_phpcsFile->addError($error, $errorPos);
            }
        } else {
            $error = 'Missing required since tag in variable comment';
            $this->_phpcsFile->addError($error, $commentEnd);
        }

    }//end _processSince()


    /**
     * Process the see tags.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    private function _processSees($commentStart)
    {
        $sees = $this->_fp->getSees();
        if (!empty($sees)) {
            foreach ($sees as $see) {
                $errorPos = $commentStart + $see->getLine();
                $content  = $see->getContent();
                if (empty($content)) {
                    $error = 'Content missing for see tag in variable comment';
                    $this->_phpcsFile->addError($error, $errorPos);
                    continue;
                }
                $spacing = substr_count($see->getWhitespaceBeforeContent(), ' ');
                if ($spacing !== 3) {
                    $error  = 'See tag indented incorrectly. ';
                    $error .= "Expected 3 spaces but found $spacing.";
                    $this->_phpcsFile->addError($error, $errorPos);
                }
            }
        }

    }//end _processSees()


    /**
     * Called to process a normal variable.
     *
     * Not required for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the double quoted
     *                                        string was found.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        return;
    }

    /**
     * Called to process variables found in duoble quoted strings.
     *
     * Not required for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the double quoted
     *                                        string was found.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        return;
    }

}//end class
?>