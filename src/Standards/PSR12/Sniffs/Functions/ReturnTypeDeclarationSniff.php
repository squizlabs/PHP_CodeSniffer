<?php
/**
 * Ensure return types are defined correctly for functions and closures.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ReturnTypeDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
            T_FN,
        ];

    }//end register()


    /**
     * Processes this test when one of its tokens is encountered.
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

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === false
            || isset($tokens[$stackPtr]['parenthesis_closer']) === false
            || $tokens[$stackPtr]['parenthesis_opener'] === null
            || $tokens[$stackPtr]['parenthesis_closer'] === null
        ) {
            return;
        }

        $methodProperties = $phpcsFile->getMethodProperties($stackPtr);
        if ($methodProperties['return_type'] === '') {
            return;
        }

        $returnType = $methodProperties['return_type_token'];
        if ($methodProperties['nullable_return_type'] === true) {
            $returnType = $phpcsFile->findPrevious(T_NULLABLE, ($returnType - 1));
        }

        if ($tokens[($returnType - 1)]['code'] !== T_WHITESPACE
            || $tokens[($returnType - 1)]['content'] !== ' '
            || $tokens[($returnType - 2)]['code'] !== T_COLON
        ) {
            $error = 'There must be a single space between the colon and type in a return type declaration';
            if ($tokens[($returnType - 1)]['code'] === T_WHITESPACE
                && $tokens[($returnType - 2)]['code'] === T_COLON
            ) {
                $fix = $phpcsFile->addFixableError($error, $returnType, 'SpaceBeforeReturnType');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($returnType - 1), ' ');
                }
            } else if ($tokens[($returnType - 1)]['code'] === T_COLON) {
                $fix = $phpcsFile->addFixableError($error, $returnType, 'SpaceBeforeReturnType');
                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore($returnType, ' ');
                }
            } else {
                $phpcsFile->addError($error, $returnType, 'SpaceBeforeReturnType');
            }
        }

        $colon = $phpcsFile->findPrevious(T_COLON, $returnType);
        if ($tokens[($colon - 1)]['code'] !== T_CLOSE_PARENTHESIS) {
            $error = 'There must not be a space before the colon in a return type declaration';
            $prev  = $phpcsFile->findPrevious(T_WHITESPACE, ($colon - 1), null, true);
            if ($tokens[$prev]['code'] === T_CLOSE_PARENTHESIS) {
                $fix = $phpcsFile->addFixableError($error, $colon, 'SpaceBeforeColon');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($x = ($prev + 1); $x < $colon; $x++) {
                        $phpcsFile->fixer->replaceToken($x, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $phpcsFile->addError($error, $colon, 'SpaceBeforeColon');
            }
        }

    }//end process()


}//end class
