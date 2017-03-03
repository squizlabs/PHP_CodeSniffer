<?php
/**
 * Checks that the opening brace of a function is on the same line as the function declaration.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class OpeningFunctionBraceKernighanRitchieSniff implements Sniff
{


    /**
     * Should this sniff check function braces?
     *
     * @var boolean
     */
    public $checkFunctions = true;

    /**
     * Should this sniff check closure braces?
     *
     * @var boolean
     */
    public $checkClosures = false;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return void
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLOSURE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        if (($tokens[$stackPtr]['code'] === T_FUNCTION
            && (bool) $this->checkFunctions === false)
            || ($tokens[$stackPtr]['code'] === T_CLOSURE
            && (bool) $this->checkClosures === false)
        ) {
            return;
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($closeBracket + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket  = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1));
                $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
            }
        }

        $functionLine = $tokens[$closeBracket]['line'];
        $braceLine    = $tokens[$openingBrace]['line'];

        $lineDifference = ($braceLine - $functionLine);

        if ($lineDifference > 0) {
            $phpcsFile->recordMetric($stackPtr, 'Function opening brace placement', 'new line');
            $error = 'Opening brace should be on the same line as the declaration';
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'BraceOnNewLine');
            if ($fix === true) {
                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($openingBrace - 1), $closeBracket, true);
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContent($prev, ' {');
                $phpcsFile->fixer->replaceToken($openingBrace, '');
                if ($tokens[($openingBrace + 1)]['code'] === T_WHITESPACE
                    && $tokens[($openingBrace + 2)]['line'] > $tokens[$openingBrace]['line']
                ) {
                    // Brace is followed by a new line, so remove it to ensure we don't
                    // leave behind a blank line at the top of the block.
                    $phpcsFile->fixer->replaceToken(($openingBrace + 1), '');

                    if ($tokens[($openingBrace - 1)]['code'] === T_WHITESPACE
                        && $tokens[($openingBrace - 1)]['line'] === $tokens[$openingBrace]['line']
                        && $tokens[($openingBrace - 2)]['line'] < $tokens[$openingBrace]['line']
                    ) {
                        // Brace is preceeded by indent, so remove it to ensure we don't
                        // leave behind more indent than is required for the first line.
                        $phpcsFile->fixer->replaceToken(($openingBrace - 1), '');
                    }
                }

                $phpcsFile->fixer->endChangeset();
            }//end if
        }//end if

        $phpcsFile->recordMetric($stackPtr, 'Function opening brace placement', 'same line');

        $next = $phpcsFile->findNext(T_WHITESPACE, ($openingBrace + 1), null, true);
        if ($tokens[$next]['line'] === $tokens[$openingBrace]['line']) {
            if ($next === $tokens[$stackPtr]['scope_closer']) {
                // Ignore empty functions.
                return;
            }

            $error = 'Opening brace must be the last content on the line';
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'ContentAfterBrace');
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($openingBrace);
            }
        }

        // Only continue checking if the opening brace looks good.
        if ($lineDifference > 0) {
            return;
        }

        // We are looking for tabs, even if they have been replaced, because
        // we enforce a space here.
        if (isset($tokens[($openingBrace - 1)]['orig_content']) === true) {
            $spacing = $tokens[($openingBrace - 1)]['content'];
        } else {
            $spacing = $tokens[($openingBrace - 1)]['content'];
        }

        if ($tokens[($openingBrace - 1)]['code'] !== T_WHITESPACE) {
            $length = 0;
        } else if ($spacing === "\t") {
            $length = '\t';
        } else {
            $length = strlen($spacing);
        }

        if ($length !== 1) {
            $error = 'Expected 1 space before opening brace; found %s';
            $data  = array($length);
            $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'SpaceBeforeBrace', $data);
            if ($fix === true) {
                if ($length === 0 || $length === '\t') {
                    $phpcsFile->fixer->addContentBefore($openingBrace, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken(($openingBrace - 1), ' ');
                }
            }
        }

    }//end process()


}//end class
