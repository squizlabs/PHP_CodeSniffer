<?php
/**
 * Ensures all function keywords are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class LowercaseFunctionKeywordsSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $tokens   = Tokens::$methodPrefixes;
        $tokens[] = T_FUNCTION;
        $tokens[] = T_CLOSURE;

        return $tokens;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $content   = $tokens[$stackPtr]['content'];
        $contentLc = strtolower($content);
        if ($content !== $contentLc) {
            $error = '%s keyword must be lowercase; expected "%s" but found "%s"';
            $data  = [
                strtoupper($content),
                $contentLc,
                $content,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'FoundUppercase', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $contentLc);
            }
        }

    }//end process()


}//end class
