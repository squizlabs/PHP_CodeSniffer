<?php
/**
 * Parses and verifies the doc comments for functions.
 *
 * Same as the Squiz standard, but adds support for API tags.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\MySource\Sniffs\Commenting;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\FunctionCommentSniff as SquizFunctionCommentSniff;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;

class FunctionCommentSniff extends SquizFunctionCommentSniff
{


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        parent::process($phpcsFile, $stackPtr);

        $tokens = $phpcsFile->getTokens();
        $find   = Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        $hasApiTag    = false;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@api') {
                if ($hasApiTag === true) {
                    // We've come across an API tag already, which means
                    // we were not the first tag in the API list.
                    $error = 'The @api tag must come first in the @api tag list in a function comment';
                    $phpcsFile->addError($error, $tag, 'ApiNotFirst');
                }

                $hasApiTag = true;

                // There needs to be a blank line before the @api tag.
                $prev = $phpcsFile->findPrevious(array(T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG), ($tag - 1));
                if ($tokens[$prev]['line'] !== ($tokens[$tag]['line'] - 2)) {
                    $error = 'There must be one blank line before the @api tag in a function comment';
                    $phpcsFile->addError($error, $tag, 'ApiSpacing');
                }
            } else if (substr($tokens[$tag]['content'], 0, 5) === '@api-') {
                $hasApiTag = true;

                $prev = $phpcsFile->findPrevious(array(T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG), ($tag - 1));
                if ($tokens[$prev]['line'] !== ($tokens[$tag]['line'] - 1)) {
                    $error = 'There must be no blank line before the @%s tag in a function comment';
                    $data  = array($tokens[$tag]['content']);
                    $phpcsFile->addError($error, $tag, 'ApiTagSpacing', $data);
                }
            }//end if
        }//end foreach

        if ($hasApiTag === true && substr($tokens[$tag]['content'], 0, 4) !== '@api') {
            // API tags must be the last tags in a function comment.
            $error = 'The @api tags must be the last tags in a function comment';
            $phpcsFile->addError($error, $commentEnd, 'ApiNotLast');
        }

    }//end process()


}//end class
