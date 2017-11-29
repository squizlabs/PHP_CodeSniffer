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
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space before opening brace of class definition; 0 found';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoneBefore');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }
        } else {
            $content = $tokens[($stackPtr - 1)]['content'];
            if ($content !== ' ') {
                if ($tokens[($stackPtr - 1)]['line'] < $tokens[$stackPtr]['line']) {
                    $length = 'newline';
                } else {
                    $length = strlen($content);
                    if ($length === 1) {
                        $length = 'tab';
                    }
                }

                $error = 'Expected 1 space before opening brace of class definition; %s found';
                $data  = [$length];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Before', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                }
            }
        }//end if

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            return;
        }

        // Check for nested class definitions.
        $nested = false;
        $found  = $phpcsFile->findNext(
            T_OPEN_CURLY_BRACKET,
            ($stackPtr + 1),
            $tokens[$stackPtr]['bracket_closer']
        );

        if ($found !== false) {
            $nested = true;
        }

        if ($tokens[$next]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Opening brace should be the last content on the line';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ContentBefore');
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($stackPtr);
            }
        } else {
            $foundLines = ($tokens[$next]['line'] - $tokens[$stackPtr]['line'] - 1);
            if ($nested === true) {
                if ($foundLines !== 1) {
                    $error = 'Expected 1 blank line after opening brace of nesting class definition; %s found';
                    $data  = [$foundLines];
                    $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'AfterNesting', $data);

                    if ($fix === true) {
                        if ($foundLines === 0) {
                            $phpcsFile->fixer->addNewline($stackPtr);
                        } else {
                            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
                            $phpcsFile->fixer->beginChangeset();
                            for ($i = ($stackPtr + 1); $i < ($next + 1); $i++) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->addNewline($stackPtr);
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }//end if
            }//end if
        }//end if

    }//end process()


}//end class
