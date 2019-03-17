<?php
/**
 * Utility functions for use when examining nested parentheses.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Files\File;

class Parentheses
{


    /**
     * Get the pointer to the parentheses owner of an open/close parenthesis.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of T_OPEN/CLOSE_PARENTHESIS token.
     *
     * @return int|false StackPtr to the parentheses owner or false if the parenthesis
     *                   does not have a (direct) owner.
     */
    public static function getOwner(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr], $tokens[$stackPtr]['parenthesis_owner']) === false) {
            return false;
        }

        return $tokens[$stackPtr]['parenthesis_owner'];

    }//end getOwner()


    /**
     * Check whether the parenthesis owner of an open/close parenthesis is within a
     * limited set of valid owners.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of T_OPEN/CLOSE_PARENTHESIS token.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return boolean True if the owner is within the list of $validOwners, false if not and
     *                 if the parenthesis does not have a (direct) owner.
     */
    public static function isOwnerIn(File $phpcsFile, $stackPtr, $validOwners)
    {
        $owner = self::getOwner($phpcsFile, $stackPtr);
        if ($owner === false) {
            return false;
        }

        $tokens      = $phpcsFile->getTokens();
        $validOwners = (array) $validOwners;
        return in_array($tokens[$owner]['code'], $validOwners, true);

    }//end isOwnerIn()


    /**
     * Check whether the passed token is nested within parentheses owned by one of the valid owners.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return boolean
     */
    public static function hasOwner(File $phpcsFile, $stackPtr, $validOwners)
    {
        return (self::nestedParensWalker($phpcsFile, $stackPtr, $validOwners) !== false);

    }//end hasOwner()


    /**
     * Retrieve the position of the opener to the first set of parentheses an arbitrary token
     * is wrapped in where the parentheses owner is within the set of valid owners.
     *
     * If no $validOwners are specified, the opener to the first set of parentheses surrounding
     * the token will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false StackPtr to the parentheses opener or false if the token does not have
     *                   parentheses owned by any of the valid owners or if the token is not
     *                   nested in parentheses at all.
     */
    public static function getFirstOpener(File $phpcsFile, $stackPtr, $validOwners=[])
    {
        return self::nestedParensWalker($phpcsFile, $stackPtr, $validOwners, false);

    }//end getFirstOpener()


    /**
     * Retrieve the position of the closer to the first set of parentheses an arbitrary token
     * is wrapped in where the parentheses owner is within the set of valid owners.
     *
     * If no $validOwners are specified, the closer to the first set of parentheses surrounding
     * the token will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false StackPtr to the parentheses closer or false if the token does not have
     *                   parentheses owned by any of the valid owners or if the token is not
     *                   nested in parentheses at all.
     */
    public static function getFirstCloser(File $phpcsFile, $stackPtr, $validOwners=[])
    {
        $opener = self::getFirstOpener($phpcsFile, $stackPtr, $validOwners);
        $tokens = $phpcsFile->getTokens();
        if ($opener !== false && isset($tokens[$opener]['parenthesis_closer']) === true) {
            return $tokens[$opener]['parenthesis_closer'];
        }

        return false;

    }//end getFirstCloser()


    /**
     * Retrieve the position of the parentheses owner to the first set of parentheses an
     * arbitrary token is wrapped in where the parentheses owner is within the set of valid owners.
     *
     * If no $validOwners are specified, the owner to the first set of parentheses surrounding
     * the token will be returned or false if the first set of parentheses does not have an owner.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false StackPtr to the parentheses owner or false if the token does not have
     *                   parentheses owned by any of the valid owners or if the token is not
     *                   nested in parentheses at all.
     */
    public static function getFirstOwner(File $phpcsFile, $stackPtr, $validOwners=[])
    {
        $opener = self::getFirstOpener($phpcsFile, $stackPtr, $validOwners);
        if ($opener !== false) {
            return self::getOwner($phpcsFile, $opener);
        }

        return false;

    }//end getFirstOwner()


    /**
     * Retrieve the position of the opener to the last set of parentheses an arbitrary token
     * is wrapped in where the parentheses owner is within the set of valid owners.
     *
     * If no $validOwners are specified, the opener to the last set of parentheses surrounding
     * the token will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false StackPtr to the parentheses opener or false if the token does not have
     *                   parentheses owned by any of the valid owners or if the token is not
     *                   nested in parentheses at all.
     */
    public static function getLastOpener(File $phpcsFile, $stackPtr, $validOwners=[])
    {
        return self::nestedParensWalker($phpcsFile, $stackPtr, $validOwners, true);

    }//end getLastOpener()


    /**
     * Retrieve the position of the closer to the last set of parentheses an arbitrary token
     * is wrapped in where the parentheses owner is within the set of valid owners.
     *
     * If no $validOwners are specified, the closer to the last set of parentheses surrounding
     * the token will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false StackPtr to the parentheses closer or false if the token does not have
     *                   parentheses owned by any of the valid owners or if the token is not
     *                   nested in parentheses at all.
     */
    public static function getLastCloser(File $phpcsFile, $stackPtr, $validOwners=[])
    {
        $opener = self::getLastOpener($phpcsFile, $stackPtr, $validOwners);
        $tokens = $phpcsFile->getTokens();
        if ($opener !== false && isset($tokens[$opener]['parenthesis_closer']) === true) {
            return $tokens[$opener]['parenthesis_closer'];
        }

        return false;

    }//end getLastCloser()


    /**
     * Retrieve the position of the parentheses owner to the last set of parentheses an
     * arbitrary token is wrapped in where the parentheses owner is within the set of valid owners.
     *
     * If no $validOwners are specified, the owner to the last set of parentheses surrounding
     * the token will be returned or false if the last set of parentheses does not have an owner.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false StackPtr to the parentheses owner or false if the token does not have
     *                   parentheses owned by any of the valid owners or if the token is not
     *                   nested in parentheses at all.
     */
    public static function getLastOwner(File $phpcsFile, $stackPtr, $validOwners=[])
    {
        $opener = self::getLastOpener($phpcsFile, $stackPtr, $validOwners);
        if ($opener !== false) {
            return self::getOwner($phpcsFile, $opener);
        }

        return false;

    }//end getLastOwner()


    /**
     * Check whether the owner of a direct wrapping set of parentheses is within a limited set of
     * acceptable tokens.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position in the stack of the
     *                                                 token to verify.
     * @param int|string|array            $validOwners Array of token constants for the owners
     *                                                 which should be considered valid.
     *
     * @return int|false StackPtr to the valid parentheses owner or false if the token was not
     *                   wrapped in parentheses or if the last set of parentheses in which the
     *                   token is wrapped does not have an owner within the set of owners
     *                   considered valid.
     */
    public static function lastOwnerIn(File $phpcsFile, $stackPtr, $validOwners)
    {
        $opener = self::getLastOpener($phpcsFile, $stackPtr);

        if ($opener !== false && self::isOwnerIn($phpcsFile, $opener, $validOwners) === true) {
            return self::getOwner($phpcsFile, $opener);
        }

        return false;

    }//end lastOwnerIn()


    /**
     * Helper method. Retrieve the position of a parentheses opener for an arbitrary passed token.
     *
     * If no $validOwners are specified, the opener to the first set of parentheses surrounding
     * the token - or if $reverse=true, the last set of parentheses - will be returned.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the token we are checking.
     * @param int|string|array            $validOwners Optional. Array of token constants for the owners
     *                                                 which should be considered valid.
     * @param bool                        $reverse     Optional. Whether to search for the highest
     *                                                 (false) or the deepest set of parentheses (true)
     *                                                 with the specified owner(s).
     *
     * @return int|false StackPtr to the parentheses opener or false if the token does not have
     *                   parentheses owned by any of the valid owners or if the token is not
     *                   nested in parentheses at all.
     */
    private static function nestedParensWalker(File $phpcsFile, $stackPtr, $validOwners=[], $reverse=false)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token is nested in parenthesis.
        if (empty($tokens[$stackPtr]['nested_parenthesis']) === true) {
            return false;
        }

        $validOwners = (array) $validOwners;
        $parentheses = $tokens[$stackPtr]['nested_parenthesis'];

        if (empty($validOwners) === true) {
            // No owners specified, just return the first/last parentheses opener.
            if ($reverse === true) {
                end($parentheses);
            } else {
                reset($parentheses);
            }

            return key($parentheses);
        }

        if ($reverse === true) {
            $parentheses = array_reverse($parentheses, true);
        }

        foreach ($parentheses as $opener => $closer) {
            if (self::isOwnerIn($phpcsFile, $opener, $validOwners) === true) {
                // We found a token with a valid owner.
                return $opener;
            }
        }

        return false;

    }//end nestedParensWalker()


}//end class
