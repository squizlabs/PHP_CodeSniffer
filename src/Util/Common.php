<?php

namespace PHP_CodeSniffer\Util;

use PHP_CodeSniffer\Exceptions\RuntimeException;

/**
 * A class to process command line phpcs scripts.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * A class to process command line phpcs scripts.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Common
{

    /**
     * An array of variable types for param/var we will check.
     *
     * @var array(string)
     */
    public static $allowedTypes = array(
                                   'array',
                                   'boolean',
                                   'float',
                                   'integer',
                                   'mixed',
                                   'object',
                                   'string',
                                   'resource',
                                   'callable',
                                  );


    /**
     * Return TRUE, if the path is a phar file.
     *
     * @param string $path The path to use.
     *
     * @return mixed
     */
    public static function isPharFile($path)
    {
        if (strpos($path, 'phar://') === 0) {
            return true;
        }

        return false;

    }//end isPharFile()


    /**
     * CodeSniffer alternative for realpath.
     *
     * Allows for phar support.
     *
     * @param string $path The path to use.
     *
     * @return mixed
     */
    public static function realpath($path)
    {
        // Support the path replacement of ~ with the user's home directory.
        if (substr($path, 0, 2) === '~/') {
            $homeDir = getenv('HOME');
            if ($homeDir !== false) {
                $path = $homeDir.substr($path, 1);
            }
        }

        // No extra work needed if this is not a phar file.
        if (self::isPharFile($path) === false) {
            return realpath($path);
        }

        // Before trying to break down the file path,
        // check if it exists first because it will mostly not
        // change after running the below code.
        if (file_exists($path) === true) {
            return $path;
        }

        $phar  = Phar::running(false);
        $extra = str_replace('phar://'.$phar, '', $path);
        $path  = realpath($phar);
        if ($path === false) {
            return false;
        }

        $path = 'phar://'.$path.$extra;
        if (file_exists($path) === true) {
            return $path;
        }

        return false;

    }//end realpath()


    /**
     * Opens a file and detects the EOL character being used.
     *
     * @param string $file     The full path to the file.
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return string
     * @throws RuntimeException If $file could not be opened.
     */
    public static function detectLineEndings($contents)
    {
        /*
            if ($contents === null) {
            // Determine the newline character being used in this file.
            // Will be either \r, \r\n or \n.
            if (is_readable($file) === false) {
                $error = 'Error opening file; file no longer exists or you do not have access to read the file';
                throw new RuntimeException($error);
            } else {
                $handle = fopen($file, 'r');
                if ($handle === false) {
                    $error = 'Error opening file; could not auto-detect line endings';
                    throw new RuntimeException($error);
                }
            }

            $firstLine = fgets($handle);
            fclose($handle);

            $eolChar = substr($firstLine, -1);
            if ($eolChar === "\n") {
                $secondLastChar = substr($firstLine, -2, 1);
                if ($secondLastChar === "\r") {
                    $eolChar = "\r\n";
                }
            } else if ($eolChar !== "\r") {
                // Must not be an EOL char at the end of the line.
                // Probably a one-line file, so assume \n as it really
                // doesn't matter considering there are no newlines.
                $eolChar = "\n";
            }
            } else {
            */
        if (preg_match("/\r\n?|\n/", $contents, $matches) !== 1) {
            // Assuming there are no newlines.
            $eolChar = "\n";
        } else {
            $eolChar = $matches[0];
        }

        // }//end if
        return $eolChar;

    }//end detectLineEndings()


    /**
     * Prepares token content for output to screen.
     *
     * Replaces invisible characters so they are visible. On non-Windows
     * OSes it will also colour the invisible characters.
     *
     * @param string $content The content to prepare.
     *
     * @return string
     */
    public static function prepareForOutput($content)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $content = str_replace("\r", '\r', $content);
            $content = str_replace("\n", '\n', $content);
            $content = str_replace("\t", '\t', $content);
        } else {
            $content = str_replace("\r", "\033[30;1m\\r\033[0m", $content);
            $content = str_replace("\n", "\033[30;1m\\n\033[0m", $content);
            $content = str_replace("\t", "\033[30;1m\\t\033[0m", $content);
            $content = str_replace(' ', "\033[30;1m·\033[0m", $content);
        }

        return $content;

    }//end prepareForOutput()


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
        // If there are space in the name, it can't be valid.
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
     * Returns a valid variable type for param/var tag.
     *
     * If type is not one of the standard type, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     *
     * @return string
     */
    public static function suggestType($varType)
    {
        if ($varType === '') {
            return '';
        }

        if (in_array($varType, self::$allowedTypes) === true) {
            return $varType;
        } else {
            $lowerVarType = strtolower($varType);
            switch ($lowerVarType) {
            case 'bool':
                return 'boolean';
            case 'double':
            case 'real':
                return 'float';
            case 'int':
                return 'integer';
            case 'array()':
                return 'array';
            }//end switch

            if (strpos($lowerVarType, 'array(') !== false) {
                // Valid array declaration:
                // array, array(type), array(type1 => type2).
                $matches = array();
                $pattern = '/^array\(\s*([^\s^=^>]*)(\s*=>\s*(.*))?\s*\)/i';
                if (preg_match($pattern, $varType, $matches) !== 0) {
                    $type1 = '';
                    if (isset($matches[1]) === true) {
                        $type1 = $matches[1];
                    }

                    $type2 = '';
                    if (isset($matches[3]) === true) {
                        $type2 = $matches[3];
                    }

                    $type1 = self::suggestType($type1);
                    $type2 = self::suggestType($type2);
                    if ($type2 !== '') {
                        $type2 = ' => '.$type2;
                    }

                    return "array($type1$type2)";
                } else {
                    return 'array';
                }//end if
            } else if (in_array($lowerVarType, self::$allowedTypes) === true) {
                // A valid type, but not lower cased.
                return $lowerVarType;
            } else {
                // Must be a custom type name.
                return $varType;
            }//end if
        }//end if

    }//end suggestType()


    public static function getSniffCode($sniffClass)
    {
        $parts    = explode('\\', $sniffClass);
        $sniff    = substr(array_pop($parts), 0, -5);
        $category = array_pop($parts);
        $sniffDir = array_pop($parts);
        $standard = array_pop($parts);
        $code     = $standard.'.'.$category.'.'.$sniff;
        return $code;

    }//end getSniffCode()


}//end class
