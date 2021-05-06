<?php
/**
 * Verifies that class members are spaced correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Tokens;

class MemberVarSpacingSniff extends AbstractVariableSniff
{

    /**
     * The number of blank lines between member vars.
     *
     * @var integer
     */
    public $spacing = 1;

    /**
     * The number of blank lines before the first member var.
     *
     * @var integer
     */
    public $spacingBeforeFirst = 1;


    /**
     * Processes the function tokens within the class.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached.
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $validPrefixes   = Tokens::$methodPrefixes;
        $validPrefixes[] = T_VAR;

        $startOfStatement = $phpcsFile->findPrevious($validPrefixes, ($stackPtr - 1), null, false, null, true);
        if ($startOfStatement === false) {
            return;
        }

        $endOfStatement = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1), null, false, null, true);

        $ignore   = $validPrefixes;
        $ignore[] = T_WHITESPACE;

        $start = $startOfStatement;
        $prev  = $phpcsFile->findPrevious($ignore, ($startOfStatement - 1), null, true);

        if ($tokens[$prev]['code'] === T_ATTRIBUTE_END) {
            do {
                if ($tokens[$start]['code'] === T_ATTRIBUTE_END) {
                    $nextContent = $tokens[$start]['attribute_opener'];
                } else {
                    $nextContent = $start;
                }

                if ($tokens[$prev]['line'] === $tokens[$nextContent]['line']) {
                    $error = 'Each attribute must be on a line by itself';
                    $fix   = $phpcsFile->addFixableError($error, $prev, 'NoNewLineBeforeAttribute');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();

                        /*
                         * Remove redundant whitespaces.
                         */

                        $cur = $prev;
                        while (($cur = $phpcsFile->findNext(T_WHITESPACE, ($cur + 1), $nextContent)) !== false) {
                            $phpcsFile->fixer->replaceToken($cur, '');
                        }

                        /*
                         * Fix indent.
                         */

                        $indent = '';
                        $cur    = $prev;

                        do {
                            $cur = $phpcsFile->findPrevious(T_WHITESPACE, ($cur - 1));
                            if ($tokens[$cur]['line'] !== $tokens[$prev]['line']) {
                                break;
                            }

                            $indent = str_repeat(' ', $tokens[$cur]['length']);
                        } while (true);

                        $phpcsFile->fixer->addContentBefore($nextContent, $indent);

                        $phpcsFile->fixer->addNewlineBefore($nextContent);
                        $phpcsFile->fixer->endChangeset();
                    }//end if
                }//end if

                $foundLines = ($tokens[$nextContent]['line'] - $tokens[$prev]['line'] - 1);
                if ($foundLines > 0) {
                    $error = 'Expected 0 blank lines after attribute; %s found';
                    $data  = [$foundLines];
                    $fix   = $phpcsFile->addFixableError($error, $prev, 'AfterAttribute', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();

                        for ($i = ($prev + 1); $i <= $nextContent; $i++) {
                            if ($tokens[$i]['line'] === $tokens[$nextContent]['line']) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                }

                $start = $prev;
                $prev  = $phpcsFile->findPrevious($ignore, ($tokens[$prev]['attribute_opener'] - 1), null, true);
            } while ($tokens[$prev]['code'] === T_ATTRIBUTE_END);
        }//end if

        if (isset(Tokens::$commentTokens[$tokens[$prev]['code']]) === true) {
            // Assume the comment belongs to the member var if it is on a line by itself.
            $prevContent = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prev - 1), null, true);
            if ($tokens[$prevContent]['line'] !== $tokens[$prev]['line']) {
                if ($tokens[$start]['code'] === T_ATTRIBUTE_END) {
                    $nextContent = $tokens[$start]['attribute_opener'];
                } else {
                    $nextContent = $start;
                }

                // Check the spacing, but then skip it.
                $foundLines = ($tokens[$nextContent]['line'] - $tokens[$prev]['line'] - 1);
                if ($foundLines > 0) {
                    $error = 'Expected 0 blank lines after member var comment; %s found';
                    $data  = [$foundLines];
                    $fix   = $phpcsFile->addFixableError($error, $prev, 'AfterComment', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        // Inline comments have the newline included in the content but
                        // docblock do not.
                        if ($tokens[$prev]['code'] === T_COMMENT) {
                            $phpcsFile->fixer->replaceToken($prev, rtrim($tokens[$prev]['content']));
                        }

                        for ($i = ($prev + 1); $i <= $nextContent; $i++) {
                            if ($tokens[$i]['line'] === $tokens[$start]['line']) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if

                $start = $prev;
            }//end if

            if ($tokens[$prevContent]['code'] === T_ATTRIBUTE_END) {
                $error = 'Member var comment must be before attributes';
                $fix   = $phpcsFile->addFixableError($error, $prev, 'AttributeAfterComment');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();

                    /*
                     * Fix indent.
                     */

                    $indent = '';
                    $cur    = $tokens[$prevContent]['attribute_opener'];

                    do {
                        $cur = $phpcsFile->findPrevious(T_WHITESPACE, ($cur - 1));

                        if ($tokens[$cur]['line'] !== $tokens[$tokens[$prevContent]['attribute_opener']]['line']) {
                            break;
                        }

                        $indent = str_repeat(' ', $tokens[$cur]['length']);
                    } while (true);

                    $phpcsFile->fixer->addContentBefore($tokens[$prevContent]['attribute_opener'], $indent);

                    $commentContent = '';
                    for ($i = $tokens[$prev]['comment_opener']; $i <= $prev; $i++) {
                        $commentContent .= $phpcsFile->fixer->getTokenContent($i);
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addNewlineBefore($tokens[$prevContent]['attribute_opener']);
                    $phpcsFile->fixer->addContentBefore($tokens[$prevContent]['attribute_opener'], $commentContent);

                    $phpcsFile->fixer->endChangeset();
                }//end if
            }//end if
        }//end if

        // There needs to be n blank lines before the var, not counting comments and attributes.
        if ($start === $startOfStatement) {
            // No comment found.
            $first = $phpcsFile->findFirstOnLine(Tokens::$emptyTokens, $start, true);
            if ($first === false) {
                $first = $start;
            }
        } else if ($tokens[$start]['code'] === T_ATTRIBUTE_END) {
            $first = $tokens[$start]['attribute_opener'];
        } else if ($tokens[$start]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            $first = $tokens[$start]['comment_opener'];
        } else {
            $first = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($start - 1), null, true);
            $first = $phpcsFile->findNext(Tokens::$commentTokens, ($first + 1));
        }

        // Determine if this is the first member var.
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($first - 1), null, true);
        if ($tokens[$prev]['code'] === T_CLOSE_CURLY_BRACKET
            && isset($tokens[$prev]['scope_condition']) === true
            && $tokens[$tokens[$prev]['scope_condition']]['code'] === T_FUNCTION
        ) {
            return;
        }

        if ($tokens[$prev]['code'] === T_OPEN_CURLY_BRACKET
            && isset(Tokens::$ooScopeTokens[$tokens[$tokens[$prev]['scope_condition']]['code']]) === true
        ) {
            $errorMsg  = 'Expected %s blank line(s) before first member var; %s found';
            $errorCode = 'FirstIncorrect';
            $spacing   = (int) $this->spacingBeforeFirst;
        } else {
            $errorMsg  = 'Expected %s blank line(s) before member var; %s found';
            $errorCode = 'Incorrect';
            $spacing   = (int) $this->spacing;
        }

        $foundLines = ($tokens[$first]['line'] - $tokens[$prev]['line'] - 1);

        if ($errorCode === 'FirstIncorrect') {
            $phpcsFile->recordMetric($stackPtr, 'Member var spacing before first', $foundLines);
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Member var spacing before', $foundLines);
        }

        if ($foundLines === $spacing) {
            if ($endOfStatement !== false) {
                return $endOfStatement;
            }

            return;
        }

        $data = [
            $spacing,
            $foundLines,
        ];

        $fix = $phpcsFile->addFixableError($errorMsg, $startOfStatement, $errorCode, $data);
        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = ($prev + 1); $i < $first; $i++) {
                if ($tokens[$i]['line'] === $tokens[$prev]['line']) {
                    continue;
                }

                if ($tokens[$i]['line'] === $tokens[$first]['line']) {
                    for ($x = 1; $x <= $spacing; $x++) {
                        $phpcsFile->fixer->addNewlineBefore($i);
                    }

                    break;
                }

                $phpcsFile->fixer->replaceToken($i, '');
            }

            $phpcsFile->fixer->endChangeset();
        }//end if

        if ($endOfStatement !== false) {
            return $endOfStatement;
        }

        return;

    }//end processMemberVar()


    /**
     * Processes normal variables.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */

    }//end processVariable()


    /**
     * Processes variables in double quoted strings.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */

    }//end processVariableInString()


}//end class
