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
use PHP_CodeSniffer\Util\Sniffs\PassedParameters;
use PHP_CodeSniffer\Util\Sniffs\TokenIs;
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

        if ($tokens[$stackPtr]['code'] === T_OPEN_SHORT_ARRAY
            && TokenIs::isShortList($phpcsFile, $stackPtr) === true
        ) {
            // No need to examine nested subs of this short list.
            return $tokens[$stackPtr]['bracket_closer'];
        }

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
            $lastArrayToken = $lastContent;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Array end comma', 'no');
            $lastArrayToken = $arrayEnd;
        }

        if ($tokens[$stackPtr]['code'] === T_ARRAY) {
            $lastToken = $tokens[$stackPtr]['parenthesis_opener'];
        } else {
            $lastToken = $stackPtr;
        }

        $arrayItems = PassedParameters::getParameters($phpcsFile, $stackPtr);
        foreach ($arrayItems as $key => $item) {
            $firstNonEmpty = $phpcsFile->findNext(
                Tokens::$emptyTokens,
                ($item['start'] + 1),
                ($item['end'] + 1),
                true
            );
            $doubleArrow   = PassedParameters::getDoubleArrowPosition($phpcsFile, $item['start'], $item['end']);

            if ($doubleArrow === false) {
                // This array item does not have an index.
                $arrayItems[$key]['value_start'] = $firstNonEmpty;
            } else {
                $indexEnd   = $phpcsFile->findPrevious(T_WHITESPACE, ($doubleArrow - 1), $item['start'], true);
                $valueStart = $phpcsFile->findNext(
                    Tokens::$emptyTokens,
                    ($doubleArrow + 1),
                    ($item['end'] + 1),
                    true
                );

                $arrayItems[$key]['index_start'] = $firstNonEmpty;
                $arrayItems[$key]['index_end']   = $indexEnd;
                $arrayItems[$key]['arrow']       = $doubleArrow;
                $arrayItems[$key]['value_start'] = $valueStart;
            }
        }//end foreach

        if ($tokens[$arrayStart]['line'] === $tokens[$arrayEnd]['line']) {
            $this->processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $arrayItems);
        } else {
            $this->processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $arrayItems);
        }

    }//end process()


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
