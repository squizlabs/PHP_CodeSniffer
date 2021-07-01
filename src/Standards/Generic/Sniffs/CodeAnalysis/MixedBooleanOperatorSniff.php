<?php
/**
 * Detects mixed '&&' and '||' within a single expression, without making
 * precedence explicit using parentheses.
 *
 * <code>
 * $var = true && true || true;
 * </code>
 *
 * @author    Tim Duesterhus <duesterhus@woltlab.com>
 * @copyright 2021 WoltLab GmbH.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MixedBooleanOperatorSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_BOOLEAN_OR,
            T_BOOLEAN_AND,
        ];

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
        $token  = $tokens[$stackPtr];

        $start = $phpcsFile->findStartOfStatement($stackPtr);

        if ($token['code'] === T_BOOLEAN_AND) {
            $search = T_BOOLEAN_OR;
        } else if ($token['code'] === T_BOOLEAN_OR) {
            $search = T_BOOLEAN_AND;
        } else {
            throw new \LogicException('Unreachable');
        }

        while (true) {
            $previous = $phpcsFile->findPrevious(
                [
                    $search,
                    T_OPEN_PARENTHESIS,
                    T_OPEN_SQUARE_BRACKET,
                    T_CLOSE_PARENTHESIS,
                    T_CLOSE_SQUARE_BRACKET,
                ],
                $stackPtr,
                $start
            );

            if ($previous === false) {
                break;
            }

            if ($tokens[$previous]['code'] === T_OPEN_PARENTHESIS
                || $tokens[$previous]['code'] === T_OPEN_SQUARE_BRACKET
            ) {
                // We halt if we reach the opening parens / bracket of the boolean operator.
                return;
            } else if ($tokens[$previous]['code'] === T_CLOSE_PARENTHESIS) {
                // We skip the content of nested parens.
                $stackPtr = ($tokens[$previous]['parenthesis_opener'] - 1);
            } else if ($tokens[$previous]['code'] === T_CLOSE_SQUARE_BRACKET) {
                // We skip the content of nested brackets.
                $stackPtr = ($tokens[$previous]['bracket_opener'] - 1);
            } else if ($tokens[$previous]['code'] === $search) {
                // We reached a mismatching operator, thus we must report the error.
                $error = "Mixed '&&' and '||' within an expression without using parentheses.";
                $phpcsFile->addError($error, $stackPtr, 'MissingParentheses', []);
                return;
            } else {
                throw new \LogicException('Unreachable');
            }
        }//end while

    }//end process()


}//end class
