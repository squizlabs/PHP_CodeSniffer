<?php
/**
 * Checks that the open tag is defined correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class OpenTagSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this sniff when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current
     *                                               token in the stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($stackPtr !== 0) {
            // This rule only applies if the open tag is on the first line of the file.
            return $phpcsFile->numTokens;
        }

        $next = $phpcsFile->findNext(T_INLINE_HTML, 0);
        if ($next !== false) {
            // This rule only applies to PHP-only files.
            return $phpcsFile->numTokens;
        }

        $tokens = $phpcsFile->getTokens();
        $next   = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$next]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Opening PHP tag must be on a line by itself';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotAlone');
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($stackPtr);
            }
        }

        return $phpcsFile->numTokens;

    }//end process()


}//end class
