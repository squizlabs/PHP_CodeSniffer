<?php
/**
 * Verifies that constants are declared with a scope modifier.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ConstantDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_CONST];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $phpVersion = Config::getConfigData('php_version');
        if (empty($phpVersion) === true) {
            $phpVersion = PHP_VERSION_ID;
        }

        if ($phpVersion < 70100) {
            // This sniff does not apply for php version < 7.1.0.
            return ($stackPtr + 1);
        }

        $tokens = $phpcsFile->getTokens();

        // Expectation: whitespace prepended with a visibility indicator.
        $scopeModifierPtr = $phpcsFile->findPrevious([T_WHITESPACE], ($stackPtr - 1), 0, true);
        if (isset($tokens[$scopeModifierPtr]) === false || is_array($tokens[$scopeModifierPtr]) === false
            || in_array($tokens[$scopeModifierPtr]['code'], Tokens::$scopeModifiers) === false
        ) {
            $phpcsFile->addError('Visibility must be declared on constant', $stackPtr, 'ScopeMissing');
        }

        return ($stackPtr + 1);

    }//end process()


}//end class
