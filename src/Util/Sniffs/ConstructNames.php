<?php
/**
 * Utility functions to examine construct names, such as function names, object names
 * and variable names.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ConstructNames
{


    /**
     * Returns the declaration names for classes, interfaces, traits, and functions.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the declaration token which
     *                                               declared the class, interface, trait, or function.
     *
     * @return string|null The name of the class, interface, trait, or function;
     *                     NULL if the function or class is anonymous; or
     *                     an empty string in case of a parse error/live coding.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      T_FUNCTION, T_CLASS, T_TRAIT, or T_INTERFACE.
     */
    public static function getDeclarationName(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $tokenCode = $tokens[$stackPtr]['code'];

        if ($tokenCode === T_ANON_CLASS || $tokenCode === T_CLOSURE) {
            return null;
        }

        if ($tokenCode !== T_FUNCTION
            && $tokenCode !== T_CLASS
            && $tokenCode !== T_INTERFACE
            && $tokenCode !== T_TRAIT
        ) {
            throw new RuntimeException('Token type "'.$tokens[$stackPtr]['type'].'" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');
        }

        if ($tokenCode === T_FUNCTION
            && strtolower($tokens[$stackPtr]['content']) !== 'function'
        ) {
            // JS specific: This is a function declared without the "function" keyword.
            // So this token is the function name.
            return $tokens[$stackPtr]['content'];
        }

        /*
         * Determine the name. Note that we cannot simply look for the first T_STRING
         * because an (invalid) class name starting with the number will be multiple tokens.
         * Whitespace or comment are however not allowed within a name.
         */

        if ($tokenCode === T_FUNCTION && isset($tokens[$stackPtr]['parenthesis_opener']) === true) {
            $opener = $tokens[$stackPtr]['parenthesis_opener'];
        } else if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $opener = $tokens[$stackPtr]['scope_opener'];
        }

        if (isset($opener) === false) {
            // Live coding or parse error.
            return '';
        }

        $nameStart = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), $opener, true);
        if ($nameStart === false) {
            // Live coding or parse error.
            return '';
        }

        $nameEnd = $phpcsFile->findNext(Tokens::$emptyTokens, $nameStart, $opener);
        if ($nameEnd === false) {
            return $tokens[$nameStart]['content'];
        }

        // Name starts with number, so is composed of multiple tokens.
        return trim($phpcsFile->getTokensAsString($nameStart, ($nameEnd - $nameStart)));

    }//end getDeclarationName()


    /**
     * Returns true if the specified string is in the camel caps format.
     *
     * @param string  $string      The string the verify.
     * @param boolean $classFormat If true, check to see if the string is in the
     *                             class format. Class format strings must start
     *                             with a capital letter and contain no
     *                             underscores.
     * @param boolean $public      If true, the first character in the string
     *                             must be an a-z character. If false, the
     *                             character must be an underscore. This
     *                             argument is only applicable if $classFormat
     *                             is false.
     * @param boolean $strict      If true, the string must not have two capital
     *                             letters next to each other. If false, a
     *                             relaxed camel caps policy is used to allow
     *                             for acronyms.
     *
     * @return boolean
     */
    public static function isCamelCaps(
        $string,
        $classFormat=false,
        $public=true,
        $strict=true
    ) {
        // Check the first character first.
        if ($classFormat === false) {
            $legalFirstChar = '';
            if ($public === false) {
                $legalFirstChar = '[_]';
            }

            if ($strict === false) {
                // Can either start with a lowercase letter, or multiple uppercase
                // in a row, representing an acronym.
                $legalFirstChar .= '([A-Z]{2,}|[a-z])';
            } else {
                $legalFirstChar .= '[a-z]';
            }
        } else {
            $legalFirstChar = '[A-Z]';
        }

        if (preg_match("/^$legalFirstChar/", $string) === 0) {
            return false;
        }

        // Check that the name only contains legal characters.
        $legalChars = 'a-zA-Z0-9';
        if (preg_match("|[^$legalChars]|", substr($string, 1)) > 0) {
            return false;
        }

        if ($strict === true) {
            // Check that there are not two capital letters next to each other.
            $length          = strlen($string);
            $lastCharWasCaps = $classFormat;

            for ($i = 1; $i < $length; $i++) {
                $ascii = ord($string{$i});
                if ($ascii >= 48 && $ascii <= 57) {
                    // The character is a number, so it cant be a capital.
                    $isCaps = false;
                } else {
                    if (strtoupper($string{$i}) === $string{$i}) {
                        $isCaps = true;
                    } else {
                        $isCaps = false;
                    }
                }

                if ($isCaps === true && $lastCharWasCaps === true) {
                    return false;
                }

                $lastCharWasCaps = $isCaps;
            }
        }//end if

        return true;

    }//end isCamelCaps()


    /**
     * Returns true if the specified string is in the underscore caps format.
     *
     * @param string $string The string to verify.
     *
     * @return boolean
     */
    public static function isUnderscoreName($string)
    {
        // If there is a space in the name, it can't be valid.
        if (strpos($string, ' ') !== false) {
            return false;
        }

        $validName = true;
        $nameBits  = explode('_', $string);

        if (preg_match('|^[A-Z]|', $string) === 0) {
            // Name does not begin with a capital letter.
            $validName = false;
        } else {
            foreach ($nameBits as $bit) {
                if ($bit === '') {
                    continue;
                }

                if ($bit{0} !== strtoupper($bit{0})) {
                    $validName = false;
                    break;
                }
            }
        }

        return $validName;

    }//end isUnderscoreName()


    /**
     * Verify whether a name contains numeric characters.
     *
     * @param string $name The string.
     *
     * @return bool
     */
    public static function hasNumbers($name)
    {
        if ($name === '') {
            return false;
        }

        return preg_match('`\pN`u', $name) === 1;

    }//end hasNumbers()


    /**
     * Remove numeric characters from the start of a string.
     *
     * @param string $name The string.
     *
     * @return string
     */
    public static function ltrimNumbers($name)
    {
        if ($name === '') {
            return '';
        }

        return preg_replace('`^[\pN]+(\X*)`u', '$1', $name);

    }//end ltrimNumbers()


    /**
     * Remove all numeric characters from a string.
     *
     * @param string $name The string.
     *
     * @return string
     */
    public static function removeNumbers($name)
    {
        if ($name === '') {
            return '';
        }

        return preg_replace('`[\pN]+`u', '', $name);

    }//end removeNumbers()


    /**
     * Transform consecutive uppercase characters to lowercase.
     *
     * Important: this function will only work on non-ascii strings when the MBString
     * extension is enabled.
     *
     * @param string $name The string.
     *
     * @return string The adjusted name or the original name if no consecutive uppercase
     *                characters where found or when MBString is not available and the input
     *                was non-ascii.
     */
    public static function lowerConsecutiveCaps($name)
    {
        static $mbstring = null, $encoding = null;

        if ($name === '') {
            return '';
        }

        // Cache the results of MbString check and encoding. These values won't change during a run.
        if (isset($mbstring) === false) {
            $mbstring = function_exists('mb_strtolower');
        }

        if (isset($encoding) === false) {
            $encoding = Config::getConfigData('encoding');
            if ($encoding === null) {
                $encoding = 'utf-8';
            }
        }

        // MBString can mangle non-ascii text when the encoding is not correctly set and
        // strtolower will mangle any non-ascii, so just return the name unchanged in that case.
        if (utf8_decode($name) !== $name && $mbstring === false) {
            return $name;
        }

        $name = preg_replace_callback(
            '`([\p{Lt}\p{Lu}])([\p{Lt}\p{Lu}]+?)(\b|$|\PL|[\p{Lt}\p{Lu}](?=[^\p{Lt}\p{Lu}])\pL|(?=[^\p{Lt}\p{Lu}])\pL)`u',
            function ($matches) use ($mbstring, $encoding) {
                if ($mbstring === true) {
                    $consecutiveChars = mb_strtolower($matches[2], $encoding);
                } else {
                    $consecutiveChars = strtolower($matches[2]);
                }

                return $matches[1].$consecutiveChars.$matches[3];
            },
            $name
        );

        return $name;

    }//end lowerConsecutiveCaps()


}//end class
