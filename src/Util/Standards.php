<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Util;

use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class Standards
{

    public static function getInstalledStandardPaths() : array
    {
        return (new StandardFinder())->getStandards();
    }

    public static function getInstalledStandards() : array
    {
        return self::getInstalledStandardPaths();
    }

    public static function isInstalledStandard(string $standard) : bool
    {
        $standards = (new StandardFinder())->getStandards();

        if (isset($standards[$standard])) {
            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public static function getInstalledStandardPath(string $name)
    {
        $standards = (new StandardFinder())->getStandards();

        if (isset($standards[$name])) {
            return $standards[$name];
        }

        return null;
    }

    public static function printInstalledStandards()
    {
        $installedStandards = self::getInstalledStandards();
        $installedStandardNames = implode(array_keys($installedStandards), ', ');
        echo 'The installed coding standards are: '.$installedStandardNames.PHP_EOL;
    }

}
