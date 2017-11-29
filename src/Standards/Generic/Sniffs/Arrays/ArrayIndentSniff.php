<?php
/**
 * Ensures that array are indented one tab stop.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays;

use PHP_CodeSniffer\Sniffs\AbstractArraySniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ArrayIndentSniff extends AbstractArraySniff
{

    /**
     * The number of spaces each array key should be indented.
     *
     * @var integer
     */
    public $indent = 4;


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
    public function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {

    }//end processSingleLineArray()


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
    public function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        $tokens = $phpcsFile->getTokens();

        $first          = $phpcsFile->findFirstOnLine(T_WHITESPACE, $stackPtr, true);
        $expectedIndent = ($tokens[$first]['column'] - 1 + $this->indent);

        foreach ($indices as $index) {
            if (isset($index['index_start']) === true) {
                $start = $index['index_start'];
            } else {
                $start = $index['value_start'];
            }

            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($start - 1), null, true);
            if ($tokens[$prev]['line'] === $tokens[$start]['line']) {
                // This index isn't the only content on the line
                // so we can't check indent rules.
                continue;
            }

            $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $start, true);

            $foundIndent = ($tokens[$first]['column'] - 1);
            if ($foundIndent === $expectedIndent) {
                continue;
            }

            $error = 'Array key not indented correctly; expected %s spaces but found %s';
            $data  = [
                $expectedIndent,
                $foundIndent,
            ];
            $fix   = $phpcsFile->addFixableError($error, $first, 'KeyIncorrect', $data);
            if ($fix === false) {
                continue;
            }

            $padding = str_repeat(' ', $expectedIndent);
            if ($foundIndent === 0) {
                $phpcsFile->fixer->addContentBefore($first, $padding);
            } else {
                $phpcsFile->fixer->replaceToken(($first - 1), $padding);
            }
        }//end foreach

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($arrayEnd - 1), null, true);
        if ($tokens[$prev]['line'] === $tokens[$arrayEnd]['line']) {
            $error = 'Closing brace of array declaration must be on a new line';
            $fix   = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceNotNewLine');
            if ($fix === true) {
                $padding = $phpcsFile->eolChar.str_repeat(' ', $expectedIndent);
                $phpcsFile->fixer->addContentBefore($arrayEnd, $padding);
            }

            return;
        }

        // The close brace must be indented one stop less.
        $expectedIndent -= $this->indent;
        $foundIndent     = ($tokens[$arrayEnd]['column'] - 1);
        if ($foundIndent === $expectedIndent) {
            return;
        }

        $error = 'Array close brace not indented correctly; expected %s spaces but found %s';
        $data  = [
            $expectedIndent,
            $foundIndent,
        ];
        $fix   = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceIncorrect', $data);
        if ($fix === false) {
            return;
        }

        $padding = str_repeat(' ', $expectedIndent);
        if ($foundIndent === 0) {
            $phpcsFile->fixer->addContentBefore($arrayEnd, $padding);
        } else {
            $phpcsFile->fixer->replaceToken(($arrayEnd - 1), $padding);
        }

    }//end processMultiLineArray()


}//end class
