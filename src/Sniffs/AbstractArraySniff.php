<?php
/**
 * Processes single and multi-line arrays.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

abstract class AbstractArraySniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    final public function register()
    {
        return [
            T_ARRAY,
            T_OPEN_SHORT_ARRAY,
        ];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_ARRAY) {
            $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'no');

            $arrayStart = $tokens[$stackPtr]['parenthesis_opener'];
            if (isset($tokens[$arrayStart]['parenthesis_closer']) === false) {
                // Incomplete array.
                return;
            }

            $arrayEnd = $tokens[$arrayStart]['parenthesis_closer'];
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'yes');
            $arrayStart = $stackPtr;
            $arrayEnd   = $tokens[$stackPtr]['bracket_closer'];
        }

        $lastContent = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($arrayEnd - 1), null, true);
        if ($tokens[$lastContent]['code'] === T_COMMA) {
            // Last array item ends with a comma.
            $phpcsFile->recordMetric($stackPtr, 'Array end comma', 'yes');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Array end comma', 'no');
        }

        $indices = [];

        $current = $arrayStart;
        while (($next = $phpcsFile->findNext(Tokens::$emptyTokens, ($current + 1), $arrayEnd, true)) !== false) {
            $end = $this->getNext($phpcsFile, $next, $arrayEnd);

            if ($tokens[$end]['code'] === T_DOUBLE_ARROW) {
                $indexEnd   = $phpcsFile->findPrevious(T_WHITESPACE, ($end - 1), null, true);
                $valueStart = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), null, true);

                $indices[] = [
                    'index_start' => $next,
                    'index_end'   => $indexEnd,
                    'arrow'       => $end,
                    'value_start' => $valueStart,
                ];
            } else {
                $valueStart = $next;
                $indices[]  = ['value_start' => $valueStart];
            }

            $current = $this->getNext($phpcsFile, $valueStart, $arrayEnd);
        }

        if ($tokens[$arrayStart]['line'] === $tokens[$arrayEnd]['line']) {
            $this->processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices);
        } else {
            $this->processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices);
        }

    }//end process()


    /**
     * Find next separator in array - either: comma or double arrow.
     *
     * @param File $phpcsFile The current file being checked.
     * @param int  $ptr       The position of current token.
     * @param int  $arrayEnd  The token that ends the array definition.
     *
     * @return int
     */
    private function getNext(File $phpcsFile, $ptr, $arrayEnd)
    {
        $tokens = $phpcsFile->getTokens();

        while ($ptr < $arrayEnd) {
            if (isset($tokens[$ptr]['scope_closer']) === true) {
                $ptr = $tokens[$ptr]['scope_closer'];
            } else if (isset($tokens[$ptr]['parenthesis_closer']) === true) {
                $ptr = $tokens[$ptr]['parenthesis_closer'];
            } else if (isset($tokens[$ptr]['bracket_closer']) === true) {
                $ptr = $tokens[$ptr]['bracket_closer'];
            }

            if ($tokens[$ptr]['code'] === T_COMMA
                || $tokens[$ptr]['code'] === T_DOUBLE_ARROW
            ) {
                return $ptr;
            }

            ++$ptr;
        }

        return $ptr;

    }//end getNext()


    /**
     * Processes a single-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     * @param array                       $indices    An array of token positions for the array keys,
     *                                                double arrows, and values.
     *
     * @return void
     */
    abstract protected function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices);


    /**
     * Processes a multi-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     * @param array                       $indices    An array of token positions for the array keys,
     *                                                double arrows, and values.
     *
     * @return void
     */
    abstract protected function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices);


}//end class
