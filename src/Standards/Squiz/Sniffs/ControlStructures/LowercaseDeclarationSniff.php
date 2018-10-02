<?php
/**
 * Ensures all control structure keywords are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class LowercaseDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_IF,
            T_ELSE,
            T_ELSEIF,
            T_FOREACH,
            T_FOR,
            T_DO,
            T_SWITCH,
            T_WHILE,
            T_TRY,
            T_CATCH,
        ];

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

        $content = $tokens[$stackPtr]['content'];
        if ($content !== strtolower($content)) {
            $error = '%s keyword must be lowercase; expected "%s" but found "%s"';
            $data  = [
                strtoupper($content),
                strtolower($content),
                $content,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'FoundUppercase', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, strtolower($content));
            }
        }

    }//end process()


}//end class
