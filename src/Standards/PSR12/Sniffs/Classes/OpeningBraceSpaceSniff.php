<?php
/**
 * Verifies that opening braces are not followed by blank lines.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class OpeningBraceSpaceSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return Tokens::$ooScopeTokens;

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
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        $opener = $tokens[$stackPtr]['scope_opener'];
        $next   = $phpcsFile->findNext(T_WHITESPACE, ($opener + 1), null, true);
        if ($next === false
            || $tokens[$next]['line'] <= ($tokens[$opener]['line'] + 1)
        ) {
            return;
        }

        $error = 'Opening brace must not be followed by a blank line';
        $fix   = $phpcsFile->addFixableError($error, $opener, 'Found');
        if ($fix === false) {
            return;
        }

        $phpcsFile->fixer->beginChangeset();
        for ($i = ($opener + 1); $i < $next; $i++) {
            if ($tokens[$i]['line'] === $tokens[$opener]['line']) {
                continue;
            }

            if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                break;
            }

            $phpcsFile->fixer->replaceToken($i, '');
        }

        $phpcsFile->fixer->endChangeset();

    }//end process()


}//end class
