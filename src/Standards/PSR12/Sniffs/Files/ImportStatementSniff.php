<?php
/**
 * Verifies that import statements are defined correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ImportStatementSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_USE];

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

        // Make sure this is not a closure USE group.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return;
        }

        if ($phpcsFile->hasCondition($stackPtr, Tokens::$ooScopeTokens) === true) {
            // This rule only applies to import statements.
            return;
        }

        if ($tokens[$next]['code'] === T_STRING
            && (strtolower($tokens[$next]['content']) === 'function'
            || strtolower($tokens[$next]['content']) === 'const')
        ) {
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
        }

        if ($tokens[$next]['code'] !== T_NS_SEPARATOR) {
            return;
        }

        $error = 'Import statements must not begin with a leading backslash';
        $fix   = $phpcsFile->addFixableError($error, $next, 'LeadingSlash');

        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($next, '');
        }

    }//end process()


}//end class
