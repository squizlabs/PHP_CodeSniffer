<?php
/**
 * Ensures the file does not end with a newline character.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class EndFileNoNewlineSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
        'CSS',
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO,
        ];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Skip to the end of the file.
        $tokens   = $phpcsFile->getTokens();
        $stackPtr = ($phpcsFile->numTokens - 1);

        if ($tokens[$stackPtr]['content'] === '') {
            --$stackPtr;
        }

        $eolCharLen = strlen($phpcsFile->eolChar);
        $lastChars  = substr($tokens[$stackPtr]['content'], ($eolCharLen * -1));
        if ($lastChars === $phpcsFile->eolChar) {
            $error = 'File must not end with a newline character';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Found');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                for ($i = $stackPtr; $i > 0; $i--) {
                    $newContent = rtrim($tokens[$i]['content'], $phpcsFile->eolChar);
                    $phpcsFile->fixer->replaceToken($i, $newContent);

                    if ($newContent !== '') {
                        break;
                    }
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
