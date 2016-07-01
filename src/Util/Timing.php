<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Util;

final class Timing
{
    /**
     * @var float
     */
    private static $startTime;

    /**
     * Used to make sure we only print the run time once per run.
     *
     * @var bool
     */
    private static $printed = false;


    public static function startTiming()
    {
        self::$startTime = microtime(true);
    }

    public static function printRunTime(bool $force = false)
    {
        if ($force === false && self::$printed === true) {
            // A double call.
            return;
        }

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

        self::$printed = true;
    }
}
