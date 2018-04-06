<?php
/**
 * Verifies that operators have valid spacing surrounding them.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class LogicalOperatorSpacingSniff implements Sniff
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
        return Tokens::$booleanOperators;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check there is one space before the operator.
        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space before logical operator; 0 found';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBefore');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }
        } else {
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($tokens[$stackPtr]['line'] === $tokens[$prev]['line']
                && strlen($tokens[($stackPtr - 1)]['content']) !== 1
            ) {
                $found = strlen($tokens[($stackPtr - 1)]['content']);
                $error = 'Expected 1 space before logical operator; %s found';
                $data  = [$found];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'TooMuchSpaceBefore', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                }
            }
        }

        // Check there is one space after the operator.
        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space after logical operator; 0 found';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfter');
            if ($fix === true) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
        } else {
            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$stackPtr]['line'] === $tokens[$next]['line']
                && strlen($tokens[($stackPtr + 1)]['content']) !== 1
            ) {
                $found = strlen($tokens[($stackPtr + 1)]['content']);
                $error = 'Expected 1 space after logical operator; %s found';
                $data  = [$found];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'TooMuchSpaceAfter', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        }

    }//end process()


}//end class
