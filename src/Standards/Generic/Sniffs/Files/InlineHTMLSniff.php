<?php
/**
 * Ensures the whole file is PHP only, with no whitespace or inline HTML.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class InlineHTMLSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_INLINE_HTML];

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
        // Ignore shebang lines.
        $tokens = $phpcsFile->getTokens();
        if (substr($tokens[$stackPtr]['content'], 0, 2) === '#!') {
            return;
        }

        $error = 'PHP files must only contain PHP code';
        $phpcsFile->addError($error, $stackPtr, 'Found');

        return $phpcsFile->numTokens;

    }//end process()


}//end class
