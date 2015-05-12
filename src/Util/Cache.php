<?php

namespace PHP_CodeSniffer\Util;

use PHP_CodeSniffer\Autoload;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;

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
        // Look at every loaded class so far and use their file contents
        // to generate a hash for the code used during the run.
        // At this point, the loaded class list contains the core PHPCS code
        // and all sniffs that have been loaded as part of the run.
        $codeHash = '';
        $classes  = array_keys(Autoload::getLoadedClasses());
        sort($classes);
        foreach ($classes as $file) {
            $codeHash .= md5_file($file);
        }

        $codeHash = md5($codeHash);

        // Along with the code hash, use various settings that can affect
        // the results of a run to create a new hash. This hash will be used
        // in the cache file name.
        $configData = array(
                       'tabWidth'        => $config->tabWidth,
                       'encoding'        => $config->encoding,
                       'errorSeverity'   => $config->errorSeverity,
                       'warningSeverity' => $config->warningSeverity,
                       'codeHash'        => $codeHash,
                      );

        $configString = implode(',', $configData);
        $hash         = substr(sha1($configString), 0, 12);

        self::$path = getcwd().DIRECTORY_SEPARATOR.".phpcs.$hash.cache";
        if (file_exists(self::$path) === true) {
            self::$cache = json_decode(file_get_contents(self::$path), true);
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


}//end class
