<?php

namespace PHP_CodeSniffer\Util;

use PHP_CodeSniffer\Autoload;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Util\Common;

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
class Cache
{

    private static $path  = '';
    private static $cache = array();


    public static function load(Config $config, Ruleset $ruleset)
    {
        // Look at every loaded sniff class so far and use their file contents
        // to generate a hash for the code used during the run.
        // At this point, the loaded class list contains the core PHPCS code
        // and all sniffs that have been loaded as part of the run.
        $codeHash = '';
        $classes  = array_keys(Autoload::getLoadedClasses());
        sort($classes);

        $installDir     = dirname(__DIR__);
        $installDirLen  = strlen($installDir);
        $standardDir    = $installDir.DIRECTORY_SEPARATOR.'Standards';
        $standardDirLen = strlen($standardDir);
        foreach ($classes as $file) {
            if (substr($file, 0, $standardDirLen) !== $standardDir) {
                if (substr($file, 0, $installDirLen) === $installDir) {
                    // We are only interested in sniffs here.
                    continue;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo PHP_EOL."\t=> including external file in code hash: ".$file;
                }
            } else if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL."\t=> including internal sniff file in code hash: ".$file;
            }

            $codeHash .= md5_file($file);
        }

        // Go through the core PHPCS code and add those files to the file
        // hash. This ensure that core PHPCS changes will also invalidate the cache.
        // Note that we ignore sniffs here, and any files that don't affect
        // the outcome of the run.
        $ignore = array(
                   'Standards'  => true,
                   'Exceptions' => true,
                   'Reports'    => true,
                   'Generators' => true,
                  );

        $di = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($installDir),
            0,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        foreach ($di as $file) {
            // Skip hidden files.
            $filename = $file->getFilename();
            if (substr($filename, 0, 1) === '.') {
                continue;
            }

            $filePath = Common::realpath($file->getPathname());
            if ($filePath === false) {
                continue;
            }

            if (is_dir($filePath) === true) {
                continue;
            }

            $dir = substr($filePath, ($installDirLen + 1));
            $dir = substr($dir, 0, strpos($dir, DIRECTORY_SEPARATOR));
            if (isset($ignore[$dir]) === true) {
                continue;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL."\t=> including core file in code hash: ".$file;
            }

            $codeHash .= md5_file($file);
        }//end foreach

        $codeHash = md5($codeHash);

        // Along with the code hash, use various settings that can affect
        // the results of a run to create a new hash. This hash will be used
        // in the cache file name.
        $configData = array(
                       'tabWidth' => $config->tabWidth,
                       'encoding' => $config->encoding,
                       'codeHash' => $codeHash,
                      );

        $configString = implode(',', $configData);
        $hash         = substr(sha1($configString), 0, 12);

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo PHP_EOL."\t** Cache key data generated **".PHP_EOL;
            echo "\t\t=> tabWidth: ".$configData['tabWidth'].PHP_EOL;
            echo "\t\t=> encoding: ".$configData['encoding'].PHP_EOL;
            echo "\t\t=> codeHash: ".$configData['codeHash'].PHP_EOL;
            echo "\t\t=> cacheHash: $hash".PHP_EOL;
        }

        self::$path = getcwd().DIRECTORY_SEPARATOR.".phpcs.$hash.cache";
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t** Using cache file \"".self::$path.'" **'.PHP_EOL;
        }

        if (file_exists(self::$path) === true) {
            self::$cache = json_decode(file_get_contents(self::$path), true);
        } else if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t** Cache file does not exist **".PHP_EOL;
        }

        self::$cache['config'] = $configData;

    }//end load()


    public static function save()
    {
        file_put_contents(self::$path, json_encode(self::$cache));

    }//end save()


    public static function get($key)
    {
        if (isset(self::$cache[$key]) === true) {
            return self::$cache[$key];
        }

        return false;

    }//end get()


    public static function set($key, $value)
    {
        self::$cache[$key] = $value;

    }//end set()


    public static function getSize()
    {
        return (count(self::$cache) - 1);

    }//end getSize()


}//end class
