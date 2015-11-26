<?php
/**
 * Ensures function params with default values are at the end of the declaration.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ValidDefaultValueSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_FUNCTION);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $argStart = $tokens[$stackPtr]['parenthesis_opener'];
        $argEnd   = $tokens[$stackPtr]['parenthesis_closer'];

        // Flag for when we have found a default in our arg list.
        // If there is a value without a default after this, it is an error.
        $defaultFound = false;

        $nextArg = $argStart;
        while (($nextArg = $phpcsFile->findNext(T_VARIABLE, ($nextArg + 1), $argEnd)) !== false) {
            if ($tokens[($nextArg - 1)]['code'] === T_ELLIPSIS) {
                continue;
            }

            $argHasDefault = self::argHasDefault($phpcsFile, $nextArg);
            if ($argHasDefault === false && $defaultFound === true) {
                $error = 'Arguments with default values must be at the end of the argument list';
                $phpcsFile->addError($error, $nextArg, 'NotAtEnd');
                return;
            }

            if ($argHasDefault === true) {
                $defaultFound = true;
            }
        }

    }//end process()


    /**
     * Returns true if the passed argument has a default value.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $argPtr    The position of the argument
     *                                        in the stack.
     *
     * @return bool
     */
    private static function argHasDefault($phpcsFile, $argPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($argPtr + 1), null, true);
        if ($tokens[$nextToken]['code'] !== T_EQUAL) {
            return false;
        }

        return true;

    }//end argHasDefault()


}//end class
