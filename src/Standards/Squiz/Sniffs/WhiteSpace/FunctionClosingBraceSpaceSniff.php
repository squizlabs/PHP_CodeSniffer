<?php
/**
 * Checks that there is one empty line before the closing brace of a function.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class FunctionClosingBraceSpaceSniff implements Sniff
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
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
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

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            // Probably an interface method.
            return;
        }

        $closeBrace  = $tokens[$stackPtr]['scope_closer'];
        $prevContent = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBrace - 1), null, true);

        // Special case for empty JS functions.
        if ($phpcsFile->tokenizerType === 'JS' && $prevContent === $tokens[$stackPtr]['scope_opener']) {
            // In this case, the opening and closing brace must be
            // right next to each other.
            if ($tokens[$stackPtr]['scope_closer'] !== ($tokens[$stackPtr]['scope_opener'] + 1)) {
                $error = 'The opening and closing braces of empty functions must be directly next to each other; e.g., function () {}';
                $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'SpacingBetween');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($tokens[$stackPtr]['scope_opener'] + 1); $i < $closeBrace; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }

            return;
        }

        $nestedFunction = false;
        if ($phpcsFile->hasCondition($stackPtr, [T_FUNCTION, T_CLOSURE]) === true
            || isset($tokens[$stackPtr]['nested_parenthesis']) === true
        ) {
            $nestedFunction = true;
        }

        $braceLine = $tokens[$closeBrace]['line'];
        $prevLine  = $tokens[$prevContent]['line'];
        $found     = ($braceLine - $prevLine - 1);

        if ($nestedFunction === true) {
            if ($found < 0) {
                $error = 'Closing brace of nested function must be on a new line';
                $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'ContentBeforeClose');
                if ($fix === true) {
                    $phpcsFile->fixer->addNewlineBefore($closeBrace);
                }
            } else if ($found > 0) {
                $error = 'Expected 0 blank lines before closing brace of nested function; %s found';
                $data  = [$found];
                $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'SpacingBeforeNestedClose', $data);

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $changeMade = false;
                    for ($i = ($prevContent + 1); $i < $closeBrace; $i++) {
                        // Try and maintain indentation.
                        if ($tokens[$i]['line'] === ($braceLine - 1)) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                        $changeMade = true;
                    }

                    // Special case for when the last content contains the newline
                    // token as well, like with a comment.
                    if ($changeMade === false) {
                        $phpcsFile->fixer->replaceToken(($prevContent + 1), '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }//end if
            }//end if
        } else {
            if ($found !== 1) {
                if ($found < 0) {
                    $found = 0;
                }

                $error = 'Expected 1 blank line before closing function brace; %s found';
                $data  = [$found];
                $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'SpacingBeforeClose', $data);

                if ($fix === true) {
                    if ($found > 1) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prevContent + 1); $i < ($closeBrace - 1); $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->replaceToken($i, $phpcsFile->eolChar);
                        $phpcsFile->fixer->endChangeset();
                    } else {
                        // Try and maintain indentation.
                        if ($tokens[($closeBrace - 1)]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->addNewlineBefore($closeBrace - 1);
                        } else {
                            $phpcsFile->fixer->addNewlineBefore($closeBrace);
                        }
                    }
                }
            }//end if
        }//end if

    }//end process()


}//end class
