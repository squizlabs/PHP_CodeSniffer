<?php
/**
 * Detects unnecessary final modifiers inside of final classes.
 *
 * This rule is based on the PMD rule catalogue. The Unnecessary Final Modifier
 * sniff detects the use of the final modifier inside of a final class which
 * is unnecessary.
 *
 * <code>
 * final class Foo
 * {
 *     public final function bar()
 *     {
 *     }
 * }
 * </code>
 *
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class UnnecessaryFinalModifierSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_CLASS];

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
        $token  = $tokens[$stackPtr];

        // Skip for statements without body.
        if (isset($token['scope_opener']) === false) {
            return;
        }

        if ($phpcsFile->getClassProperties($stackPtr)['is_final'] === false) {
            // This class is not final so we don't need to check it.
            return;
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        for (; $next <= $end; ++$next) {
            if ($tokens[$next]['code'] === T_FINAL) {
                $error = 'Unnecessary FINAL modifier in FINAL class';
                $phpcsFile->addWarning($error, $next, 'Found');
            }

            // Skip over the contents of functions as those can't contain the `final` keyword anyway.
            if ($tokens[$next]['code'] === T_FUNCTION
                && isset($tokens[$next]['scope_closer']) === true
            ) {
                $next = $tokens[$next]['scope_closer'];
            }
        }

    }//end process()


}//end class
