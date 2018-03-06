<?php
/**
 * Verifies that the closing brace is on the same line as an else/elseif etc.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ClosingBracePlacementSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_ELSE,
            T_ELSEIF,
            T_CATCH,
            T_FINALLY,
            T_WHILE,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $brace  = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($brace === false
            || $tokens[$brace]['code'] !== T_CLOSE_CURLY_BRACKET
            || isset($tokens[$brace]['scope_condition']) === false
        ) {
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_WHILE
            && $tokens[$tokens[$brace]['scope_condition']]['code'] !== T_DO
        ) {
            return;
        }

        if ($tokens[$brace]['line'] !== $tokens[$stackPtr]['line']) {
            $error = 'Control structure keyword must be on the same line as the closing brace from the earlier body';

            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($prev === $brace) {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NotSameLine');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($brace + 1); $i < $stackPtr; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addContent($brace, ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $phpcsFile->addError($error, $stackPtr, 'NotSameLine');
            }
        }

    }//end process()


}//end class
