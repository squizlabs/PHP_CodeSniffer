<?php
/**
 * Checks for empty catch clause without a comment.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class EmptyCatchCommentSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CATCH);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $scopeStart   = $tokens[$stackPtr]['scope_opener'];
        $firstContent = $phpcsFile->findNext(T_WHITESPACE, ($scopeStart + 1), $tokens[$stackPtr]['scope_closer'], true);

        if ($firstContent === false) {
            $error = 'Empty CATCH statement must have a comment to explain why the exception is not handled';
            $phpcsFile->addError($error, $scopeStart, 'Missing');
        }

    }//end process()


}//end class
