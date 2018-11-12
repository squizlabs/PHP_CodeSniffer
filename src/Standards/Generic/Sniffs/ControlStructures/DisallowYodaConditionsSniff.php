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
        $nextIndex = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($nextIndex === false || in_array(
            $tokens[$nextIndex]['code'],
            [
                T_OPEN_SHORT_ARRAY,
                T_ARRAY,
                T_TRUE,
                T_FALSE,
                T_NULL,
                T_LNUMBER,
                T_DNUMBER,
                T_CONSTANT_ENCAPSED_STRING,
            ],
            true
        ) === true
        ) {
            return;
        }

        $previousIndex = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($previousIndex === false || in_array(
            $tokens[$previousIndex]['code'],
            [
                T_VARIABLE,
                T_STRING,
            ],
            true
        ) === true
        ) {
            return;
        }

        if ($tokens[$previousIndex]['code'] === T_CLOSE_PARENTHESIS) {
            $found = $phpcsFile->findPrevious([T_VARIABLE], ($previousIndex - 1), $tokens[$previousIndex]['parenthesis_opener']);
            if ($found !== false) {
                return;
            }
        }

        $phpcsFile->addError(
            'Usage of Yoda conditions is not allowed. Switch the expression order.',
            $stackPtr,
            'DisallowYodaCondition'
        );

    }//end process()


}//end class
