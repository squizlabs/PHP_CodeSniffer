<?php
/**
 * Checks for usage of $this in static methods, which will cause runtime errors.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

class StaticThisUsageSniff extends AbstractScopeSniff
{


    /**
     * Constructs the test with the tokens it wishes to listen for.
     */
    public function __construct()
    {
        parent::__construct([T_CLASS, T_TRAIT, T_ENUM, T_ANON_CLASS], [T_FUNCTION]);

    }//end __construct()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     * @param int                         $currScope A pointer to the start of the scope.
     *
     * @return void
     */
    public function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Determine if this is a function which needs to be examined.
        $conditions = $tokens[$stackPtr]['conditions'];
        end($conditions);
        $deepestScope = key($conditions);
        if ($deepestScope !== $currScope) {
            return;
        }

        // Ignore abstract functions.
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false || $tokens[$next]['code'] !== T_STRING) {
            // Not a function declaration, or incomplete.
            return;
        }

        $methodProps = $phpcsFile->getMethodProperties($stackPtr);
        if ($methodProps['is_static'] === false) {
            return;
        }

        $next = $stackPtr;
        $end  = $tokens[$stackPtr]['scope_closer'];

        $this->checkThisUsage($phpcsFile, $next, $end);

    }//end processTokenWithinScope()


    /**
     * Check for $this variable usage between $next and $end tokens.
     *
     * @param File $phpcsFile The current file being scanned.
     * @param int  $next      The position of the next token to check.
     * @param int  $end       The position of the last token to check.
     *
     * @return void
     */
    private function checkThisUsage(File $phpcsFile, $next, $end)
    {
        $tokens = $phpcsFile->getTokens();

        do {
            $next = $phpcsFile->findNext([T_VARIABLE, T_ANON_CLASS], ($next + 1), $end);
            if ($next === false) {
                continue;
            }

            if ($tokens[$next]['code'] === T_ANON_CLASS) {
                $this->checkThisUsage($phpcsFile, $next, $tokens[$next]['scope_opener']);
                $next = $tokens[$next]['scope_closer'];
                continue;
            }

            if ($tokens[$next]['content'] !== '$this') {
                continue;
            }

            $error = 'Usage of "$this" in static methods will cause runtime errors';
            $phpcsFile->addError($error, $next, 'Found');
        } while ($next !== false);

    }//end checkThisUsage()


    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack where this
     *                                               token was found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {

    }//end processTokenOutsideScope()


}//end class
