<?php

namespace PHP_CodeSniffer\Util;

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
class Timing
{

    private static $startTime;


    /**
     * Start recording time for the run.
     *
     * @return void
     */
    public static function startTiming()
    {

        self::$startTime = microtime(true);

    }//end startTiming()


    /**
     * Print information about the run.
     *
     * @return void
     */
    public static function printRunTime()
    {
        $time = ((microtime(true) - self::$startTime) * 1000);

        if ($time > 60000) {
            $mins = floor($time / 60000);
            $secs = round((($time % 60000) / 1000), 2);
            $time = $mins.' mins';
            if ($secs !== 0) {
                $time .= ", $secs secs";
            }
        } else if ($time > 1000) {
            $time = round(($time / 1000), 2).' secs';
        } else {
            $time = round($time).'ms';
        }

        $mem = round((memory_get_peak_usage(true) / (1024 * 1024)), 2).'Mb';
        echo "Time: $time; Memory: $mem".PHP_EOL.PHP_EOL;

    }//end printRunTime()


}//end class
