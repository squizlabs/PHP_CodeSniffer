<?php
/**
 * Utility functions to determine what an ambiguous token represents.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
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


    /**
     * Determine whether a T_OPEN/CLOSE_SHORT_ARRAY token is a short list() construct.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the array bracket token.
     *
     * @return bool True if the token passed is the open/close bracket of a short list.
     *              False if the token is a short array bracket or not
     *              a T_OPEN/CLOSE_SHORT_ARRAY token.
     */
    public static function isShortList(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this one of the tokens this function handles ?
        if ($tokens[$stackPtr]['code'] !== T_OPEN_SHORT_ARRAY
            && $tokens[$stackPtr]['code'] !== T_CLOSE_SHORT_ARRAY
        ) {
            return false;
        }

        switch ($tokens[$stackPtr]['code']) {
        case T_OPEN_SHORT_ARRAY:
            $opener = $stackPtr;
            $closer = $tokens[$stackPtr]['bracket_closer'];
            break;

        case T_CLOSE_SHORT_ARRAY:
            $opener = $tokens[$stackPtr]['bracket_opener'];
            $closer = $stackPtr;
            break;
        }

        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($closer + 1), null, true, null, true);
        if ($nextNonEmpty !== false && $tokens[$nextNonEmpty]['code'] === T_EQUAL) {
            return true;
        }

        // Check for short list in foreach, i.e. `foreach($array as [$a, $b])`.
        $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($opener - 1), null, true, null, true);
        if ($prevNonEmpty !== false
            && ($tokens[$prevNonEmpty]['code'] === T_AS
            || $tokens[$prevNonEmpty]['code'] === T_DOUBLE_ARROW)
            && Parentheses::lastOwnerIn($phpcsFile, $prevNonEmpty, T_FOREACH) !== false
        ) {
            return true;
        }

        // Maybe this is a short list syntax nested inside another short list syntax ?
        $parentOpen = $opener;
        do {
            $parentOpen = $phpcsFile->findPrevious(
                T_OPEN_SHORT_ARRAY,
                ($parentOpen - 1),
                null,
                false,
                null,
                true
            );

            if ($parentOpen === false) {
                return false;
            }
        } while ($tokens[$parentOpen]['bracket_closer'] < $opener);

        return self::isShortList($phpcsFile, $parentOpen);

    }//end isShortList()


    /**
     * Determine whether a T_MINUS/T_PLUS token is a unary operator.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the plus/minus token.
     *
     * @return bool True if the token passed is a unary operator.
     *              False otherwise or if the token is not a T_PLUS/T_MINUS token.
     */
    public static function isUnaryPlusMinus(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== T_PLUS
            && $tokens[$stackPtr]['code'] !== T_MINUS)
        ) {
            return false;
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            // Live coding or parse error.
            return false;
        }

        if (isset(Tokens::$operators[$tokens[$next]['code']]) === true) {
            // Next token is an operator, so this is not a unary.
            return false;
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($tokens[$prev]['code'] === T_RETURN) {
            // Just returning a positive/negative value; eg. (return -1).
            return true;
        }

        if (isset(Tokens::$operators[$tokens[$prev]['code']]) === true) {
            // Just trying to operate on a positive/negative value; eg. ($var * -1).
            return true;
        }

        if (isset(Tokens::$comparisonTokens[$tokens[$prev]['code']]) === true) {
            // Just trying to compare a positive/negative value; eg. ($var === -1).
            return true;
        }

        if (isset(Tokens::$booleanOperators[$tokens[$prev]['code']]) === true) {
            // Just trying to compare a positive/negative value; eg. ($var || -1 === $b).
            return true;
        }

        if (isset(Tokens::$assignmentTokens[$tokens[$prev]['code']]) === true) {
            // Just trying to assign a positive/negative value; eg. ($var = -1).
            return true;
        }

        if (isset(Tokens::$castTokens[$tokens[$prev]['code']]) === true) {
            // Just casting a positive/negative value; eg. (string) -$var.
            return true;
        }

        // Other indicators that a plus/minus sign is a unary operator.
        $invalidTokens = [
            T_COMMA               => true,
            T_OPEN_PARENTHESIS    => true,
            T_OPEN_SQUARE_BRACKET => true,
            T_OPEN_SHORT_ARRAY    => true,
            T_COLON               => true,
            T_INLINE_THEN         => true,
            T_INLINE_ELSE         => true,
            T_CASE                => true,
            T_OPEN_CURLY_BRACKET  => true,
        ];

        if (isset($invalidTokens[$tokens[$prev]['code']]) === true) {
            // Just trying to use a positive/negative value; eg. myFunction($var, -2).
            return true;
        }

        return false;

    }//end isUnaryPlusMinus()


}//end class
