<?php
/**
 * Responsible for running PHPCS and PHPCBF.
 *
 * After creating an object of this class, you probably just want to
 * call runPHPCS() or runPHPCBF().
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer;

use Exception;
use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Files\FileList;
use Symplify\PHP7_CodeSniffer\Util\Cache;
use Symplify\PHP7_CodeSniffer\Exceptions\RuntimeException;

final class Runner
{
    /**
     * @var Config
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

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->ensureLineEndingsAreDetected();
    }

    /**
     * @return array
     */
    public function runPHPCS()
    {
        // Init the run and load the rulesets to set additional config vars.
        $this->init();

        die;

        $numErrors = $this->run();

        // Print all the reports for this run.
        $toScreen = $this->reporter->printReports();

        // Only print timer output if no reports were
        // printed to the screen so we don't put additional output
        // in something like an XML report. If we are printing to screen,
        // the report types would have already worked out who should
        // print the timer info.
        if (($toScreen === false
            || (($this->reporter->totalErrors + $this->reporter->totalWarnings) === 0 && $this->config->showProgress === true))
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
        $this->config = new Config();

        // Init the run and load the rulesets to set additional config vars.
        $this->init();

        // Override some of the command line settings that might break the fixes.
        $this->config->showProgress = false;
        $this->config->explain      = false;
        $this->config->cache        = false;
        $this->config->reports      = array('cbf' => null);

        $numErrors = $this->run();
        $this->reporter->printReports();

        echo PHP_EOL;
        Util\Timing::printRunTime();

        // We can't tell exactly how many errors were fixed, but
        // we know how many errors were found.
        exit($numErrors);

    }//end runPHPCBF()


    /**
     * Init the rulesets and other high-level settings.
     *
     * @return void
     */
    private function init()
    {


        // Check that the standards are valid.
        foreach ($this->config->getStandards() as $standard) {
            if (Util\Standards::isInstalledStandard($standard) === false) {
                // They didn't select a valid coding standard, so help them
                // out by letting them know which standards are installed.
                echo 'ERROR: the "'.$standard.'" coding standard is not installed. ';
                Util\Standards::printInstalledStandards();
                exit(2);
            }
        }

        // The ruleset contains all the information about how the files
        // should be checked and/or fixed.
        die;

        $this->ruleset = new Ruleset($this->config);
    }//end init()


    /**
     * @return int The number of errors and warnings found.
     */
    private function run() : int
    {
        // The class that manages all reporters for the run.
        $this->reporter = new Reporter($this->config);

        // todo: resolve in command running
        if (empty($this->config->files) === true) {
            echo 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
            $this->config->printUsage();
            exit(0);
        }

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
        if (PHP_CodeSniffer_CBF === true) {
            echo 'Processing '.basename($file->path).' ';
        }

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
