<?php
/**
 * Verifies that nullable typehints are lacking superfluous whitespace, e.g. ?int
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class NullableTypeDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_NULLABLE];

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
        $nextValidTokenPtr = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], ($stackPtr + 1), null);
        if ($nextValidTokenPtr === false) {
            // Parse error or live coding.
            return;
        }

        if ($nextValidTokenPtr !== ($stackPtr + 1)) {
            $nonWhitespaceTokenPtr = $phpcsFile->findNext([T_WHITESPACE], ($stackPtr + 1), $nextValidTokenPtr, true);

            if ($nonWhitespaceTokenPtr === false) {
                // No other tokens then whitespace tokens found; fixable.
                $fix = $phpcsFile->addFixableError('Superfluous whitespace after nullable', ($stackPtr + 1), 'WhitespaceFound');
                if ($fix === true) {
                    for ($ptr = ($stackPtr + 1); $ptr < $nextValidTokenPtr; $ptr++) {
                        $phpcsFile->fixer->replaceToken($ptr, '');
                    }
                }

                return;
            }

            // Non-whitespace tokens found; trigger error but don't fix.
            $phpcsFile->addError('Unexpected characters found after nullable', ($stackPtr + 1), 'UnexpectedCharactersFound');
        }

        return;

    }//end process()


}//end class
