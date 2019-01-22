<?php
/**
 * Verifies that the short form of type keywords is used (e.g., int, bool).
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Keywords;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ShortFormTypeKeywordsSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_BOOL_CAST,
            T_INT_CAST,
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
        $tokens     = $phpcsFile->getTokens();
        $typecast   = str_replace(' ', '', $tokens[$stackPtr]['content']);
        $typecast   = str_replace("\t", '', $typecast);
        $typecast   = trim($typecast, '()');
        $typecastLc = strtolower($typecast);

        if (($tokens[$stackPtr]['code'] === T_BOOL_CAST
            && $typecastLc === 'bool')
            || ($tokens[$stackPtr]['code'] === T_INT_CAST
            && $typecastLc === 'int')
        ) {
            return;
        }

        $error = 'Short form type keywords must be used. Found: %s';
        $data  = [$tokens[$stackPtr]['content']];
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'LongFound', $data);
        if ($fix === true) {
            if ($tokens[$stackPtr]['code'] === T_BOOL_CAST) {
                $replacement = str_replace($typecast, 'bool', $tokens[$stackPtr]['content']);
            } else {
                $replacement = str_replace($typecast, 'int', $tokens[$stackPtr]['content']);
            }

            $phpcsFile->fixer->replaceToken($stackPtr, $replacement);
        }

    }//end process()


}//end class
