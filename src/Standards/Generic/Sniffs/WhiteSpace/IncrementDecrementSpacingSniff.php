<?php
/**
 * Verifies spacing between variables and increment/decrement operators.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class IncrementDecrementSpacingSniff implements Sniff
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
            T_DEC,
            T_INC,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $tokenName = 'increment';
        if ($tokens[$stackPtr]['code'] === T_DEC) {
            $tokenName = 'decrement';
        }

        // Is this a pre-increment/decrement ?
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($nextNonEmpty !== false
            && (($phpcsFile->tokenizerType === 'PHP' && $tokens[$nextNonEmpty]['code'] === T_VARIABLE)
            || ($phpcsFile->tokenizerType === 'JS' && $tokens[$nextNonEmpty]['code'] === T_STRING))
        ) {
            if ($nextNonEmpty === ($stackPtr + 1)) {
                $phpcsFile->recordMetric($stackPtr, 'Spacing between in/decrementor and variable', 0);
                return;
            }

            $spaces            = 0;
            $fixable           = true;
            $nextNonWhitespace = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($nextNonWhitespace !== $nextNonEmpty) {
                $fixable = false;
                $spaces  = 'comment';
            } else {
                if ($tokens[$stackPtr]['line'] !== $tokens[$nextNonEmpty]['line']) {
                    $spaces = 'newline';
                } else {
                    $spaces = $tokens[($stackPtr + 1)]['length'];
                }
            }

            $phpcsFile->recordMetric($stackPtr, 'Spacing between in/decrementor and variable', $spaces);

            $error     = 'Expected no spaces between the %s operator and %s; %s found';
            $errorCode = 'SpaceAfter'.ucfirst($tokenName);
            $data      = [
                $tokenName,
                $tokens[$nextNonEmpty]['content'],
                $spaces,
            ];

            if ($fixable === false) {
                $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
                return;
            }

            $fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode, $data);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($stackPtr + 1); $i < $nextNonEmpty; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }

            return;
        }//end if

        // Is this a post-increment/decrement ?
        $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($prevNonEmpty !== false
            && (($phpcsFile->tokenizerType === 'PHP' && $tokens[$prevNonEmpty]['code'] === T_VARIABLE)
            || ($phpcsFile->tokenizerType === 'JS' && $tokens[$prevNonEmpty]['code'] === T_STRING))
        ) {
            if ($prevNonEmpty === ($stackPtr - 1)) {
                $phpcsFile->recordMetric($stackPtr, 'Spacing between in/decrementor and variable', 0);
                return;
            }

            $spaces            = 0;
            $fixable           = true;
            $prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($prevNonWhitespace !== $prevNonEmpty) {
                $fixable = false;
                $spaces  = 'comment';
            } else {
                if ($tokens[$stackPtr]['line'] !== $tokens[$nextNonEmpty]['line']) {
                    $spaces = 'newline';
                } else {
                    $spaces = $tokens[($stackPtr - 1)]['length'];
                }
            }

            $phpcsFile->recordMetric($stackPtr, 'Spacing between in/decrementor and variable', $spaces);

            $error     = 'Expected no spaces between %s and the %s operator; %s found';
            $errorCode = 'SpaceAfter'.ucfirst($tokenName);
            $data      = [
                $tokens[$prevNonEmpty]['content'],
                $tokenName,
                $spaces,
            ];

            if ($fixable === false) {
                $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
                return;
            }

            $fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode, $data);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($stackPtr - 1); $prevNonEmpty < $i; $i--) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }//end if

    }//end process()


}//end class
