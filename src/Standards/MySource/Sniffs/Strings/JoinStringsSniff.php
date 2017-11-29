<?php
/**
 * Ensures that strings are not joined using array.join().
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\MySource\Sniffs\Strings;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class JoinStringsSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['JS'];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_STRING];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param integer                     $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['content'] !== 'join') {
            return;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] !== T_OBJECT_OPERATOR) {
            return;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prev - 1), null, true);
        if ($tokens[$prev]['code'] === T_CLOSE_SQUARE_BRACKET) {
            $opener = $tokens[$prev]['bracket_opener'];
            if ($tokens[($opener - 1)]['code'] !== T_STRING) {
                // This means the array is declared inline, like x = [a,b,c].join()
                // and not elsewhere, like x = y[a].join()
                // The first is not allowed while the second is.
                $error = 'Joining strings using inline arrays is not allowed; use the + operator instead';
                $phpcsFile->addError($error, $stackPtr, 'ArrayNotAllowed');
            }
        }

    }//end process()


}//end class
