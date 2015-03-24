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
    public static function detectLineEndings($file, $contents=null)
    {
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
            if (preg_match("/\r\n?|\n/", $contents, $matches) !== 1) {
                // Assuming there are no newlines.
                $eolChar = "\n";
            } else {
                $eolChar = $matches[0];
            }
        }//end if

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

}