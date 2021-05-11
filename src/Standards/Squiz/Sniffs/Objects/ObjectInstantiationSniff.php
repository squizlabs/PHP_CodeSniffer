<?php
/**
 * Ensures objects are assigned to a variable when instantiated.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Objects;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ObjectInstantiationSniff implements Sniff
{


    /**
     * Registers the token types that this sniff wishes to listen to.
     *
     * @return array
     */
    public function register()
    {
        return [T_NEW];

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

        $allowedTokens   = Tokens::$emptyTokens;
        $allowedTokens[] = T_BITWISE_AND;

        $prev = $phpcsFile->findPrevious($allowedTokens, ($stackPtr - 1), null, true);

        $allowedTokens = [
            T_EQUAL          => T_EQUAL,
            T_COALESCE_EQUAL => T_COALESCE_EQUAL,
            T_DOUBLE_ARROW   => T_DOUBLE_ARROW,
            T_FN_ARROW       => T_FN_ARROW,
            T_MATCH_ARROW    => T_MATCH_ARROW,
            T_THROW          => T_THROW,
            T_RETURN         => T_RETURN,
        ];

        if (isset($allowedTokens[$tokens[$prev]['code']]) === true) {
            return;
        }

        $ternaryLikeTokens = [
            T_COALESCE    => true,
            T_INLINE_THEN => true,
            T_INLINE_ELSE => true,
        ];

        // For ternary like tokens, walk a little further back to see if it is preceded by
        // one of the allowed tokens (within the same statement).
        if (isset($ternaryLikeTokens[$tokens[$prev]['code']]) === true) {
            $hasAllowedBefore = $phpcsFile->findPrevious($allowedTokens, ($prev - 1), null, false, null, true);
            if ($hasAllowedBefore !== false) {
                return;
            }
        }

        $error = 'New objects must be assigned to a variable';
        $phpcsFile->addError($error, $stackPtr, 'NotAssigned');

    }//end process()


}//end class
