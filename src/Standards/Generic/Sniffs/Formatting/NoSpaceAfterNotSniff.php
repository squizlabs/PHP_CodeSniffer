<?php
/**
 * Ensures there is no space after a NOT operator.
 *
 * @author    Gabriel Caruso <carusogabriel34@gmail.com>
 * @copyright 2006-2018 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NoSpaceAfterNotSniff implements Sniff
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
        return [T_BOOLEAN_NOT];

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

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            return;
        }

        $error = 'A NOT operator must not be followed by a space';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceFound');

        if ($fix === true) {
            $phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
        }

    }//end process()


}//end class
