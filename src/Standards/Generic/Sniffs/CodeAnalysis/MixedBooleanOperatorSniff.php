<?php
/**
 * Forbid mixing different binary boolean operators within a single expression without making precedence
 * clear using parentheses.
 *
 * <code>
 * $one = false;
 * $two = false;
 * $three = true;
 *
 * $result = $one && $two || $three;
 *
 * $result3 = $one && !$two xor $three;
 *
 *
 * if (
 *     $result && !$result3
 *     || !$result && $result3
 * ) {}
 * </code>
 *
 * @author    Tim Duesterhus <duesterhus@woltlab.com>
 * @copyright 2021 WoltLab GmbH.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class MixedBooleanOperatorSniff implements Sniff
{

    /**
     * Array of tokens this test searches for to find either a boolean
     * operator or the start of the current (sub-)expression. Used for
     * performance optimization purposes.
     *
     * @var array<int|string>
     */
    private $searchTargets = [];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        $this->searchTargets  = Tokens::$booleanOperators;
        $this->searchTargets += Tokens::$blockOpeners;
        $this->searchTargets[\T_INLINE_THEN] = \T_INLINE_THEN;
        $this->searchTargets[\T_INLINE_ELSE] = \T_INLINE_ELSE;

        return Tokens::$booleanOperators;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $start = $phpcsFile->findStartOfStatement($stackPtr);

        $previous = $phpcsFile->findPrevious(
            $this->searchTargets,
            ($stackPtr - 1),
            $start,
            false,
            null,
            true
        );

        if ($previous === false) {
            // No token found.
            return;
        }

        if ($tokens[$previous]['code'] === $tokens[$stackPtr]['code']) {
            // Identical operator found.
            return;
        }

        if (\in_array($tokens[$previous]['code'], [\T_INLINE_THEN, \T_INLINE_ELSE], true) === true) {
            // Beginning of the expression found for the ternary conditional operator.
            return;
        }

        if (isset(Tokens::$blockOpeners[$tokens[$previous]['code']]) === true) {
            // Beginning of the expression found for a block opener. Needed to
            // correctly handle match arms.
            return;
        }

        // We found a mismatching operator, thus we must report the error.
        $error  = 'Mixing different binary boolean operators within an expression';
        $error .= ' without using parentheses to clarify precedence is not allowed.';
        $phpcsFile->addError($error, $stackPtr, 'MissingParentheses');

    }//end process()


}//end class
