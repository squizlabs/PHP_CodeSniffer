<?php
/**
 * Verifies that inline control statements are not present.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * This sniff is copied from php-fig-rectified/psr2-r package.
 *
 * @see https://github.com/php-fig-rectified/psr2r-sniffer/blob/master/PSR2R/Sniffs/ControlStructures/ConditionalExpressionOrderSniff.php
 */
class DisallowYodaConditionsSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return Tokens::$comparisonTokens;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $prevIndex = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (in_array(
            $tokens[$prevIndex]['code'],
            [
                T_CLOSE_SHORT_ARRAY,
                T_TRUE,
                T_FALSE,
                T_NULL,
                T_LNUMBER,
                T_CONSTANT_ENCAPSED_STRING,
            ],
            true
        ) === false
        ) {
            return;
        }

        if ($tokens[$prevIndex]['code'] === T_CLOSE_SHORT_ARRAY) {
            $prevIndex = $tokens[$prevIndex]['bracket_opener'];
        }

        $prevIndex = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prevIndex - 1), null, true);
        if ($prevIndex === false) {
            return;
        }

        if (in_array($tokens[$prevIndex]['code'], Tokens::$arithmeticTokens, true) === true) {
            return;
        }

        if ($tokens[$prevIndex]['code'] === T_STRING_CONCAT) {
            return;
        }

        $phpcsFile->addError(
            'Usage of Yoda conditions is not allowed. Switch the expression order.',
            $stackPtr,
            'DisallowYodaCondition'
        );

    }//end process()


}//end class
