<?php
/**
 * Verifies that there are no else if statements (elseif should be used instead).
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ElseIfDeclarationSniff implements Sniff
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

        if ($tokens[$stackPtr]['code'] === T_ELSEIF) {
            $phpcsFile->recordMetric($stackPtr, 'Use of ELSE IF or ELSEIF', 'elseif');
            return;
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_IF) {
            $phpcsFile->recordMetric($stackPtr, 'Use of ELSE IF or ELSEIF', 'else if');
            $error = 'Usage of ELSE IF is discouraged; use ELSEIF instead';
            $fix   = $phpcsFile->addFixableWarning($error, $stackPtr, 'NotAllowed');

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, 'elseif');
                for ($i = ($stackPtr + 1); $i <= $next; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

    }//end process()


}//end class
