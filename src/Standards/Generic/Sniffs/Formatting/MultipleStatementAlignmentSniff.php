<?php
/**
 * Checks alignment of assignments.
 *
 * If there are multiple adjacent assignments, it will check that the equals signs of
 * each assignment are aligned. It will display a warning to advise that the signs should be aligned.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class MultipleStatementAlignmentSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var boolean
     */
    public $error = false;

    /**
     * The maximum amount of padding before the alignment is ignored.
     *
     * If the amount of padding required to align this assignment with the
     * surrounding assignments exceeds this number, the assignment will be
     * ignored and no errors or warnings will be thrown.
     *
     * @var integer
     */
    public $maxPadding = 1000;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $tokens = Tokens::$assignmentTokens;
        unset($tokens[T_DOUBLE_ARROW]);
        return $tokens;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore assignments used in a condition, like an IF or FOR.
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $start => $end) {
                if (isset($tokens[$start]['parenthesis_owner']) === true) {
                    return;
                }
            }
        }

        $lastAssign = $this->checkAlignment($phpcsFile, $stackPtr);
        return ($lastAssign + 1);

    }//end process()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     * @param int                         $end       The token where checking should end.
     *                                               If NULL, the entire file will be checked.
     *
     * @return int
     */
    public function checkAlignment($phpcsFile, $stackPtr, $end=null)
    {
        $tokens = $phpcsFile->getTokens();

        $assignments = [];
        $prevAssign  = null;
        $lastLine    = $tokens[$stackPtr]['line'];
        $maxPadding  = null;
        $stopped     = null;
        $lastCode    = $stackPtr;
        $lastSemi    = null;
        $arrayEnd    = null;

        if ($end === null) {
            $end = $phpcsFile->numTokens;
        }

        $find = Tokens::$assignmentTokens;
        unset($find[T_DOUBLE_ARROW]);

        $scopes = Tokens::$scopeOpeners;
        unset($scopes[T_CLOSURE]);
        unset($scopes[T_ANON_CLASS]);
        unset($scopes[T_OBJECT]);

        for ($assign = $stackPtr; $assign < $end; $assign++) {
            if ($tokens[$assign]['level'] < $tokens[$stackPtr]['level']) {
                // Statement is in a different context, so the block is over.
                break;
            }

            if (isset($scopes[$tokens[$assign]['code']]) === true
                && isset($tokens[$assign]['scope_opener']) === true
                && $tokens[$assign]['level'] === $tokens[$stackPtr]['level']
            ) {
                break;
            }

            if ($assign === $arrayEnd) {
                $arrayEnd = null;
            }

            if (isset($find[$tokens[$assign]['code']]) === false) {
                // A blank line indicates that the assignment block has ended.
                if (isset(Tokens::$emptyTokens[$tokens[$assign]['code']]) === false
                    && ($tokens[$assign]['line'] - $tokens[$lastCode]['line']) > 1
                    && $tokens[$assign]['level'] === $tokens[$stackPtr]['level']
                    && $arrayEnd === null
                ) {
                    break;
                }

                if ($tokens[$assign]['code'] === T_CLOSE_TAG) {
                    // Breaking out of PHP ends the assignment block.
                    break;
                }

                if ($tokens[$assign]['code'] === T_OPEN_SHORT_ARRAY
                    && isset($tokens[$assign]['bracket_closer']) === true
                ) {
                    $arrayEnd = $tokens[$assign]['bracket_closer'];
                }

                if ($tokens[$assign]['code'] === T_ARRAY
                    && isset($tokens[$assign]['parenthesis_opener']) === true
                    && isset($tokens[$tokens[$assign]['parenthesis_opener']]['parenthesis_closer']) === true
                ) {
                    $arrayEnd = $tokens[$tokens[$assign]['parenthesis_opener']]['parenthesis_closer'];
                }

                if (isset(Tokens::$emptyTokens[$tokens[$assign]['code']]) === false) {
                    $lastCode = $assign;

                    if ($tokens[$assign]['code'] === T_SEMICOLON) {
                        if ($tokens[$assign]['conditions'] === $tokens[$stackPtr]['conditions']) {
                            if ($lastSemi !== null && $prevAssign !== null && $lastSemi > $prevAssign) {
                                // This statement did not have an assignment operator in it.
                                break;
                            } else {
                                $lastSemi = $assign;
                            }
                        } else if ($tokens[$assign]['level'] < $tokens[$stackPtr]['level']) {
                            // Statement is in a different context, so the block is over.
                            break;
                        }
                    }
                }//end if

                continue;
            } else if ($assign !== $stackPtr && $tokens[$assign]['line'] === $lastLine) {
                // Skip multiple assignments on the same line. We only need to
                // try and align the first assignment.
                continue;
            }//end if

            if ($assign !== $stackPtr) {
                if ($tokens[$assign]['level'] > $tokens[$stackPtr]['level']) {
                    // Has to be nested inside the same conditions as the first assignment.
                    // We've gone one level down, so process this new block.
                    $assign   = $this->checkAlignment($phpcsFile, $assign);
                    $lastCode = $assign;
                    continue;
                } else if ($tokens[$assign]['level'] < $tokens[$stackPtr]['level']) {
                    // We've gone one level up, so the block we are processing is done.
                    break;
                } else if ($arrayEnd !== null) {
                    // Assignments inside arrays are not part of
                    // the original block, so process this new block.
                    $assign   = ($this->checkAlignment($phpcsFile, $assign, $arrayEnd) - 1);
                    $arrayEnd = null;
                    $lastCode = $assign;
                    continue;
                }

                // Make sure it is not assigned inside a condition (eg. IF, FOR).
                if (isset($tokens[$assign]['nested_parenthesis']) === true) {
                    foreach ($tokens[$assign]['nested_parenthesis'] as $start => $end) {
                        if (isset($tokens[$start]['parenthesis_owner']) === true) {
                            break(2);
                        }
                    }
                }
            }//end if

            $var = $phpcsFile->findPrevious(
                Tokens::$emptyTokens,
                ($assign - 1),
                null,
                true
            );

            // Make sure we wouldn't break our max padding length if we
            // aligned with this statement, or they wouldn't break the max
            // padding length if they aligned with us.
            $varEnd    = $tokens[($var + 1)]['column'];
            $assignLen = $tokens[$assign]['length'];
            if ($assign !== $stackPtr) {
                if ($prevAssign === null) {
                    // Processing an inner block but no assignments found.
                    break;
                }

                if (($varEnd + 1) > $assignments[$prevAssign]['assign_col']) {
                    $padding      = 1;
                    $assignColumn = ($varEnd + 1);
                } else {
                    $padding = ($assignments[$prevAssign]['assign_col'] - $varEnd + $assignments[$prevAssign]['assign_len'] - $assignLen);
                    if ($padding <= 0) {
                        $padding = 1;
                    }

                    if ($padding > $this->maxPadding) {
                        $stopped = $assign;
                        break;
                    }

                    $assignColumn = ($varEnd + $padding);
                }//end if

                if (($assignColumn + $assignLen) > ($assignments[$maxPadding]['assign_col'] + $assignments[$maxPadding]['assign_len'])) {
                    $newPadding = ($varEnd - $assignments[$maxPadding]['var_end'] + $assignLen - $assignments[$maxPadding]['assign_len'] + 1);
                    if ($newPadding > $this->maxPadding) {
                        $stopped = $assign;
                        break;
                    } else {
                        // New alignment settings for previous assignments.
                        foreach ($assignments as $i => $data) {
                            if ($i === $assign) {
                                break;
                            }

                            $newPadding = ($varEnd - $data['var_end'] + $assignLen - $data['assign_len'] + 1);
                            $assignments[$i]['expected']   = $newPadding;
                            $assignments[$i]['assign_col'] = ($data['var_end'] + $newPadding);
                        }

                        $padding      = 1;
                        $assignColumn = ($varEnd + 1);
                    }
                } else if ($padding > $assignments[$maxPadding]['expected']) {
                    $maxPadding = $assign;
                }//end if
            } else {
                $padding      = 1;
                $assignColumn = ($varEnd + 1);
                $maxPadding   = $assign;
            }//end if

            $found = 0;
            if ($tokens[($var + 1)]['code'] === T_WHITESPACE) {
                $found = $tokens[($var + 1)]['length'];
                if ($found === 0) {
                    // This means a newline was found.
                    $found = 1;
                }
            }

            $assignments[$assign] = [
                'var_end'    => $varEnd,
                'assign_len' => $assignLen,
                'assign_col' => $assignColumn,
                'expected'   => $padding,
                'found'      => $found,
            ];

            $lastLine   = $tokens[$assign]['line'];
            $prevAssign = $assign;
        }//end for

        if (empty($assignments) === true) {
            return $stackPtr;
        }

        $numAssignments = count($assignments);

        $errorGenerated = false;
        foreach ($assignments as $assignment => $data) {
            if ($data['found'] === $data['expected']) {
                continue;
            }

            $expectedText = $data['expected'].' space';
            if ($data['expected'] !== 1) {
                $expectedText .= 's';
            }

            if ($data['found'] === null) {
                $foundText = 'a new line';
            } else {
                $foundText = $data['found'].' space';
                if ($data['found'] !== 1) {
                    $foundText .= 's';
                }
            }

            if ($numAssignments === 1) {
                $type  = 'Incorrect';
                $error = 'Equals sign not aligned correctly; expected %s but found %s';
            } else {
                $type  = 'NotSame';
                $error = 'Equals sign not aligned with surrounding assignments; expected %s but found %s';
            }

            $errorData = [
                $expectedText,
                $foundText,
            ];

            if ($this->error === true) {
                $fix = $phpcsFile->addFixableError($error, $assignment, $type, $errorData);
            } else {
                $fix = $phpcsFile->addFixableWarning($error, $assignment, $type.'Warning', $errorData);
            }

            $errorGenerated = true;

            if ($fix === true && $data['found'] !== null) {
                $newContent = str_repeat(' ', $data['expected']);
                if ($data['found'] === 0) {
                    $phpcsFile->fixer->addContentBefore($assignment, $newContent);
                } else {
                    $phpcsFile->fixer->replaceToken(($assignment - 1), $newContent);
                }
            }
        }//end foreach

        if ($numAssignments > 1) {
            if ($errorGenerated === true) {
                $phpcsFile->recordMetric($stackPtr, 'Adjacent assignments aligned', 'no');
            } else {
                $phpcsFile->recordMetric($stackPtr, 'Adjacent assignments aligned', 'yes');
            }
        }

        if ($stopped !== null) {
            return $this->checkAlignment($phpcsFile, $stopped);
        } else {
            return $assign;
        }

    }//end checkAlignment()


}//end class
