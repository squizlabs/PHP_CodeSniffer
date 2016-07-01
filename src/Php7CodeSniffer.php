<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Exception;

final class Config
{
    /**
     * @var string
     */
    const VERSION = '3.0.0';

    /**
     * @var array<string, mixed>
     */
    private $settings = [
        'files'           => null,
        'standards'       => ['PSR2'],
        'showSources'     => true,
        'recordErrors'     => true,
        'extensions'      => ['php' => 'PHP'],
        'sniffs'          => null,
        'reports'       => ['full' => null],
        'reportWidth'     => null,
        'cache'       => true,
    ];

    /**
     * @var bool
     */
    public $showProgress = true;

    /**
     * @var array
     */
    private $standards = ['PSR2'];

    /**
     * @var int
     */
    private $reportWidth;

    public function __construct()
    {
        $this->setDefaultReportWidth();
    }

    public function setStandards(array $standards)
    {
        $this->ensureStandardsAreValid($standards);
        $this->standards = $standards;
    }

    public function getStandards() : array
    {
        foreach ($this->standards as $standard) {
            // todo: include rulesets!
            dump(123);
        }
        return $this->standards;
    }


    /**
     * Processes a long (--example) command line argument.
     *
     * @param string $arg The command line argument.
     * @param int    $pos The position of the argument on the command line.
     *
     * @return void
     */
    public function processLongArgument($arg, $pos)
    {
        switch ($arg) {
        default:
            if (substr($arg, 0, 7) === 'sniffs=') {
                $sniffs = explode(',', substr($arg, 7));
                foreach ($sniffs as $sniff) {
                    if (substr_count($sniff, '.') !== 2) {
                        echo 'ERROR: The specified sniff code "'.$sniff.'" is invalid'.PHP_EOL.PHP_EOL;
                        $this->printUsage();
                        exit(2);
                    }
                }

                $this->sniffs = $sniffs;
            } else if (substr($arg, 0, 9) === 'standard=') {
                $standards = trim(substr($arg, 9));
                if ($standards !== '') {
                    $this->standards = explode(',', $standards);
                }
            } else {
                $this->processUnknownArgument('--'.$arg, $pos);
            }//end if

            break;
        }//end switch

    }//end processLongArgument()


    /**
     * Processes an unknown command line argument.
     *
     * Assumes all unknown arguments are files and folders to check.
     *
     * @param string $arg The command line argument.
     * @param int    $pos The position of the argument on the command line.
     *
     * @return void
     */
    public function processUnknownArgument($arg, $pos)
    {
        // We don't know about any additional switches; just files.
        if ($arg{0} === '-') {
            echo "ERROR: option \"$arg\" not known".PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        }

        $file = Util\Common::realpath($arg);
        if (file_exists($file) === false) {
            echo 'ERROR: The file "'.$arg.'" does not exist.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        } else {
            $files       = $this->files;
            $files[]     = $file;
            $this->files = $files;
        }

    }//end processUnknownArgument()


    public function printUsage()
    {
        if (PHP_CodeSniffer_CBF === true) {
            $this->printPHPCBFUsage();
        } else {
            $this->printPHPCSUsage();
        }
    }

    public function printPHPCSUsage()
    {
        echo 'Usage: phpcs '.PHP_EOL;
        echo '    [--standard=<standard>] [--sniffs=<sniffs>]'.PHP_EOL;
        echo '    <file> - ...'.PHP_EOL;
        echo '        -s            Show sniff codes in all reports'.PHP_EOL;
        echo '        <file>        One or more files and/or directories to check'.PHP_EOL;
        echo '        <sniffs>      A comma separated list of sniff codes to limit the check to'.PHP_EOL;
        echo '                      (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo '        <standard>    The name or path of the coding standard to use'.PHP_EOL;
    }

    public function printPHPCBFUsage()
    {
        echo 'Usage: phpcbf '.PHP_EOL;
        echo '    [--standard=<standard>] [--sniffs=<sniffs>]'.PHP_EOL;
        echo '    <file> - ...'.PHP_EOL;
        echo '        <file>        One or more files and/or directories to fix'.PHP_EOL;
        echo '        <sniffs>      A comma separated list of sniff codes to limit the fixes to'.PHP_EOL;
        echo '                      (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo '        <standard>    The name or path of the coding standard to use'.PHP_EOL;
    }

    /**
     * @throws Exception
     */
    private function ensureStandardsAreValid(array $standards)
    {
        foreach ($standards as $standard) {
            if (Util\Standards::isInstalledStandard($standard) === false) {
                throw new Exception(
                    sprintf(
                        'The "%s" coding standard is not installed.',
                        $standard
                    )
                );
            }
        }
    }

    private function setDefaultReportWidth()
    {
        if (preg_match('|\d+ (\d+)|', shell_exec('stty size 2>&1'), $matches) === 1) {
            $this->reportWidth = (int)$matches[1];
        }
    }
}
