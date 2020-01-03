<?php
/**
 * Ensures that empty arrays have no space between parentheses.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class EmptyArraySniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_ARRAY,
            T_OPEN_SHORT_ARRAY,
        ];

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

        if ($tokens[$stackPtr]['code'] === T_ARRAY) {
            $arrayStart = $tokens[$stackPtr]['parenthesis_opener'];

            if (isset($tokens[$arrayStart]['parenthesis_closer']) === false) {
                return;
            }

            $arrayEnd = $tokens[$arrayStart]['parenthesis_closer'];
        } else {
            $arrayStart = $stackPtr;
            $arrayEnd   = $tokens[$stackPtr]['bracket_closer'];
        }

        $content = $phpcsFile->findNext(T_WHITESPACE, ($arrayStart + 1), ($arrayEnd + 1), true);
        if ($content === $arrayEnd) {
            // Empty array, but if the brackets aren't together, there's a problem.
            if (($arrayEnd - $arrayStart) !== 1) {
                $error = 'Empty array declaration must have no space between the parentheses';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceInEmptyArray');

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($arrayStart + 1); $i < $arrayEnd; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }
        }

    }//end process()


}//end class
