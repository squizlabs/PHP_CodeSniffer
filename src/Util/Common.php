<?php
/**
 * Basic util functions.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util;

class Common
{

    /**
     * An array of variable types for param/var we will check.
     *
     * @var string[]
     */
    public static $allowedTypes = [
        'array',
        'boolean',
        'float',
        'integer',
        'mixed',
        'object',
        'string',
        'resource',
        'callable',
    ];


    /**
     * Return TRUE if the path is a PHAR file.
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
     * Checks if a file is readable.
     *
     * Addresses PHP bug related to reading files from network drives on Windows.
     * e.g. when using WSL2.
     *
     * @param string $path The path to the file.
     *
     * @return boolean
     */
    public static function isReadable($path)
    {
        if (@is_readable($path) === true) {
            return true;
        }

        if (@file_exists($path) === true && @is_file($path) === true) {
            $f = @fopen($path, 'rb');
            if (fclose($f) === true) {
                return true;
            }
        }

        return false;

    }//end isReadable()


    /**
     * CodeSniffer alternative for realpath.
     *
     * Allows for PHAR support.
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

        // Check for process substitution.
        if (strpos($path, '/dev/fd') === 0) {
            return str_replace('/dev/fd', 'php://fd', $path);
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

        $phar  = \Phar::running(false);
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
     * Removes a base path from the front of a file path.
     *
     * @param string $path     The path of the file.
     * @param string $basepath The base path to remove. This should not end
     *                         with a directory separator.
     *
     * @return string
     */
    public static function stripBasepath($path, $basepath)
    {
        if (empty($basepath) === true) {
            return $path;
        }

        $basepathLen = strlen($basepath);
        if (substr($path, 0, $basepathLen) === $basepath) {
            $path = substr($path, $basepathLen);
        }

        $path = ltrim($path, DIRECTORY_SEPARATOR);
        if ($path === '') {
            $path = '.';
        }

        return $path;

    }//end stripBasepath()


    /**
     * Detects the EOL character being used in a string.
     *
     * @param string $contents The contents to check.
     *
     * @return string
     */
    public static function detectLineEndings($contents)
    {
        if (preg_match("/\r\n?|\n/", $contents, $matches) !== 1) {
            // Assume there are no newlines.
            $eolChar = "\n";
        } else {
            $eolChar = $matches[0];
        }

        return $eolChar;

    }//end detectLineEndings()


    /**
     * Check if STDIN is a TTY.
     *
     * @return boolean
     */
    public static function isStdinATTY()
    {
        // The check is slow (especially calling `tty`) so we static
        // cache the result.
        static $isTTY = null;

        if ($isTTY !== null) {
            return $isTTY;
        }

        if (defined('STDIN') === false) {
            return false;
        }

        // If PHP has the POSIX extensions we will use them.
        if (function_exists('posix_isatty') === true) {
            $isTTY = (posix_isatty(STDIN) === true);
            return $isTTY;
        }

        // Next try is detecting whether we have `tty` installed and use that.
        if (defined('PHP_WINDOWS_VERSION_PLATFORM') === true) {
            $devnull = 'NUL';
            $which   = 'where';
        } else {
            $devnull = '/dev/null';
            $which   = 'which';
        }

        $tty = trim(shell_exec("$which tty 2> $devnull"));
        if (empty($tty) === false) {
            exec("tty -s 2> $devnull", $output, $returnValue);
            $isTTY = ($returnValue === 0);
            return $isTTY;
        }

        // Finally we will use fstat.  The solution borrowed from
        // https://stackoverflow.com/questions/11327367/detect-if-a-php-script-is-being-run-interactively-or-not
        // This doesn't work on Mingw/Cygwin/... using Mintty but they
        // have `tty` installed.
        $type = [
            'S_IFMT'  => 0170000,
            'S_IFIFO' => 0010000,
        ];

        $stat  = fstat(STDIN);
        $mode  = ($stat['mode'] & $type['S_IFMT']);
        $isTTY = ($mode !== $type['S_IFIFO']);

        return $isTTY;

    }//end isStdinATTY()


    /**
     * Escape a path to a system command.
     *
     * @param string $cmd The path to the system command.
     *
     * @return string
     */
    public static function escapeshellcmd($cmd)
    {
        $cmd = escapeshellcmd($cmd);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Spaces are not escaped by escapeshellcmd on Windows, but need to be
            // for the command to be able to execute.
            $cmd = preg_replace('`(?<!^) `', '^ ', $cmd);
        }

        return $cmd;

    }//end escapeshellcmd()


    /**
     * Prepares token content for output to screen.
     *
     * Replaces invisible characters so they are visible. On non-Windows
     * operating systems it will also colour the invisible characters.
     *
     * @param string   $content The content to prepare.
     * @param string[] $exclude A list of characters to leave invisible.
     *                          Can contain \r, \n, \t and a space.
     *
     * @return string
     */
    public static function prepareForOutput($content, $exclude=[])
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (in_array("\r", $exclude, true) === false) {
                $content = str_replace("\r", '\r', $content);
            }

            if (in_array("\n", $exclude, true) === false) {
                $content = str_replace("\n", '\n', $content);
            }

            if (in_array("\t", $exclude, true) === false) {
                $content = str_replace("\t", '\t', $content);
            }
        } else {
            if (in_array("\r", $exclude, true) === false) {
                $content = str_replace("\r", "\033[30;1m\\r\033[0m", $content);
            }

            if (in_array("\n", $exclude, true) === false) {
                $content = str_replace("\n", "\033[30;1m\\n\033[0m", $content);
            }

            if (in_array("\t", $exclude, true) === false) {
                $content = str_replace("\t", "\033[30;1m\\t\033[0m", $content);
            }

            if (in_array(' ', $exclude, true) === false) {
                $content = str_replace(' ', "\033[30;1m·\033[0m", $content);
            }
        }//end if

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
                $ascii = ord($string[$i]);
                if ($ascii >= 48 && $ascii <= 57) {
                    // The character is a number, so it cant be a capital.
                    $isCaps = false;
                } else {
                    if (strtoupper($string[$i]) === $string[$i]) {
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

                if ($bit[0] !== strtoupper($bit[0])) {
                    $validName = false;
                    break;
                }
            }
        }

        return $validName;

    }//end isUnderscoreName()


    /**
     * Returns a valid variable type for param/var tags.
     *
     * If type is not one of the standard types, it must be a custom type.
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

        if (in_array($varType, self::$allowedTypes, true) === true) {
            return $varType;
        } else {
            $lowerVarType = strtolower($varType);
            switch ($lowerVarType) {
            case 'bool':
            case 'boolean':
                return 'boolean';
            case 'double':
            case 'real':
            case 'float':
                return 'float';
            case 'int':
            case 'integer':
                return 'integer';
            case 'array()':
            case 'array':
                return 'array';
            }//end switch

            if (strpos($lowerVarType, 'array(') !== false) {
                // Valid array declaration:
                // array, array(type), array(type1 => type2).
                $matches = [];
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
            } else if (in_array($lowerVarType, self::$allowedTypes, true) === true) {
                // A valid type, but not lower cased.
                return $lowerVarType;
            } else {
                // Must be a custom type name.
                return $varType;
            }//end if
        }//end if

    }//end suggestType()


    /**
     * Given a sniff class name, returns the code for the sniff.
     *
     * @param string $sniffClass The fully qualified sniff class name.
     *
     * @return string
     */
    public static function getSniffCode($sniffClass)
    {
        $parts = explode('\\', $sniffClass);
        $sniff = array_pop($parts);

        if (substr($sniff, -5) === 'Sniff') {
            // Sniff class name.
            $sniff = substr($sniff, 0, -5);
        } else {
            // Unit test class name.
            $sniff = substr($sniff, 0, -8);
        }

        $category = array_pop($parts);
        $sniffDir = array_pop($parts);
        $standard = array_pop($parts);
        $code     = $standard.'.'.$category.'.'.$sniff;
        return $code;

    }//end getSniffCode()


    /**
     * Removes project-specific information from a sniff class name.
     *
     * @param string $sniffClass The fully qualified sniff class name.
     *
     * @return string
     */
    public static function cleanSniffClass($sniffClass)
    {
        $newName = strtolower($sniffClass);

        $sniffPos = strrpos($newName, '\sniffs\\');
        if ($sniffPos === false) {
            // Nothing we can do as it isn't in a known format.
            return $newName;
        }

        $end   = (strlen($newName) - $sniffPos + 1);
        $start = strrpos($newName, '\\', ($end * -1));

        if ($start === false) {
            // Nothing needs to be cleaned.
            return $newName;
        }

        $newName = substr($newName, ($start + 1));
        return $newName;

    }//end cleanSniffClass()


}//end class
