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

        $preppedIndices = $this->prepareIndices($phpcsFile, $indices, $arrayEnd);

        foreach ($preppedIndices as $index) {
            $start = $index['start'];
            $end   = $index['end'];

            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($start - 1), null, true);
            if ($tokens[$prev]['line'] === $tokens[$start]['line']) {
                // This index isn't the only content on the line
                // so we can't check indent rules.
                continue;
            }

            $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $start, true);

            while ($first !== false) {
                $this->processIndex($phpcsFile, $first, $expectedIndent);
                $first = $this->findFirstIndexOnNextLine($phpcsFile, $first, $end);
            }//end while
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


    /**
     * Prepares the indices by calculating the start and end of each index.
     *
     * The prepared indices will contain the tokens that define the start and end
     * of each index.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param array                       $indices   An array of token positions for the array keys,
     *                                               double arrows, and values.
     * @param int                         $arrayEnd  The token that ends the array definition.
     *
     * @return array
     */
    protected function prepareIndices($phpcsFile, $indices, $arrayEnd)
    {
        $lastKey         = null;
        $preparedIndices = [];

        foreach ($indices as $key => $index) {
            if (isset($index['index_start']) === true) {
                $start = $index['index_start'];
            } else {
                $start = $index['value_start'];
            }

            $preparedIndices[$key]['start'] = $start;

            if ($lastKey !== null) {
                $end = $phpcsFile->findPrevious(T_COMMA, $start, $preparedIndices[$lastKey]['start']);

                $preparedIndices[$lastKey]['end'] = $end;
            }

            $lastKey = $key;
        }

        if ($lastKey === null) {
            return $preparedIndices;
        }

        $commaEnd = $phpcsFile->findPrevious(T_COMMA, ($arrayEnd - 1), $preparedIndices[$lastKey]['start']);
        if ($commaEnd !== false) {
            $preparedIndices[$lastKey]['end'] = $commaEnd;

            return $preparedIndices;
        }

        $whitespaceEnd = $phpcsFile->findPrevious(T_WHITESPACE, ($arrayEnd - 1), null, true);

        $preparedIndices[$lastKey]['end'] = $whitespaceEnd;

        return $preparedIndices;

    }//end prepareIndices()


    /**
     * Processes an array index from start to end.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile      The current file being checked.
     * @param int                         $start          The token to start processing from.
     * @param int                         $expectedIndent The number of spaces each line should be tabbed.
     *
     * @return void
     */
    protected function processIndex($phpcsFile, $start, $expectedIndent)
    {
        $tokens = $phpcsFile->getTokens();

        $foundIndent = ($tokens[$start]['column'] - 1);
        if ($foundIndent === $expectedIndent) {
            return;
        }

        $error = 'Array index not indented correctly; expected %s spaces but found %s';
        $data  = [
            $expectedIndent,
            $foundIndent,
        ];
        $fix   = $phpcsFile->addFixableError($error, $start, 'KeyIncorrect', $data);
        if ($fix === false) {
            return;
        }

        $padding = str_repeat(' ', $expectedIndent);
        if ($foundIndent === 0) {
            $phpcsFile->fixer->addContentBefore($start, $padding);

            return;
        }

        $phpcsFile->fixer->replaceToken(($start - 1), $padding);

    }//end processIndex()


    /**
     * Gets the first non-whitespace index of the next viable line.
     *
     * Skips over any scopes or arrays found while looking for the next line.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $start     The token to start finding from.
     * @param int                         $end       The token to stop finding at.
     *
     * @return int|bool
     */
    protected function findFirstIndexOnNextLine($phpcsFile, $start, $end)
    {
        $tokens = $phpcsFile->getTokens();

        $prevToken        = null;
        $viableStart      = $start;
        $invalidTokenList = (Tokens::$scopeOpeners + Tokens::$blockOpeners + [T_ARRAY => T_ARRAY, T_OPEN_SHORT_ARRAY => T_OPEN_SHORT_ARRAY]);

        while ($prevToken !== $viableStart) {
            $prevToken = $viableStart;

            $invalidToken = $phpcsFile->findNext($invalidTokenList, $viableStart, $end);
            if ($invalidToken === false
                || $tokens[$viableStart]['line'] !== $tokens[$invalidToken]['line']
            ) {
                continue;
            }

            $keys   = array_keys($tokens[$invalidToken]);
            $result = preg_grep('/^\w+\_closer$/', $keys);
            if ($result === []) {
                $viableStart = ($invalidToken + 1);
            } else {
                $viableStart = $tokens[$invalidToken][reset($result)];
            }
        }//end while

        if ($prevToken === null) {
            $nextLineStart = $start;
        } else {
            $nextLineStart = $prevToken;
        }

        $newline = $phpcsFile->findNext(T_WHITESPACE, $nextLineStart, $end, false, PHP_EOL);
        if ($newline === false
            || $tokens[($newline + 1)]['line'] > $tokens[$end]['line']
        ) {
            return false;
        }

        $nextNewline = $phpcsFile->findNext(T_WHITESPACE, ($newline + 1), $end, false, PHP_EOL);
        if ($nextNewline === false) {
            $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $end, true);
        } else {
            $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $nextNewline, true);
        }

        if (in_array($tokens[$first]['code'], [T_ARRAY, T_OPEN_SHORT_ARRAY]) !== true) {
            return false;
        }

        return $first;

    }//end findFirstIndexOnNextLine()


}//end class
