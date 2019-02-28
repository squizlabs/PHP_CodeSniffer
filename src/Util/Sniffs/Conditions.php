<?php
/**
 * Utility functions for use when examining token conditions.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class Conditions
{


    /**
     * Return the position of the condition for the passed token.
     *
     * If no types are specified, the first condition for the token - or if $reverse=true,
     * the last condition - will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string|array            $types     Optional. The type(s) of tokens to search for.
     * @param bool                        $reverse   Optional. Whether to search for the highest
     *                                               (false) or the deepest condition (true) of
     *                                               the specified type(s).
     *
     * @return int|false StackPtr to the condition or false if the token does not have the condition.
     */
    public static function getCondition(File $phpcsFile, $stackPtr, $types=[], $reverse=false)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (empty($tokens[$stackPtr]['conditions']) === true) {
            return false;
        }

        $types      = (array) $types;
        $conditions = $tokens[$stackPtr]['conditions'];

        if (empty($types) === true) {
            // No types specified, just return the first/last condition pointer.
            if ($reverse === true) {
                end($conditions);
            } else {
                reset($conditions);
            }

            return key($conditions);
        }

        if ($reverse === true) {
            $conditions = array_reverse($conditions, true);
        }

        foreach ($conditions as $ptr => $type) {
            if (isset($tokens[$ptr]) === true
                && in_array($type, $types, true) === true
            ) {
                // We found a token with the required type.
                return $ptr;
            }
        }

        return false;

    }//end getCondition()


    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string|array            $types     The type(s) of tokens to search for.
     *
     * @return bool
     */
    public static function hasCondition(File $phpcsFile, $stackPtr, $types)
    {
        return (self::getCondition($phpcsFile, $stackPtr, $types) !== false);

    }//end hasCondition()


    /**
     * Return the position of the first condition of a certain type for the passed token.
     *
     * If no types are specified, the first condition for the token, independently of type,
     * will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string|array            $types     Optional. The type(s) of tokens to search for.
     *
     * @return int|false StackPtr to the condition or false if the token does not have the condition.
     */
    public static function getFirstCondition(File $phpcsFile, $stackPtr, $types=[])
    {
        return self::getCondition($phpcsFile, $stackPtr, $types, false);

    }//end getFirstCondition()


    /**
     * Return the position of the last condition of a certain type for the passed token.
     *
     * If no types are specified, the last condition for the token, independently of type,
     * will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the token we are checking.
     * @param int|string|array            $types     Optional. The type(s) of tokens to search for.
     *
     * @return int|false StackPtr to the condition or false if the token does not have the condition.
     */
    public static function getLastCondition(File $phpcsFile, $stackPtr, $types=[])
    {
        return self::getCondition($phpcsFile, $stackPtr, $types, true);

    }//end getLastCondition()


    /**
     * Check whether a T_VARIABLE token is a class/trait property declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               T_VARIABLE token to verify.
     *
     * @return bool
     */
    public static function isOOProperty(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== T_VARIABLE) {
            return false;
        }

        // Note: interfaces can not declare properties.
        $validScopes = [
            T_CLASS,
            T_ANON_CLASS,
            T_TRAIT,
        ];

        $scopePtr = self::validDirectScope($phpcsFile, $stackPtr, $validScopes);
        if ($scopePtr !== false) {
            // Make sure it's not a method parameter.
            if (empty($tokens[$stackPtr]['nested_parenthesis']) === true) {
                return true;
            } else {
                $parenthesis = $tokens[$stackPtr]['nested_parenthesis'];
                $parenthesis = array_keys($parenthesis);
                $deepestOpen = array_pop($parenthesis);
                if ($deepestOpen < $scopePtr
                    || isset($tokens[$deepestOpen]['parenthesis_owner']) === false
                    || $tokens[$tokens[$deepestOpen]['parenthesis_owner']]['code'] !== T_FUNCTION
                ) {
                    return true;
                }
            }
        }

        return false;

    }//end isOOProperty()


    /**
     * Check whether a T_CONST token is a class/interface constant declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               T_CONST token to verify.
     *
     * @return bool
     */
    public static function isOOConstant(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== T_CONST) {
            return false;
        }

        // Note: traits can not declare constants.
        $validScopes = [
            T_CLASS,
            T_ANON_CLASS,
            T_INTERFACE,
        ];

        if (self::validDirectScope($phpcsFile, $stackPtr, $validScopes) !== false) {
            return true;
        }

        return false;

    }//end isOOConstant()


    /**
     * Check whether a T_FUNCTION token is a class/interface/trait method declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               T_FUNCTION token to verify.
     *
     * @return bool
     */
    public static function isOOMethod(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== T_FUNCTION) {
            return false;
        }

        if (self::validDirectScope($phpcsFile, $stackPtr, Tokens::$ooScopeTokens) !== false) {
            return true;
        }

        return false;

    }//end isOOMethod()


    /**
     * Check whether the direct wrapping scope of a token is within a limited set of
     * acceptable tokens.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position in the stack of the
     *                                                 token to verify.
     * @param int|string|array            $validScopes Array of token constants.
     *
     * @return int|false StackPtr to the valid direct scope or false if no valid direct scope was found.
     */
    public static function validDirectScope(File $phpcsFile, $stackPtr, $validScopes)
    {
        $ptr = self::getLastCondition($phpcsFile, $stackPtr);

        if ($ptr !== false) {
            $tokens      = $phpcsFile->getTokens();
            $validScopes = (array) $validScopes;

            if (isset($tokens[$ptr]) === true
                && in_array($tokens[$ptr]['code'], $validScopes, true) === true
            ) {
                return $ptr;
            }
        }

        return false;

    }//end validDirectScope()


}//end class
