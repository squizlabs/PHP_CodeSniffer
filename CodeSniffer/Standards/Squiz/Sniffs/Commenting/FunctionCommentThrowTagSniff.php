<?php
/**
 * Verifies that a @throws tag exists for a function that throws exceptions.
 * Verifies the number of @throws tags and the number of throw tokens matches.
 * Verifies the exception type.
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

require_once 'PHP/CodeSniffer/Standards/AbstractScopeSniff.php';

/**
 * Verifies that a @throws tag exists for a function that throws exceptions.
 * Verifies the number of @throws tags and the number of throw tokens matches.
 * Verifies the exception type.
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
class Squiz_Sniffs_Commenting_FunctionCommentThrowTagSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Commenting_FunctionCommentThrowTagSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_FUNCTION), array(T_THROW));

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        // Is this the first throw token within the current function scope?
        // If so, we have to validate other throw tokens within the same scope
        $previousThrow = $phpcsFile->findPrevious(T_THROW, ($stackPtr-1), $currScope);
        if ($previousThrow !== false) {
            return;
        }

        // Parse the function comment.
        $tokens       = $phpcsFile->getTokens();
        $commentEnd   = $phpcsFile->findPrevious(T_DOC_COMMENT, ($stackPtr-1));
        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $comment      = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        // Find the position where the current function scope ends.
        $currScopeEnd = 0;
        if (isset($tokens[$currScope]['scope_closer'])) {
            $currScopeEnd = $tokens[$currScope]['scope_closer'];
        }

        // Find all the exception type token within the current scope.
        $throwTokens = array();
        $currPos     = $stackPtr;
        if ($currScopeEnd !== 0) {
            while ($currPos < $currScopeEnd && $currPos !== false) {
                $currException = $phpcsFile->findNext(T_STRING, $currPos, $currScopeEnd);
                $throwTokens[] = $tokens[$currException]['content'];
                $currPos       = $phpcsFile->findNext(T_THROW, ($currPos+1), $currScopeEnd);
            }
        }

        // Check token (number, type) against the comment.
        $throws = $this->commentParser->getThrows();
        if (empty($throws) === false) {
            // Check whether the number of throw tags matches.
            $expectedTag = count($throwTokens);
            $foundTag    = count($throws);
            if ($foundTag !== $expectedTag) {
                $tags  = ($expectedTag > 1) ? 'tags' : 'tag';
                $error = "Expected $expectedTag @throws $tags in function comment; $foundTag found";
                $phpcsFile->addError($error, $commentEnd);
                return;
            } else {
                // Validate throw tag exception type.
                foreach ($throws as $i => $throw) {
                    $exception = $throw->getValue();
                    if (empty($exception) === false && $exception !== $throwTokens[$i]) {
                        $error    = "Expected \"$throwTokens[$i]\" but found \"$exception\" for @throws tag exception";
                        $errorPos = ($commentStart + $throw->getLine());
                        $phpcsFile->addError($error, $errorPos);
                    }
                }
            }
        } else {
            $error = 'Missing @throws tag in function comment';
            $phpcsFile->addError($error, $commentEnd);
        }

    }//end processTokenWithinScope()


}//end class
?>