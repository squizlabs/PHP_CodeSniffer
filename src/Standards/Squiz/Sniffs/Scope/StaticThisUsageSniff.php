<?php
/**
 * Checks for usage of $this in static methods, which will cause runtime errors.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope;

use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Files\File;

class StaticThisUsageSniff extends AbstractScopeSniff
{


    /**
     * Constructs the test with the tokens it wishes to listen for.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_FUNCTION));

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
        $tokens   = $phpcsFile->getTokens();
        $function = $tokens[($stackPtr + 2)];

        if ($function['code'] !== T_STRING) {
            return;
        }

        $functionName = $function['content'];
        $classOpener  = $tokens[$currScope]['scope_condition'];
        $className    = $tokens[($classOpener + 2)]['content'];

        $methodProps = $phpcsFile->getMethodProperties($stackPtr);

        if ($methodProps['is_static'] === true) {
            if (isset($tokens[$stackPtr]['scope_closer']) === false) {
                // There is no scope opener or closer, so the function
                // must be abstract.
                return;
            }

            $thisUsage = $stackPtr;
            while (($thisUsage = $phpcsFile->findNext(array(T_VARIABLE), ($thisUsage + 1), $tokens[$stackPtr]['scope_closer'], false, '$this')) !== false) {
                if ($thisUsage === false) {
                    return;
                }

                $error = 'Usage of "$this" in static methods will cause runtime errors';
                $phpcsFile->addError($error, $thisUsage, 'Found');
            }
        }//end if

    }//end processTokenWithinScope()


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
