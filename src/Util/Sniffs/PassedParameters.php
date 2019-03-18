<?php
/**
 * Utility functions to retrieve information about parameters passed to function calls,
 * array declarations, list, isset and unset constructs.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2016-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class PassedParameters
{


    /**
     * The token types these methods can handle.
     *
     * @var array
     */
    private static $allowedConstructs = [
        T_STRING              => true,
        T_VARIABLE            => true,
        T_SELF                => true,
        T_STATIC              => true,
        T_CLOSE_CURLY_BRACKET => true,
        T_CLOSE_PARENTHESIS   => true,
        T_ARRAY               => true,
        T_OPEN_SHORT_ARRAY    => true,
        T_ISSET               => true,
        T_UNSET               => true,
        T_LIST                => true,
    ];

    /**
     * Tokens which are considered stop point, either because they are the end
     * of the parameter (comma) or because we need to skip over them.
     *
     * @var array
     */
    private static $callParsingStopPoints = [
        T_COMMA            => T_COMMA,
        T_ARRAY            => T_ARRAY,
        T_OPEN_SHORT_ARRAY => T_OPEN_SHORT_ARRAY,
        T_CLOSURE          => T_CLOSURE,
        T_ANON_CLASS       => T_ANON_CLASS,
    ];


    /**
     * Checks if any parameters have been passed.
     *
     * Expects to be passed the T_STRING or T_VARIABLE stack pointer for a function call.
     * If passed a T_STRING which is *not* a function call, the behaviour is unreliable.
     *
     * Extra features:
     * - If passed a T_SELF or T_STATIC stack pointer, it will accept it as a
     *   function call when used like `new self()`.
     * - A T_CLOSE_CURLY_BRACKET and a T_CLOSE_PARENTHESIS stack pointer will be
     *   checked as a function call.
     * - If passed a T_ARRAY or T_OPEN_SHORT_ARRAY stack pointer, it will detect
     *   whether the array (or short list) has values or is empty.
     * - If passed a T_ISSET, T_UNSET or T_LIST stack pointer, it will detect whether
     *   those language constructs have "parameters".
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the T_STRING, T_VARIABLE, T_ARRAY,
     *                                               T_OPEN_SHORT_ARRAY, T_ISSET, T_UNSET or T_LIST token.
     *
     * @return bool
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types.
     */
    public static function hasParameters(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this one of the tokens this function handles ?
        if (isset(self::$allowedConstructs[$tokens[$stackPtr]['code']]) === false) {
            throw new RuntimeException(
                'The hasParameters() method expects a function call, array, list, isset or unset token to be passed. Received "'.$tokens[$stackPtr]['type'].'" instead'
            );
        }

        // Weed out some of the obvious non-function calls.
        if ($tokens[$stackPtr]['code'] === T_SELF || $tokens[$stackPtr]['code'] === T_STATIC) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prev]['code'] !== T_NEW) {
                throw new RuntimeException(
                    'The hasParameters() method expects a function call, array, list, isset or unset token to be passed. Received "'.$tokens[$stackPtr]['type'].'" instead'
                );
            }
        } else if ($tokens[$stackPtr]['code'] === T_CLOSE_CURLY_BRACKET
            && isset($tokens[$stackPtr]['scope_condition']) === true
        ) {
            throw new RuntimeException(
                'The hasParameters() method expects a function call, array, list, isset or unset token to be passed. Received a "'.$tokens[$stackPtr]['type'].'" which is not function call'
            );
        } else if ($tokens[$stackPtr]['code'] === T_CLOSE_PARENTHESIS
            && isset($tokens[$stackPtr]['parenthesis_owner']) === true
        ) {
            throw new RuntimeException(
                'The hasParameters() method expects a function call, array, list, isset or unset token to be passed. Received a "'.$tokens[$stackPtr]['type'].'" which is not function call'
            );
        }//end if

        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);

        if ($nextNonEmpty === false) {
            return false;
        }

        // Deal with short array and short list syntax.
        if ($tokens[$stackPtr]['code'] === T_OPEN_SHORT_ARRAY) {
            if ($nextNonEmpty === $tokens[$stackPtr]['bracket_closer']) {
                // No parameters.
                return false;
            } else {
                return true;
            }
        }

        // Deal with function calls, long arrays, long lists, isset and unset.
        // Next non-empty token should be the open parenthesis.
        if ($tokens[$nextNonEmpty]['code'] !== T_OPEN_PARENTHESIS) {
            return false;
        }

        if (isset($tokens[$nextNonEmpty]['parenthesis_closer']) === false) {
            return false;
        }

        $closeParenthesis = $tokens[$nextNonEmpty]['parenthesis_closer'];
        $nextNextNonEmpty = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            ($nextNonEmpty + 1),
            ($closeParenthesis + 1),
            true
        );

        if ($nextNextNonEmpty === $closeParenthesis) {
            // No parameters.
            return false;
        }

        return true;

    }//end hasParameters()


    /**
     * Get information on all parameters passed to a function call.
     *
     * Expects to be passed the T_STRING or T_VARIABLE stack pointer for the function call.
     * If passed a T_STRING which is *not* a function call, the behaviour is unreliable.
     *
     * Will return an multi-dimentional array with the start token pointer, end token
     * pointer and raw parameter value for all parameters. Index will be 1-based.
     * If no parameters are found, will return an empty array.
     *
     * Extra feature: If passed an T_ARRAY or T_OPEN_SHORT_ARRAY stack pointer,
     * it will tokenize the values / key/value pairs contained in the array call.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the T_STRING, T_VARIABLE, T_ARRAY,
     *                                               T_OPEN_SHORT_ARRAY, T_ISSET, T_UNSET or T_LIST token.
     *
     * @return array
     */
    public static function getParameters(File $phpcsFile, $stackPtr)
    {
        if (self::hasParameters($phpcsFile, $stackPtr) === false) {
            return [];
        }

        // Ok, we know we have a valid token with parameters and valid open & close brackets/parenthesis.
        $tokens = $phpcsFile->getTokens();

        // Mark the beginning and end tokens.
        if ($tokens[$stackPtr]['code'] === T_OPEN_SHORT_ARRAY) {
            $opener = $stackPtr;
            $closer = $tokens[$stackPtr]['bracket_closer'];

            $nestedParenthesisCount = 0;
        } else {
            $opener = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);
            $closer = $tokens[$opener]['parenthesis_closer'];

            $nestedParenthesisCount = 1;
        }

        // Which nesting level is the one we are interested in ?
        if (isset($tokens[$opener]['nested_parenthesis']) === true) {
            $nestedParenthesisCount += count($tokens[$opener]['nested_parenthesis']);
        }

        $parameters   = [];
        $nextComma    = $opener;
        $paramStart   = ($opener + 1);
        $cnt          = 1;
        $stopPoints   = self::$callParsingStopPoints;
        $stopPoints[] = $tokens[$closer]['code'];

        while (($nextComma = $phpcsFile->findNext($stopPoints, ($nextComma + 1), ($closer + 1))) !== false) {
            // Ignore anything within short array definition brackets.
            if ($tokens[$nextComma]['code'] === T_OPEN_SHORT_ARRAY) {
                // Skip forward to the end of the short array definition.
                $nextComma = $tokens[$nextComma]['bracket_closer'];
                continue;
            }

            // Skip past nested arrays.
            if ($tokens[$nextComma]['code'] === T_ARRAY
                && isset($tokens[$nextComma]['parenthesis_opener'], $tokens[$tokens[$nextComma]['parenthesis_opener']]['parenthesis_closer']) === true
            ) {
                $nextComma = $tokens[$tokens[$nextComma]['parenthesis_opener']]['parenthesis_closer'];
                continue;
            }

            // Skip past closures and anonymous classes passed as function parameters.
            if (($tokens[$nextComma]['code'] === T_CLOSURE
                || $tokens[$nextComma]['code'] === T_ANON_CLASS)
                && (isset($tokens[$nextComma]['scope_condition']) === true
                && $tokens[$nextComma]['scope_condition'] === $nextComma)
                && isset($tokens[$nextComma]['scope_closer']) === true
            ) {
                // Skip forward to the end of the closure/anonymous class declaration.
                $nextComma = $tokens[$nextComma]['scope_closer'];
                continue;
            }

            // Ignore comma's at a lower nesting level.
            if ($tokens[$nextComma]['code'] === T_COMMA
                && isset($tokens[$nextComma]['nested_parenthesis']) === true
                && count($tokens[$nextComma]['nested_parenthesis']) !== $nestedParenthesisCount
            ) {
                continue;
            }

            // Ignore closing parenthesis/bracket if not 'ours'.
            if ($tokens[$nextComma]['code'] === $tokens[$closer]['code'] && $nextComma !== $closer) {
                continue;
            }

            // Ok, we've reached the end of the parameter.
            $parameters[$cnt]['start'] = $paramStart;
            $parameters[$cnt]['end']   = ($nextComma - 1);
            $parameters[$cnt]['raw']   = trim($phpcsFile->getTokensAsString($paramStart, ($nextComma - $paramStart)));

            // Check if there are more tokens before the closing parenthesis.
            // Prevents function calls with trailing comma's from setting an extra parameter:
            // `functionCall( $param1, $param2, );`.
            $hasNextParam = $phpcsFile->findNext(
                Tokens::$emptyTokens,
                ($nextComma + 1),
                $closer,
                true,
                null,
                true
            );
            if ($hasNextParam === false) {
                break;
            }

            // Prepare for the next parameter.
            $paramStart = ($nextComma + 1);
            $cnt++;
        }//end while

        return $parameters;

    }//end getParameters()


    /**
     * Get information on a specific parameter passed.
     *
     * Expects to be passed the T_STRING or T_VARIABLE stack pointer for the function call.
     * If passed a T_STRING which is *not* a function call, the behaviour is unreliable.
     *
     * Will return a array with the start token pointer, end token pointer and the raw value
     * of the parameter at a specific offset.
     * If the specified parameter is not found, will return false.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    The position of the T_STRING, T_VARIABLE, T_ARRAY,
     *                                                 T_OPEN_SHORT_ARRAY, T_ISSET, T_UNSET or T_LIST token.
     * @param int                         $paramOffset The 1-based index position of the parameter to retrieve.
     *
     * @return array|false
     */
    public static function getParameter(File $phpcsFile, $stackPtr, $paramOffset)
    {
        $parameters = self::getParameters($phpcsFile, $stackPtr);

        if (isset($parameters[$paramOffset]) === false) {
            return false;
        }

        return $parameters[$paramOffset];

    }//end getParameter()


    /**
     * Count the number of parameters which have been passed.
     *
     * Expects to be passed the T_STRING or T_VARIABLE stack pointer for the function call.
     * If passed a T_STRING which is *not* a function call, the behaviour is unreliable.
     *
     * Extra feature: If passed an T_ARRAY or T_OPEN_SHORT_ARRAY stack pointer,
     * it will return the number of values in the array.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position of the T_STRING, T_VARIABLE, T_ARRAY,
     *                                               T_OPEN_SHORT_ARRAY, T_ISSET, T_UNSET or T_LIST token.
     *
     * @return int
     */
    public static function getParameterCount(File $phpcsFile, $stackPtr)
    {
        if (self::hasParameters($phpcsFile, $stackPtr) === false) {
            return 0;
        }

        return count(self::getParameters($phpcsFile, $stackPtr));

    }//end getParameterCount()


}//end class
