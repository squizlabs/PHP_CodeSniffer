<?php
/**
 * Checks that the opening brace of a class/interface/trait is on the same line as the class declaration.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Classes;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class OpeningBraceSameLineSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
                T_TRAIT,
               );

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
        $tokens          = $phpcsFile->getTokens();
        $scopeIdentifier = $phpcsFile->findNext(T_STRING, ($stackPtr + 1));
        $errorData       = array(strtolower($tokens[$stackPtr]['content']).' '.$tokens[$scopeIdentifier]['content']);

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            $error = 'Possible parse error: %s missing opening or closing brace';
            $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $errorData);
            return;
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];

        // Is the brace on the same line as the class/interface/trait declaration ?
        $lastClassLineToken = $phpcsFile->findPrevious(T_WHITESPACE, ($openingBrace - 1), $stackPtr, true);
        $lastClassLine      = $tokens[$lastClassLineToken]['line'];
        $braceLine          = $tokens[$openingBrace]['line'];
        $lineDifference     = ($braceLine - $lastClassLine);

        if ($lineDifference > 0) {
            $phpcsFile->recordMetric($stackPtr, 'Class opening brace placement', 'new line');
            $error = 'Opening brace should be on the same line as the declaration for %s';
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'BraceOnNewLine', $errorData);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContent($lastClassLineToken, ' {');
                $phpcsFile->fixer->replaceToken($openingBrace, '');
                $phpcsFile->fixer->endChangeset();
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Class opening brace placement', 'same line');
        }

        // Is the opening brace the last thing on the line ?
        $next = $phpcsFile->findNext(T_WHITESPACE, ($openingBrace + 1), null, true);
        if ($tokens[$next]['line'] === $tokens[$openingBrace]['line']) {
            if ($next === $tokens[$stackPtr]['scope_closer']) {
                // Ignore empty classes.
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

        // Is there precisely one space before the opening brace ?
        if ($tokens[($openingBrace - 1)]['code'] !== T_WHITESPACE) {
            $length = 0;
        } else if ($tokens[($openingBrace - 1)]['content'] === "\t") {
            $length = '\t';
        } else {
            $length = strlen($tokens[($openingBrace - 1)]['content']);
        }

        if ($length !== 1) {
            $error = 'Expected 1 space before opening brace; found %s';
            $data  = array($length);
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'SpaceBeforeBrace', $data);
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
