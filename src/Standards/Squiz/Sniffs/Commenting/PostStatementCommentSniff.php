<?php
/**
 * Checks to ensure that there are no comments after statements.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class PostStatementCommentSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_COMMENT];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
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

        if (substr($tokens[$stackPtr]['content'], 0, 2) !== '//') {
            return;
        }

        $commentLine = $tokens[$stackPtr]['line'];
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);

        if ($tokens[$lastContent]['line'] !== $commentLine) {
            return;
        }

        if ($tokens[$lastContent]['code'] === T_CLOSE_CURLY_BRACKET) {
            return;
        }

        // Special case for JS files.
        if ($tokens[$lastContent]['code'] === T_COMMA
            || $tokens[$lastContent]['code'] === T_SEMICOLON
        ) {
            $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($lastContent - 1), null, true);
            if ($tokens[$lastContent]['code'] === T_CLOSE_CURLY_BRACKET) {
                return;
            }
        }

        $error = 'Comments may not appear after statements';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Found');
        if ($fix === true) {
            $phpcsFile->fixer->addNewlineBefore($stackPtr);
        }

    }//end process()


}//end class
