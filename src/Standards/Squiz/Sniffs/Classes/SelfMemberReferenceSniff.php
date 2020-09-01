<?php
/**
 * Tests self member references.
 *
 * Verifies that :
 * - self:: is used instead of Self::
 * - self:: is used for local static member reference
 * - self:: is used instead of self ::
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

class SelfMemberReferenceSniff extends AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Classes_SelfMemberReferenceSniff.
     */
    public function __construct()
    {
        parent::__construct([T_CLASS], [T_DOUBLE_COLON]);

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     * @param int                         $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Determine if this is a double colon which needs to be examined.
        $conditions = $tokens[$stackPtr]['conditions'];
        $conditions = array_reverse($conditions, true);
        foreach ($conditions as $conditionToken => $tokenCode) {
            if ($tokenCode === T_CLASS || $tokenCode === T_ANON_CLASS || $tokenCode === T_CLOSURE) {
                break;
            }
        }

        if ($conditionToken !== $currScope) {
            return;
        }

        $calledClassName = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($calledClassName === false) {
            // Parse error.
            return;
        }

        if ($tokens[$calledClassName]['code'] === T_SELF) {
            if ($tokens[$calledClassName]['content'] !== 'self') {
                $error = 'Must use "self::" for local static member reference; found "%s::"';
                $data  = [$tokens[$calledClassName]['content']];
                $fix   = $phpcsFile->addFixableError($error, $calledClassName, 'IncorrectCase', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($calledClassName, 'self');
                }

                return;
            }
        } else if ($tokens[$calledClassName]['code'] === T_STRING
            || $tokens[$calledClassName]['code'] === T_NAME_QUALIFIED
            || $tokens[$calledClassName]['code'] === T_NAME_FULLY_QUALIFIED
            || $tokens[$calledClassName]['code'] === T_NAME_RELATIVE
        ) {
            $namespaceName = $this->getNamespaceName($phpcsFile, $currScope);
            if ($namespaceName === '\\') {
                $namespaceName = '';
            }

            $declarationName = $namespaceName.'\\'.$phpcsFile->getDeclarationName($currScope);

            switch ($tokens[$calledClassName]['code']) {
            case T_NAME_FULLY_QUALIFIED:
                $inlineName = $tokens[$calledClassName]['content'];
                break;

            case T_NAME_QUALIFIED:
            case T_STRING:
                $inlineName = $namespaceName.'\\'.$tokens[$calledClassName]['content'];
                break;

            case T_NAME_RELATIVE:
                $inlineName = $namespaceName.substr($tokens[$calledClassName]['content'], 9);
                break;
            }

            if ($declarationName === $inlineName) {
                // Class name is the same as the current class, which is not allowed.
                $error = 'Must use "self::" for local static member reference';
                $fix   = $phpcsFile->addFixableError($error, $calledClassName, 'NotUsed');

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($calledClassName, '');
                    $phpcsFile->fixer->replaceToken($stackPtr, 'self::');
                    $phpcsFile->fixer->endChangeset();

                    // Fix potential whitespace issues in the next loop.
                    return;
                }//end if
            }//end if
        }//end if

        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $found = $tokens[($stackPtr - 1)]['length'];
            $error = 'Expected 0 spaces before double colon; %s found';
            $data  = [$found];
            $fix   = $phpcsFile->addFixableError($error, ($stackPtr - 1), 'SpaceBefore', $data);

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                for ($i = ($stackPtr - 1); $tokens[$i]['code'] === T_WHITESPACE; $i--) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
            $found = $tokens[($stackPtr + 1)]['length'];
            $error = 'Expected 0 spaces after double colon; %s found';
            $data  = [$found];
            $fix   = $phpcsFile->addFixableError($error, ($stackPtr - 1), 'SpaceAfter', $data);

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                for ($i = ($stackPtr + 1); $tokens[$i]['code'] === T_WHITESPACE; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
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
     * Returns the namespace name of a the current scope.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the search for the
     *                                               namespace declaration will start.
     *
     * @return string
     */
    protected function getNamespaceName(File $phpcsFile, $stackPtr)
    {
        $namespace            = '\\';
        $namespaceDeclaration = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);

        if ($namespaceDeclaration !== false) {
            $tokens       = $phpcsFile->getTokens();
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($namespaceDeclaration + 1), null, true);
            if ($nextNonEmpty !== false
                && ($tokens[$nextNonEmpty]['code'] === T_NAME_QUALIFIED
                || $tokens[$nextNonEmpty]['code'] === T_STRING)
            ) {
                $namespace .= $tokens[$nextNonEmpty]['content'];
            }
        }

        return $namespace;

    }//end getNamespaceName()


}//end class
