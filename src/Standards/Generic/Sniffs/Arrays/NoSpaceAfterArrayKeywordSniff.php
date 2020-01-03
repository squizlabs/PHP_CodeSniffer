<?php
/**
 * Ensure there is no space after the "array" keyword
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class NoSpaceAfterArrayKeywordSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_ARRAY];

    }//end register()


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
        $tokens = $phpcsFile->getTokens();

        $arrayStart = $tokens[$stackPtr]['parenthesis_opener'];
        if (isset($tokens[$arrayStart]['parenthesis_closer']) === false) {
            return;
        }

        if ($arrayStart !== ($stackPtr + 1)) {
            $error = 'There must be no space between the "array" keyword and the opening parenthesis';

            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), $arrayStart, true);
            if (isset(Tokens::$commentTokens[$tokens[$next]['code']]) === true) {
                // We don't have anywhere to put the comment, so don't attempt to fix it.
                $phpcsFile->addError($error, $stackPtr, 'SpaceAfterKeyword');
            } else {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($stackPtr + 1); $i < $arrayStart; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }
        }

    }//end process()


}//end class
