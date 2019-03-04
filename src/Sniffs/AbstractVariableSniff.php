<?php
/**
 * A class to find T_VARIABLE tokens.
 *
 * This class can distinguish between normal T_VARIABLE tokens, and those tokens
 * that represent class members. If a class member is encountered, then the
 * processMemberVar method is called so the extending class can process it. If
 * the token is found to be a normal T_VARIABLE token, then processVariable is
 * called.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Sniffs\Conditions;
use PHP_CodeSniffer\Util\Sniffs\Variables;
use PHP_CodeSniffer\Util\Tokens;

abstract class AbstractVariableSniff extends AbstractScopeSniff
{


    /**
     * List of PHP Reserved variables.
     *
     * Used by various naming convention sniffs.
     *
     * Set from within the constructor.
     *
     * @var array
     *
     * @deprecated 3.5.0 Use PHP_CodeSniffer\Util\Sniffs\Variables::$phpReservedVars instead.
     */
    protected $phpReservedVars = [];


    /**
     * Constructs an AbstractVariableTest.
     */
    public function __construct()
    {
        // Preserve BC without code duplication.
        $this->phpReservedVars = Variables::$phpReservedVars;

        $scopes = Tokens::$ooScopeTokens;

        $listen = [
            T_VARIABLE,
            T_DOUBLE_QUOTED_STRING,
            T_HEREDOC,
        ];

        parent::__construct($scopes, $listen, true);

    }//end __construct()


    /**
     * Processes the token in the specified PHP_CodeSniffer\Files\File.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     * @param int                         $currScope The current scope opener token.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return ($phpcsFile->numTokens + 1) to skip
     *                  the rest of the file.
     */
    final protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING
            || $tokens[$stackPtr]['code'] === T_HEREDOC
        ) {
            // Check to see if this string has a variable in it.
            $pattern = '|(?<!\\\\)(?:\\\\{2})*\${?[a-zA-Z0-9_]+}?|';
            if (preg_match($pattern, $tokens[$stackPtr]['content']) !== 0) {
                return $this->processVariableInString($phpcsFile, $stackPtr);
            }

            return;
        }

        $deepestScope = Conditions::getLastCondition($phpcsFile, $stackPtr, Tokens::$ooScopeTokens);
        if ($deepestScope !== $currScope) {
            return;
        }

        if (Conditions::isOOProperty($phpcsFile, $stackPtr) === false) {
            return $this->processVariable($phpcsFile, $stackPtr);
        } else {
            return $this->processMemberVar($phpcsFile, $stackPtr);
        }

    }//end processTokenWithinScope()


    /**
     * Processes the token outside the scope in the file.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return ($phpcsFile->numTokens + 1) to skip
     *                  the rest of the file.
     */
    final protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        // These variables are not member vars.
        if ($tokens[$stackPtr]['code'] === T_VARIABLE) {
            return $this->processVariable($phpcsFile, $stackPtr);
        } else if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING
            || $tokens[$stackPtr]['code'] === T_HEREDOC
        ) {
            // Check to see if this string has a variable in it.
            $pattern = '|(?<!\\\\)(?:\\\\{2})*\${?[a-zA-Z0-9_]+}?|';
            if (preg_match($pattern, $tokens[$stackPtr]['content']) !== 0) {
                return $this->processVariableInString($phpcsFile, $stackPtr);
            }
        }

    }//end processTokenOutsideScope()


    /**
     * Called to process class member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return ($phpcsFile->numTokens + 1) to skip
     *                  the rest of the file.
     */
    abstract protected function processMemberVar(File $phpcsFile, $stackPtr);


    /**
     * Called to process normal member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return ($phpcsFile->numTokens + 1) to skip
     *                  the rest of the file.
     */
    abstract protected function processVariable(File $phpcsFile, $stackPtr);


    /**
     * Called to process variables found in double quoted strings or heredocs.
     *
     * Note that there may be more than one variable in the string, which will
     * result only in one call for the string or one call per line for heredocs.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the double quoted
     *                                               string was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return ($phpcsFile->numTokens + 1) to skip
     *                  the rest of the file.
     */
    abstract protected function processVariableInString(File $phpcsFile, $stackPtr);


}//end class
