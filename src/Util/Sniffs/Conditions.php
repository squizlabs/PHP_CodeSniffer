<?php
/**
 * Utility functions for use when examining token conditions.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Files\File;

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


}//end class
