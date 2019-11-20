<?php
/**
 * Verifies that closing braces are the last content on a line.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClosingBraceSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_FUNCTION,
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
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $closer = $tokens[$stackPtr]['scope_closer'];
        $next   = $phpcsFile->findNext(T_WHITESPACE, ($closer + 1), null, true);
        if ($next === false
            || $tokens[$next]['line'] !== $tokens[$closer]['line']
        ) {
            return;
        }

        $error = 'Closing brace must not be followed by any comment or statement on the same line';
        $phpcsFile->addError($error, $closer, 'StatementAfter');

    }//end process()


}//end class
