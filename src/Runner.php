<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */


namespace Symplify\PHP7_CodeSniffer;

use Exception;
use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Files\FileList;
use Symplify\PHP7_CodeSniffer\Util\Cache;

final class Runner
{
    /**
     * @var Configuration
     */
    public $config;

    /**
     * @var Ruleset
     */
    public $ruleset;

    /**
     * @var Reporter
     */
    public $reporter;

    public function __construct(Configuration $config, Ruleset $ruleset, Reporter $reporter)
    {
        $this->config = $config;
        $this->ruleset = $ruleset;
        $this->ensureLineEndingsAreDetected();
    }

    /**
     * @return array
     */
    public function runPHPCS()
    {
        $numErrors = $this->run();

        // Print all the reports for this run.
        $toScreen = $this->reporter->printReports();

        // Only print timer output if no reports were
        // printed to the screen so we don't put additional output
        // in something like an XML report. If we are printing to screen,
        // the report types would have already worked out who should
        // print the timer info.
        if ($toScreen === false
            || (($this->reporter->totalErrors + $this->reporter->totalWarnings) === 0)
        ) {
            Util\Timing::printRunTime();
        }

        if ($numErrors === 0) {
            exit(0);
        } else {
            exit(1);
        }
    }


    public function runPHPCBF() : array
    {
        if (defined('PHP_CodeSniffer_CBF') === false) {
            define('PHP_CodeSniffer_CBF', true);
        }

        Util\Timing::startTiming();

        // Creating the Config object populates it with all required settings
        // based on the CLI arguments provided to the script and any config
        // values the user has set.

        // Init the run and load the rulesets to set additional config vars.
        $this->init();

        // Override some of the command line settings that might break the fixes.
        $this->config->reports      = array('cbf' => null);

        $numErrors = $this->run();
        $this->reporter->printReports();

        echo PHP_EOL;
        Util\Timing::printRunTime();

        // We can't tell exactly how many errors were fixed, but
        // we know how many errors were found.
        exit($numErrors);

    }//end runPHPCBF()


//    private function init()
//    {
//        // The ruleset contains all the information about how the files
//        // should be checked and/or fixed.
//        $this->ruleset = new Ruleset($this->config);
//    }

    /**
     * @return int The number of errors and warnings found.
     */
    private function run() : int
    {
        $todo = new FileList($this->config, $this->ruleset);
        $numFiles = count($todo);

        Cache::load($this->ruleset, $this->config);

        $numProcessed = 0;
        $dots         = 0;
        $maxLength    = strlen($numFiles);
        $lastDir      = '';

        // Running normally.
        foreach ($todo as $path => $file) {
            $currDir = dirname($path);
            if ($lastDir !== $currDir) {
                $lastDir = $currDir;
            }

            $this->processFile($file);

            $numProcessed++;

            // Show progress information.
            if ($file->ignored === true) {
                echo 'S';
            } else {
                $errors   = $file->getErrorCount();
                $warnings = $file->getWarningCount();
                if ($errors > 0) {
                    echo "\033[31m";
                    echo 'E';
                } else if ($warnings > 0) {
                    echo "\033[33m";

                    echo 'W';
                } else {
                    echo '.';
                }

                echo "\033[0m";
            }//end if

            $dots++;
            if ($dots === 60) {
                $padding = ($maxLength - strlen($numProcessed));
                echo str_repeat(' ', $padding);
                $percent = round(($numProcessed / $numFiles) * 100);
                echo " $numProcessed / $numFiles ($percent%)".PHP_EOL;
                $dots = 0;
            }
        }//end foreach

        Cache::save();

        return ($this->reporter->totalErrors + $this->reporter->totalWarnings);
    }

    /**
     * Processes a single file, including checking and fixing.
     */
    private function processFile(File $file)
    {
        try {
            $file->process();
        } catch (Exception $e) {
            $error = 'An error occurred during processing; checking has been aborted. The error message was: '.$e->getMessage();
            $file->addErrorOnLine($error, 1, 'Internal.Exception');
        }

        $this->reporter->cacheFileReport($file, $this->config);

        // Clean up the file to save (a lot of) memory.
        $file->cleanUp();
    }

    /**
     * Ensure this option is enabled or else line endings will not always
     * be detected properly for files created on a Mac with the /r line ending.
     */
    private function ensureLineEndingsAreDetected()
    {
        ini_set('auto_detect_line_endings', true);
    }
}
