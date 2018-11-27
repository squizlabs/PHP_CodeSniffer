<?php
/**
 * Ensures the last member of an object is not followed by a comma.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Objects;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ObjectMemberCommaSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['JS'];


    /**
     * Registers the token types that this sniff wishes to listen to.
     *
     * @return array
     */
    public function register()
    {
        return [T_CLOSE_OBJECT];

    }//end register()


    /**
     * Process the tokens that this sniff is listening for.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_COMMA) {
            $error = 'Last member of object must not be followed by a comma';
            $fix   = $phpcsFile->addFixableError($error, $prev, 'Found');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($prev, '');
            }
        }

    }//end process()


}//end class
