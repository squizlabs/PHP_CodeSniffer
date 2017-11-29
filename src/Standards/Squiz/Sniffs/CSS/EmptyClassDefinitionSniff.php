<?php
/**
 * Ensure that class definitions are not empty.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class EmptyClassDefinitionSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_OPEN_CURLY_BRACKET];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
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
        $next   = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($next === false || $tokens[$next]['code'] === T_CLOSE_CURLY_BRACKET) {
            $error = 'Class definition is empty';
            $phpcsFile->addError($error, $stackPtr, 'Found');
        }

    }//end process()


}//end class
