<?php
/**
 * Ensure a single space before, and a newline after, the class opening brace
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ClassDefinitionOpeningBraceSpaceSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_OPEN_CURLY_BRACKET];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens            = $phpcsFile->getTokens();
        $prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);

        if ($prevNonWhitespace !== false) {
            $length = 0;
            if ($tokens[$stackPtr]['line'] !== $tokens[$prevNonWhitespace]['line']) {
                $length = 'newline';
            } else if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
                if (strpos($tokens[($stackPtr - 1)]['content'], "\t") !== false) {
                    $length = 'tab';
                } else {
                    $length = $tokens[($stackPtr - 1)]['length'];
                }
            }

            if ($length === 0) {
                $error = 'Expected 1 space before opening brace of class definition; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoneBefore');
                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
                }
            } else if ($length !== 1) {
                $error = 'Expected 1 space before opening brace of class definition; %s found';
                $data  = [$length];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Before', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();

                    for ($i = ($stackPtr - 1); $i > $prevNonWhitespace; $i--) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            }//end if
        }//end if

        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($nextNonEmpty === false) {
            return;
        }

        if ($tokens[$nextNonEmpty]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Opening brace should be the last content on the line';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ContentBefore');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($stackPtr);

                // Remove potentially left over trailing whitespace.
                if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        } else {
            if (isset($tokens[$stackPtr]['bracket_closer']) === false) {
                // Syntax error or live coding, bow out.
                return;
            }

            // Check for nested class definitions.
            $found = $phpcsFile->findNext(
                T_OPEN_CURLY_BRACKET,
                ($stackPtr + 1),
                $tokens[$stackPtr]['bracket_closer']
            );

            if ($found === false) {
                // Not nested.
                return;
            }

            $lastOnLine = $stackPtr;
            for ($lastOnLine; $lastOnLine < $tokens[$stackPtr]['bracket_closer']; $lastOnLine++) {
                if ($tokens[$lastOnLine]['line'] !== $tokens[($lastOnLine + 1)]['line']) {
                    break;
                }
            }

            $nextNonWhiteSpace = $phpcsFile->findNext(T_WHITESPACE, ($lastOnLine + 1), null, true);
            if ($nextNonWhiteSpace === false) {
                return;
            }

            $foundLines = ($tokens[$nextNonWhiteSpace]['line'] - $tokens[$stackPtr]['line'] - 1);
            if ($foundLines !== 1) {
                $error = 'Expected 1 blank line after opening brace of nesting class definition; %s found';
                $data  = [max(0, $foundLines)];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'AfterNesting', $data);

                if ($fix === true) {
                    $firstOnNextLine = $nextNonWhiteSpace;
                    while ($tokens[$firstOnNextLine]['column'] !== 1) {
                        --$firstOnNextLine;
                    }

                    if ($found < 0) {
                        // First statement on same line as the opening brace.
                        $phpcsFile->fixer->addContentBefore($nextNonWhiteSpace, $phpcsFile->eolChar.$phpcsFile->eolChar);
                    } else if ($found === 0) {
                        // Next statement on next line, no blank line.
                        $phpcsFile->fixer->addNewlineBefore($firstOnNextLine);
                    } else {
                        // Too many blank lines.
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($firstOnNextLine - 1); $i > $stackPtr; $i--) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addContentBefore($firstOnNextLine, $phpcsFile->eolChar.$phpcsFile->eolChar);
                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if
            }//end if
        }//end if

    }//end process()


}//end class
