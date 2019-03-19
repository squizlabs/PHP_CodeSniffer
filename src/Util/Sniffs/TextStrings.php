<?php
/**
 * Utility functions for working with text string tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;

class TextStrings
{


    /**
     * Get the complete contents of a multi-line text string.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    Pointer to the first text string token
     *                                                 of a multi-line text string or to a
     *                                                 Nowdoc/Heredoc opener.
     * @param bool                        $stripQuotes Optional. Whether to strip text delimiter
     *                                                 quotes off the resulting text string.
     *                                                 Defaults to true.
     *
     * @return string
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      valid text string token or if the
     *                                                      token is not the first text string token.
     */
    public static function getCompleteTextString(File $phpcsFile, $stackPtr, $stripQuotes=true)
    {
        $tokens = $phpcsFile->getTokens();

        // Must be the start of a text string token.
        if ($tokens[$stackPtr]['code'] !== T_START_HEREDOC
            && $tokens[$stackPtr]['code'] !== T_START_NOWDOC
            && $tokens[$stackPtr]['code'] !== T_CONSTANT_ENCAPSED_STRING
            && $tokens[$stackPtr]['code'] !== T_DOUBLE_QUOTED_STRING
        ) {
            throw new RuntimeException('$stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING or T_DOUBLE_QUOTED_STRING');
        }

        if ($tokens[$stackPtr]['code'] === T_CONSTANT_ENCAPSED_STRING
            || $tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING
        ) {
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($tokens[$stackPtr]['code'] === $tokens[$prev]['code']) {
                throw new RuntimeException('$stackPtr must be the start of the text string');
            }
        }

        switch ($tokens[$stackPtr]['code']) {
        case T_START_HEREDOC:
            $stripQuotes = false;
            $targetType  = T_HEREDOC;
            $current     = ($stackPtr + 1);
            break;

        case T_START_NOWDOC:
            $stripQuotes = false;
            $targetType  = T_NOWDOC;
            $current     = ($stackPtr + 1);
            break;

        default:
            $targetType = $tokens[$stackPtr]['code'];
            $current    = $stackPtr;
            break;
        }

        $string = '';
        do {
            $string .= $tokens[$current]['content'];
            ++$current;
        } while ($tokens[$current]['code'] === $targetType);

        if ($stripQuotes === true) {
            return self::stripQuotes($string);
        }

        return $string;

    }//end getCompleteTextString()


    /**
     * Strip text delimiter quotes from an arbitrary string.
     *
     * Intended for use with the "contents" of a T_CONSTANT_ENCAPSED_STRING / T_DOUBLE_QUOTED_STRING.
     *
     * Prevents stripping mis-matched quotes.
     * Prevents stripping quotes from the textual content of the string.
     *
     * @param string $string The raw string.
     *
     * @return string String without quotes around it.
     */
    public static function stripQuotes($string)
    {
        return preg_replace('`^([\'"])(.*)\1$`Ds', '$2', $string);

    }//end stripQuotes()


}//end class
