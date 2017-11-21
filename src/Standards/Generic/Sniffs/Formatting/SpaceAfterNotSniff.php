<?php
/**
 * Ensures there is a single space after a NOT operator.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class SpaceAfterNotSniff implements Sniff
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

        $spacing = 0;
        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $spacing = $tokens[($stackPtr + 1)]['length'];
        }

        if ($spacing === 1) {
            return;
        }

        $message = 'There must be a single space after a NOT operator; %s found';
        $fix     = $phpcsFile->addFixableError($message, $stackPtr, 'Incorrect', [$spacing]);

        if ($fix === true) {
            if ($spacing === 0) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            } else {
                $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
            }
        }

    }//end process()


}//end class
