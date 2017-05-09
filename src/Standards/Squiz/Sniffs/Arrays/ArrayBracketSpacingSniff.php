<?php
/**
 * Ensure that there are no spaces around square brackets.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ArrayBracketSpacingSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_SQUARE_BRACKET,
                T_CLOSE_SQUARE_BRACKET,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // PHP 5.4 introduced a shorthand array declaration syntax, so we need
        // to ignore the these type of array declarations because this sniff is
        // only dealing with array usage.
        if ($tokens[$stackPtr]['code'] === T_OPEN_SQUARE_BRACKET) {
            $openBracket = $stackPtr;
        } else {
            if (isset($tokens[$stackPtr]['bracket_opener']) === false) {
                return;
            }

            $openBracket = $tokens[$stackPtr]['bracket_opener'];
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($openBracket - 1), null, true);
        if ($tokens[$prev]['code'] === T_EQUAL) {
            return;
        }

        // Square brackets can not have a space before them.
        $prevType = $tokens[($stackPtr - 1)]['code'];
        if (isset(Tokens::$emptyTokens[$prevType]) === true) {
            $nonSpace = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 2), null, true);
            $expected = $tokens[$nonSpace]['content'].$tokens[$stackPtr]['content'];
            $found    = $phpcsFile->getTokensAsString($nonSpace, ($stackPtr - $nonSpace)).$tokens[$stackPtr]['content'];
            $error    = 'Space found before square bracket; expected "%s" but found "%s"';
            $data     = array(
                         $expected,
                         $found,
                        );
            $fix      = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeBracket', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($stackPtr - 1), '');
            }
        }

        // Open square brackets can't ever have spaces after them.
        if ($tokens[$stackPtr]['code'] === T_OPEN_SQUARE_BRACKET) {
            $nextType = $tokens[($stackPtr + 1)]['code'];
            if (isset(Tokens::$emptyTokens[$nextType]) === true) {
                $nonSpace = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 2), null, true);
                $expected = $tokens[$stackPtr]['content'].$tokens[$nonSpace]['content'];
                $found    = $phpcsFile->getTokensAsString($stackPtr, ($nonSpace - $stackPtr + 1));
                $error    = 'Space found after square bracket; expected "%s" but found "%s"';
                $data     = array(
                             $expected,
                             $found,
                            );
                $fix      = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterBracket', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
                }
            }
        }

    }//end process()


}//end class
