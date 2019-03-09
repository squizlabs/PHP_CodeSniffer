<?php
/**
 * Utility functions to determine what an ambiguous token represents.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class TokenIs
{


    /**
     * Determine if the passed token is a reference operator.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the T_BITWISE_AND token.
     *
     * @return boolean True if the specified token represents a reference.
     *                 False if the token represents a bitwise operator or is not
     *                 a T_BITWISE_AND token.
     */
    public static function isReference(File $phpcsFile, $stackPtr)
    {
		$tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($tokens[$tokenBefore]['code'] === T_FUNCTION) {
            // Function returns a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === T_DOUBLE_ARROW) {
            // Inside a foreach loop or array assignment, this is a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (isset(Tokens::$assignmentTokens[$tokens[$tokenBefore]['code']]) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        $tokenAfter = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$tokenAfter]['code'] === T_NEW) {
            return true;
        }

        $lastOpener = Parentheses::getLastOpener($phpcsFile, $stackPtr);
        if ($lastOpener !== false) {
            $lastOwner = Parentheses::lastOwnerIn($phpcsFile, $stackPtr, [T_FUNCTION, T_CLOSURE]);
            if ($lastOwner !== false) {
                $params = FunctionDeclarations::getParameters($phpcsFile, $lastOwner);
                foreach ($params as $param) {
                    $varToken = $tokenAfter;
                    if ($param['variable_length'] === true) {
                        $varToken = $phpcsFile->findNext(
                            (Tokens::$emptyTokens + [T_ELLIPSIS]),
                            ($stackPtr + 1),
                            null,
                            true
                        );
                    }

                    if ($param['token'] === $varToken
                        && $param['pass_by_reference'] === true
                    ) {
                        // Function parameter declared to be passed by reference.
                        return true;
                    }
                }
            } else if (isset($tokens[$lastOpener]['parenthesis_owner']) === false) {
                $prev = false;
                for ($t = ($lastOpener - 1); $t >= 0; $t--) {
                    if ($tokens[$t]['code'] !== T_WHITESPACE) {
                        $prev = $t;
                        break;
                    }
                }

                if ($prev !== false && $tokens[$prev]['code'] === T_USE) {
                    // Closure use by reference.
                    return true;
                }
            }//end if
        }//end if

        // Pass by reference in function calls and assign by reference in arrays.
        if ($tokens[$tokenBefore]['code'] === T_OPEN_PARENTHESIS
            || $tokens[$tokenBefore]['code'] === T_COMMA
            || $tokens[$tokenBefore]['code'] === T_OPEN_SHORT_ARRAY
        ) {
            if ($tokens[$tokenAfter]['code'] === T_VARIABLE) {
                return true;
            } else {
                $skip   = Tokens::$emptyTokens;
                $skip[] = T_NS_SEPARATOR;
                $skip[] = T_SELF;
                $skip[] = T_PARENT;
                $skip[] = T_STATIC;
                $skip[] = T_STRING;
                $skip[] = T_NAMESPACE;
                $skip[] = T_DOUBLE_COLON;

                $nextSignificantAfter = $phpcsFile->findNext(
                    $skip,
                    ($stackPtr + 1),
                    null,
                    true
                );
                if ($tokens[$nextSignificantAfter]['code'] === T_VARIABLE) {
                    return true;
                }
            }//end if
        }//end if

        return false;

    }//end isReference()


}//end class
