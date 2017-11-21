<?php
/**
 * Processes single and mutli-line arrays.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Sniffs;

use PHP_CodeSniffer\Sniffs\Sniff;
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

        $keyUsed = false;
        $indices = [];

        for ($checkToken = ($stackPtr + 1); $checkToken <= $lastArrayToken; $checkToken++) {
            // Skip bracketed statements, like function calls.
            if ($tokens[$checkToken]['code'] === T_OPEN_PARENTHESIS
                && (isset($tokens[$checkToken]['parenthesis_owner']) === false
                || $tokens[$checkToken]['parenthesis_owner'] !== $stackPtr)
            ) {
                $checkToken = $tokens[$checkToken]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$checkToken]['code'] === T_ARRAY
                || $tokens[$checkToken]['code'] === T_OPEN_SHORT_ARRAY
                || $tokens[$checkToken]['code'] === T_CLOSURE
            ) {
                // Let subsequent calls of this test handle nested arrays.
                if ($tokens[$lastToken]['code'] !== T_DOUBLE_ARROW) {
                    $indices[] = ['value_start' => $checkToken];
                    $lastToken = $checkToken;
                }

                if ($tokens[$checkToken]['code'] === T_ARRAY) {
                    $checkToken = $tokens[$tokens[$checkToken]['parenthesis_opener']]['parenthesis_closer'];
                } else if ($tokens[$checkToken]['code'] === T_OPEN_SHORT_ARRAY) {
                    $checkToken = $tokens[$checkToken]['bracket_closer'];
                } else {
                    // T_CLOSURE.
                    $checkToken = $tokens[$checkToken]['scope_closer'];
                }

                $checkToken = $phpcsFile->findNext(T_WHITESPACE, ($checkToken + 1), null, true);
                if ($tokens[$checkToken]['code'] !== T_COMMA) {
                    $checkToken--;
                } else {
                    $lastToken = $checkToken;
                }

                continue;
            }//end if

            if ($tokens[$checkToken]['code'] !== T_DOUBLE_ARROW
                && $tokens[$checkToken]['code'] !== T_COMMA
                && $checkToken !== $arrayEnd
            ) {
                continue;
            }

            if ($tokens[$checkToken]['code'] === T_COMMA
                || $checkToken === $arrayEnd
            ) {
                $stackPtrCount = 0;
                if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
                    $stackPtrCount = count($tokens[$stackPtr]['nested_parenthesis']);
                }

                $commaCount = 0;
                if (isset($tokens[$checkToken]['nested_parenthesis']) === true) {
                    $commaCount = count($tokens[$checkToken]['nested_parenthesis']);
                    if ($tokens[$stackPtr]['code'] === T_ARRAY) {
                        // Remove parenthesis that are used to define the array.
                        $commaCount--;
                    }
                }

                if ($commaCount > $stackPtrCount) {
                    // This comma is inside more parenthesis than the ARRAY keyword,
                    // so it is actually a comma used to do things like
                    // separate arguments in a function call.
                    continue;
                }

                if ($keyUsed === false) {
                    $valueContent = $phpcsFile->findNext(
                        Tokens::$emptyTokens,
                        ($lastToken + 1),
                        $checkToken,
                        true
                    );

                    $indices[] = ['value_start' => $valueContent];
                }

                $lastToken = $checkToken;
                $keyUsed   = false;
                continue;
            }//end if

            if ($tokens[$checkToken]['code'] === T_DOUBLE_ARROW) {
                $keyUsed = true;

                // Find the start of index that uses this double arrow.
                $indexEnd   = $phpcsFile->findPrevious(T_WHITESPACE, ($checkToken - 1), $arrayStart, true);
                $indexStart = $phpcsFile->findStartOfStatement($indexEnd);

                // Find the value of this index.
                $nextContent = $phpcsFile->findNext(
                    Tokens::$emptyTokens,
                    ($checkToken + 1),
                    $arrayEnd,
                    true
                );

                $indices[] = [
                    'index_start' => $indexStart,
                    'index_end'   => $indexEnd,
                    'arrow'       => $checkToken,
                    'value_start' => $nextContent,
                ];

                $lastToken = $checkToken;
            }//end if
        }//end for

        if ($tokens[$arrayStart]['line'] === $tokens[$arrayEnd]['line']) {
            $this->processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices);
        } else {
            $this->processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices);
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
