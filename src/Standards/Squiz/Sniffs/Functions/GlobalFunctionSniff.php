<?php
/**
 * Tests for functions outside of classes.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Sniffs\Conditions;
use PHP_CodeSniffer\Util\Sniffs\ConstructNames;
use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;
use PHP_CodeSniffer\Util\Tokens;

class GlobalFunctionSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FUNCTION];

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
        if (Conditions::hasCondition($phpcsFile, $stackPtr, Tokens::$ooScopeTokens) === true) {
            return;
        }

        // Special exception for PHP magic functions as they need to be global.
        if (FunctionDeclarations::isMagicFunction($phpcsFile, $stackPtr) === true) {
            return;
        }

        $functionName = ConstructNames::getDeclarationName($phpcsFile, $stackPtr);
        if (empty($functionName) === true) {
            // Live coding or parse error.
            return;
        }

        $error = 'Consider putting global function "%s" in a static class';
        $data  = [$functionName];
        $phpcsFile->addWarning($error, $stackPtr, 'Found', $data);

    }//end process()


}//end class
