<?php
/**
 * Utility functions for checking the orthography of arbitrary text strings.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

class Orthography
{

    /**
     * Characters which are considered terminal points for a sentence.
     *
     * @link https://www.thepunctuationguide.com/terminal-points.html
     *
     * @var string
     */
    const TERMINAL_POINTS = '.?!';


    /**
     * Check if the first character of an arbitrary text string is a capital letter.
     *
     * Letter characters which do not have a concept of lower/uppercase will
     * be accepted as correctly capitalized.
     *
     * @param string $string The text string to examine.
     *                       This can be the contents of a text string token,
     *                       but also, for instance, a comment text.
     *                       Potential text delimiter quotes should be stripped
     *                       off a text string before passing it to this method.
     *
     * @return boolean True when the first character is a capital letter or a letter
     *                 which doesn't have a concept of capitalization.
     *                 False otherwise, including for non-letter characters.
     */
    public static function isFirstCharCapitalized($string)
    {
        $string = ltrim($string);
        return (preg_match('`^[\p{Lu}\p{Lt}\p{Lo}]`u', $string) > 0);

    }//end isFirstCharCapitalized()


    /**
     * Check if the first character of an arbitrary text string is a lowercase letter.
     *
     * @param string $string The text string to examine.
     *                       This can be the contents of a text string token,
     *                       but also, for instance, a comment text.
     *                       Potential text delimiter quotes should be stripped
     *                       off a text string before passing it to this method.
     *
     * @return boolean True when the first character is a lowercase letter.
     *                 False otherwise, including for letters which don't have a concept of
     *                 capitalization and for non-letter characters.
     */
    public static function isFirstCharLowercase($string)
    {
        $string = ltrim($string);
        return (preg_match('`^\p{Ll}`u', $string) > 0);

    }//end isFirstCharLowercase()


    /**
     * Check if the last character of an arbitrary text string is a valid punctuation character.
     *
     * @param string $string       The text string to examine.
     *                             This can be the contents of a text string token,
     *                             but also, for instance, a comment text.
     *                             Potential text delimiter quotes should be stripped
     *                             off a text string before passing it to this method.
     * @param string $allowedChars Characters which are considered valid punctuation
     *                             to end the text string.
     *                             Defaults to '.?!', i.e. a full stop, question mark
     *                             or exclamation mark.
     *
     * @return boolean
     */
    public static function isLastCharPunctuation($string, $allowedChars=self::TERMINAL_POINTS)
    {
        $string = rtrim($string);
        if (function_exists('iconv_substr') === true) {
            $lastChar = iconv_substr($string, -1);
        } else {
            $lastChar = substr($string, -1);
        }

        if (function_exists('iconv_strpos') === true) {
            return (iconv_strpos($allowedChars, $lastChar) !== false);
        } else {
            return (strpos($allowedChars, $lastChar) !== false);
        }

    }//end isLastCharPunctuation()


}//end class
