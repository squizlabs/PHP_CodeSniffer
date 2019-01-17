<?php
/**
 * Ensures that values submitted via JS are not compared to NULL.
 *
 * With jQuery 1.8, the behavior of ajax requests changed so that null values are
 * submitted as null= instead of null=null.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\MySource\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class AjaxNullComparisonSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FUNCTION];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure it is an API function. We know this by the doc comment.
        $commentEnd   = $phpcsFile->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $stackPtr);
        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, ($commentEnd - 1));
        // If function doesn't contain any doc comments - skip it.
        if ($commentEnd === false || $commentStart === false) {
            return;
        }

        $comment = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart));
        if (strpos($comment, '* @api') === false) {
            return;
        }

        // Find all the vars passed in as we are only interested in comparisons
        // to NULL for these specific variables.
        $foundVars = [];
        $open      = $tokens[$stackPtr]['parenthesis_opener'];
        $close     = $tokens[$stackPtr]['parenthesis_closer'];
        for ($i = ($open + 1); $i < $close; $i++) {
            if ($tokens[$i]['code'] === T_VARIABLE) {
                $foundVars[$tokens[$i]['content']] = true;
            }
        }

        if (empty($foundVars) === true) {
            return;
        }

        $start = $tokens[$stackPtr]['scope_opener'];
        $end   = $tokens[$stackPtr]['scope_closer'];
        for ($i = ($start + 1); $i < $end; $i++) {
            if ($tokens[$i]['code'] !== T_VARIABLE
                || isset($foundVars[$tokens[$i]['content']]) === false
            ) {
                continue;
            }

            $operator = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), null, true);
            if ($tokens[$operator]['code'] !== T_IS_IDENTICAL
                && $tokens[$operator]['code'] !== T_IS_NOT_IDENTICAL
            ) {
                continue;
            }

            $nullValue = $phpcsFile->findNext(T_WHITESPACE, ($operator + 1), null, true);
            if ($tokens[$nullValue]['code'] !== T_NULL) {
                continue;
            }

            $error = 'Values submitted via Ajax requests should not be compared directly to NULL; use empty() instead';
            $phpcsFile->addWarning($error, $nullValue, 'Found');
        }//end for

    }//end process()


}//end class
