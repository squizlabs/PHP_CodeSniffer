<?php

/**
 * Bans the use of compact() function
 *
 * @author    Paweł Bogut <pbogut@pbogut.me>
 * @copyright 2006-2023 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class DisallowCompactArrayBuilderSniff implements Sniff
{
    protected const VARIABLE_NAME_PATTERN = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/';


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_STRING];

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

        $content = $tokens[$stackPtr]['content'];

        if (strtolower($content) !== 'compact') {
            return;
        }

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        $prev     = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        $prevPrev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prev - 1), null, true);

        $ignorePrev = [
            T_BITWISE_AND,
            T_NS_SEPARATOR,
        ];

        $excludedPrev = [
            T_NULLSAFE_OBJECT_OPERATOR,
            T_OBJECT_OPERATOR,
            T_DOUBLE_COLON,
            T_NEW,
            T_NAMESPACE,
            T_STRING,
            T_FUNCTION,
        ];

        $significantPrev = $prev;
        if (in_array($tokens[$prev]['code'], $ignorePrev) === true) {
            $significantPrev = $prevPrev;
        }

        // Make sure it is built-in function call.
        if (in_array($tokens[$significantPrev]['code'], $excludedPrev) === true) {
            // Not a built-in function call.
            return;
        }

        $error = 'Array must not be created with compact() function';

        // Make sure it is not prepended by bitwise operator.
        if ($tokens[$prev]['code'] === T_BITWISE_AND) {
            // Can not be fixed as &[] is not valid syntax.
            $phpcsFile->addError($error, $stackPtr, 'Found');
            return;
        }

        $fixable  = false;
        $toExpand = [];
        $openPtr  = $next;
        $closePtr = null;
        // Find all params in compact() function call, and check if it is fixable.
        while (($next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true)) !== false) {
            if ($tokens[$next]['code'] === T_CONSTANT_ENCAPSED_STRING) {
                $variableName = substr($tokens[$next]['content'], 1, -1);
                $isValid      = preg_match(self::VARIABLE_NAME_PATTERN, $variableName);

                if ($isValid === false || $isValid === 0) {
                    break;
                }

                $toExpand[] = $next;
                continue;
            }

            if ($tokens[$next]['code'] === T_CLOSE_PARENTHESIS) {
                $fixable  = true;
                $closePtr = $next;
                break;
            }

            if ($tokens[$next]['code'] !== T_COMMA) {
                break;
            }
        }//end while

        if ($fixable === false) {
            $phpcsFile->addError($error, $stackPtr, 'Found');
            return;
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found');

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();

            if ($tokens[$prev]['code'] === T_NS_SEPARATOR) {
                $phpcsFile->fixer->replaceToken($prev, '');
            }

            $phpcsFile->fixer->replaceToken($stackPtr, '');
            $phpcsFile->fixer->replaceToken($openPtr, '[');
            $phpcsFile->fixer->replaceToken($closePtr, ']');

            foreach ($toExpand as $ptr) {
                $variableName = substr($tokens[$ptr]['content'], 1, -1);
                $phpcsFile->fixer->replaceToken(
                    $ptr,
                    $tokens[$ptr]['content'].' => $'.$variableName
                );
            }

            $phpcsFile->fixer->endChangeset();
        }//end if

    }//end process()


}//end class
