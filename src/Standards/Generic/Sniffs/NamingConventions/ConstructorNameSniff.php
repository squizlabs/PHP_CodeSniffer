<?php
/**
 * Bans PHP 4 style constructors.
 *
 * Favor PHP 5 constructor syntax, which uses "function __construct()".
 * Avoid PHP 4 constructor syntax, which uses "function ClassName()".
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Leif Wickland <lwickland@rightnow.com>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions;

use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Files\File;

class ConstructorNameSniff extends AbstractScopeSniff
{

    /**
     * The name of the class we are currently checking.
     *
     * @var string
     */
    private $currentClass = '';

    /**
     * A list of functions in the current class.
     *
     * @var string[]
     */
    private $functionList = [];


    /**
     * Constructs the test with the tokens it wishes to listen for.
     */
    public function __construct()
    {
        parent::__construct([T_CLASS, T_ANON_CLASS, T_INTERFACE], [T_FUNCTION], true);

    }//end __construct()


    /**
     * Processes this test when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     * @param int                         $currScope A pointer to the start of the scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Determine if this is a function which needs to be examined.
        $conditions = $tokens[$stackPtr]['conditions'];
        end($conditions);
        $deepestScope = key($conditions);
        if ($deepestScope !== $currScope) {
            return;
        }

        $className = strtolower($phpcsFile->getDeclarationName($currScope));
        if ($className !== $this->currentClass) {
            $this->loadFunctionNamesInScope($phpcsFile, $currScope);
            $this->currentClass = $className;
        }

        $methodName = strtolower($phpcsFile->getDeclarationName($stackPtr));

        if ($methodName === $className) {
            if (in_array('__construct', $this->functionList, true) === false) {
                $error = 'PHP4 style constructors are not allowed; use "__construct()" instead';
                $phpcsFile->addError($error, $stackPtr, 'OldStyle');
            }
        } else if ($methodName !== '__construct') {
            // Not a constructor.
            return;
        }

        // Stop if the constructor doesn't have a body, like when it is abstract.
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $parentClassName = strtolower($phpcsFile->findExtendedClassName($currScope));
        if ($parentClassName === false) {
            return;
        }

        $endFunctionIndex = $tokens[$stackPtr]['scope_closer'];
        $startIndex       = $stackPtr;
        while (($doubleColonIndex = $phpcsFile->findNext(T_DOUBLE_COLON, $startIndex, $endFunctionIndex)) !== false) {
            if ($tokens[($doubleColonIndex + 1)]['code'] === T_STRING
                && strtolower($tokens[($doubleColonIndex + 1)]['content']) === $parentClassName
            ) {
                $error = 'PHP4 style calls to parent constructors are not allowed; use "parent::__construct()" instead';
                $phpcsFile->addError($error, ($doubleColonIndex + 1), 'OldStyleCall');
            }

            $startIndex = ($doubleColonIndex + 1);
        }

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


    /**
     * Extracts all the function names found in the given scope.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being scanned.
     * @param int                         $currScope A pointer to the start of the scope.
     *
     * @return void
     */
    protected function loadFunctionNamesInScope(File $phpcsFile, $currScope)
    {
        $this->functionList = [];
        $tokens = $phpcsFile->getTokens();

        for ($i = ($tokens[$currScope]['scope_opener'] + 1); $i < $tokens[$currScope]['scope_closer']; $i++) {
            if ($tokens[$i]['code'] !== T_FUNCTION) {
                continue;
            }

            $this->functionList[] = trim(strtolower($phpcsFile->getDeclarationName($i)));

            if (isset($tokens[$i]['scope_closer']) !== false) {
                // Skip past nested functions and such.
                $i = $tokens[$i]['scope_closer'];
            }
        }

    }//end loadFunctionNamesInScope()


}//end class
