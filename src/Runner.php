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

use Symplify\PHP7_CodeSniffer\Files\FileList;
use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Files\DummyFile;
use Symplify\PHP7_CodeSniffer\Util\Cache;
use Symplify\PHP7_CodeSniffer\Util\Common;
use Symplify\PHP7_CodeSniffer\Exceptions\RuntimeException;

class Runner
{

    /**
     * The config data for the run.
     *
     * @var \Symplify\PHP7_CodeSniffer\Config
     */
    public $config = null;

    /**
     * The ruleset used for the run.
     *
     * @var \Symplify\PHP7_CodeSniffer\Ruleset
     */
    public $ruleset = null;

    /**
     * The reporter used for generating reports after the run.
     *
     * @var \Symplify\PHP7_CodeSniffer\Reporter
     */
    public $reporter = null;


    /**
     * Run the PHPCS script.
     *
     * @return array
     */
    public function runPHPCS()
    {
        Util\Timing::startTiming();

        if (defined('PHP_CodeSniffer_CBF') === false) {
            define('PHP_CodeSniffer_CBF', false);
        }

        // Creating the Config object populates it with all required settings
        // based on the CLI arguments provided to the script and any config
        // values the user has set.
        $this->config = new Config();

        // Init the run and load the rulesets to set additional config vars.
        $this->init();

        // Print a list of sniffs in each of the supplied standards.
        // We fudge the config here so that each standard is explained in isolation.
        if ($this->config->explain === true) {
            $standards = $this->config->standards;
            foreach ($standards as $standard) {
                $this->config->standards = array($standard);
                $ruleset = new Ruleset($this->config);
                $ruleset->explain();
            }

            exit(0);
        }

        // Generate documentation for each of the supplied standards.
        if ($this->config->generator !== null) {
            $standards = $this->config->standards;
            foreach ($standards as $standard) {
                $this->config->standards = array($standard);
                $ruleset   = new Ruleset($this->config);
                $class     = 'Symplify\PHP7_CodeSniffer\Generators\\'.$this->config->generator;
                $generator = new $class($ruleset);
                $generator->generate();
            }

            exit(0);
        }

        // Other report formats don't really make sense in interactive mode
        // so we hard-code the full report here and when outputting.
        // We also ensure parallel processing is off because we need to do one file at a time.
        if ($this->config->interactive === true) {
            $this->config->reports  = array('full' => null);
            $this->config->parallel = 1;
        }

        // Disable caching if we are processing STDIN as we can't be 100%
        // sure where the file came from or if it will change in the future.
        if ($this->config->stdin === true) {
            $this->config->cache = false;
        }

        $numErrors = $this->run();

        // Print all the reports for this run.
        $toScreen = $this->reporter->printReports();

        // Only print timer output if no reports were
        // printed to the screen so we don't put additional output
        // in something like an XML report. If we are printing to screen,
        // the report types would have already worked out who should
        // print the timer info.
        if ($this->config->interactive === false
            && ($toScreen === false
            || (($this->reporter->totalErrors + $this->reporter->totalWarnings) === 0 && $this->config->showProgress === true))
        ) {
            Util\Timing::printRunTime();
        }

        if ($numErrors === 0) {
            exit(0);
        } else {
            exit(1);
        }

    }//end runPHPCS()


    /**
     * Run the PHPCBF script.
     *
     * @return array
     */
    public function runPHPCBF()
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
        $this->config->verbosity    = 0;
        $this->config->showProgress = false;
        $this->config->generator    = null;
        $this->config->explain      = false;
        $this->config->interactive  = false;
        $this->config->cache        = false;
        $this->config->showSources  = false;
        $this->config->recordErrors = false;
        $this->config->reportFile   = null;
        $this->config->reports      = array('cbf' => null);

        // If a standard tries to set command line arguments itself, some
        // may be blocked because PHPCBF is running, so stop the script
        // dying if any are found.
        $this->config->dieOnUnknownArg = false;

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
        // Ensure this option is enabled or else line endings will not always
        // be detected properly for files created on a Mac with the /r line ending.
        ini_set('auto_detect_line_endings', true);

        // Check that the standards are valid.
        foreach ($this->config->standards as $standard) {
            if (Util\Standards::isInstalledStandard($standard) === false) {
                // They didn't select a valid coding standard, so help them
                // out by letting them know which standards are installed.
                echo 'ERROR: the "'.$standard.'" coding standard is not installed. ';
                Util\Standards::printInstalledStandards();
                exit(2);
            }
        }

        // Saves passing the Config object into other objects that only need
        // the verbostity flag for deubg output.
        if (defined('PHP_CodeSniffer_VERBOSITY') === false) {
            define('PHP_CodeSniffer_VERBOSITY', $this->config->verbosity);
        }

        // Create this class so it is autoloaded and sets up a bunch
        // of Symplify\PHP7_CodeSniffer-specific token type constants.
        $tokens = new Util\Tokens();

        // The ruleset contains all the information about how the files
        // should be checked and/or fixed.
        $this->ruleset = new Ruleset($this->config);

    }//end init()


    /**
     * Performs the run.
     *
     * @return int The number of errors and warnings found.
     */
    private function run()
    {
        // The class that manages all reporters for the run.
        $this->reporter = new Reporter($this->config);

        // Include bootstrap files.
        foreach ($this->config->bootstrap as $bootstrap) {
            include $bootstrap;
        }

        if ($this->config->stdin === true) {
            $fileContents = $this->config->stdinContent;
            if ($fileContents === null) {
                $handle = fopen('php://stdin', 'r');
                stream_set_blocking($handle, true);
                $fileContents = stream_get_contents($handle);
                fclose($handle);
            }

            $todo  = new FileList($this->config, $this->ruleset);
            $dummy = new DummyFile($fileContents, $this->ruleset, $this->config);
            $todo->addFile($dummy->path, $dummy);

            $numFiles = 1;
        } else {
            if (empty($this->config->files) === true) {
                echo 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
                $this->config->printUsage();
                exit(0);
            }

            if (PHP_CodeSniffer_VERBOSITY > 0) {
                echo 'Creating file list... ';
            }

            $todo     = new FileList($this->config, $this->ruleset);
            $numFiles = count($todo);

            if (PHP_CodeSniffer_VERBOSITY > 0) {
                echo "DONE ($numFiles files in queue)".PHP_EOL;
            }

            if ($this->config->cache === true) {
                if (PHP_CodeSniffer_VERBOSITY > 0) {
                    echo 'Loading cache... ';
                }

                Cache::load($this->ruleset, $this->config);

                if (PHP_CodeSniffer_VERBOSITY > 0) {
                    $size = Cache::getSize();
                    echo "DONE ($size files in cache)".PHP_EOL;
                }
            }
        }//end if

        $numProcessed = 0;
        $dots         = 0;
        $maxLength    = strlen($numFiles);
        $lastDir      = '';
        $childProcs   = array();

        // Turn all sniff errors into exceptions.
        set_error_handler(array($this, 'handleErrors'));

        // If verbosity is too high, turn off parallelism so the
        // debug output is clean.
        if (PHP_CodeSniffer_VERBOSITY > 1) {
            $this->config->parallel = 1;
        }

        if ($this->config->parallel === 1) {
            // Running normally.
            foreach ($todo as $path => $file) {
                $currDir = dirname($path);
                if ($lastDir !== $currDir) {
                    if (PHP_CodeSniffer_VERBOSITY > 0 || (PHP_CodeSniffer_CBF === true && $this->config->stdin === false)) {
                        echo 'Changing into directory '.Common::stripBasepath($currDir, $this->config->basepath).PHP_EOL;
                    }

                    $lastDir = $currDir;
                }

                $this->processFile($file);

                $numProcessed++;

                if (PHP_CodeSniffer_VERBOSITY > 0
                    || $this->config->interactive === true
                    || $this->config->showProgress === false
                ) {
                    continue;
                }

                // Show progress information.
                if ($file->ignored === true) {
                    echo 'S';
                } else {
                    $errors   = $file->getErrorCount();
                    $warnings = $file->getWarningCount();
                    if ($errors > 0) {
                        if ($this->config->colors === true) {
                            echo "\033[31m";
                        }

                        echo 'E';
                    } else if ($warnings > 0) {
                        if ($this->config->colors === true) {
                            echo "\033[33m";
                        }

                        echo 'W';
                    } else {
                        echo '.';
                    }

                    if ($this->config->colors === true) {
                        echo "\033[0m";
                    }
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
        } else {
            // Batching and forking.
            $numFiles    = count($todo);
            $numPerBatch = ceil($numFiles / $this->config->parallel);

            for ($batch = 0; $batch < $this->config->parallel; $batch++) {
                $startAt = ($batch * $numPerBatch);
                if ($startAt >= $numFiles) {
                    break;
                }

                $endAt = ($startAt + $numPerBatch);
                if ($endAt > $numFiles) {
                    $endAt = $numFiles;
                }

                $childOutFilename = tempnam(sys_get_temp_dir(), 'phpcs-child');
                $pid = pcntl_fork();
                if ($pid === -1) {
                    throw new RuntimeException('Failed to create child process');
                } else if ($pid !== 0) {
                    $childProcs[] = array(
                                     'pid' => $pid,
                                     'out' => $childOutFilename,
                                    );
                } else {
                    // Move forward to the start of the batch.
                    $todo->rewind();
                    for ($i = 0; $i < $startAt; $i++) {
                        $todo->next();
                    }

                    // Reset the reporter to make sure only figures from this
                    // file batch are recorded.
                    $this->reporter->totalFiles    = 0;
                    $this->reporter->totalErrors   = 0;
                    $this->reporter->totalWarnings = 0;
                    $this->reporter->totalFixable  = 0;

                    // Process the files.
                    $pathsProcessed = array();
                    ob_start();
                    for ($i = $startAt; $i < $endAt; $i++) {
                        $path = $todo->key();
                        $file = $todo->current();

                        $currDir = dirname($path);
                        if ($lastDir !== $currDir) {
                            if (PHP_CodeSniffer_VERBOSITY > 0 || (PHP_CodeSniffer_CBF === true && $this->config->stdin === false)) {
                                echo 'Changing into directory '.Common::stripBasepath($currDir, $this->config->basepath).PHP_EOL;
                            }

                            $lastDir = $currDir;
                        }

                        $this->processFile($file);

                        $pathsProcessed[] = $path;
                        $todo->next();
                    }

                    $debugOutput = ob_get_contents();
                    ob_end_clean();

                    // Write information about the run to the filesystem
                    // so it can be picked up by the main process.
                    $childOutput = array(
                                    'totalFiles'    => $this->reporter->totalFiles,
                                    'totalErrors'   => $this->reporter->totalErrors,
                                    'totalWarnings' => $this->reporter->totalWarnings,
                                    'totalFixable'  => $this->reporter->totalFixable,
                                   );

                    $output  = '<'.'?php'."\n".' $childOutput = ';
                    $output .= var_export($childOutput, true);
                    $output .= ";\n\$debugOutput = ";
                    $output .= var_export($debugOutput, true);

                    if ($this->config->cache === true) {
                        $childCache = array();
                        foreach ($pathsProcessed as $path) {
                            $childCache[$path] = Cache::get($path);
                        }

                        $output .= ";\n\$childCache = ";
                        $output .= var_export($childCache, true);
                    }

                    $output .= ";\n?".'>';
                    file_put_contents($childOutFilename, $output);
                    exit($pid);
                }//end if
            }//end for

            $this->processChildProcs($childProcs);
        }//end if

        restore_error_handler();

        if (PHP_CodeSniffer_VERBOSITY === 0
            && $this->config->interactive === false
            && $this->config->showProgress === true
        ) {
            echo PHP_EOL.PHP_EOL;
        }

        if ($this->config->cache === true) {
            Cache::save();
        }

        $ignoreWarnings = Config::getConfigData('ignore_warnings_on_exit');
        $ignoreErrors   = Config::getConfigData('ignore_errors_on_exit');

        $return = ($this->reporter->totalErrors + $this->reporter->totalWarnings);
        if ($ignoreErrors !== null) {
            $ignoreErrors = (bool) $ignoreErrors;
            if ($ignoreErrors === true) {
                $return -= $this->reporter->totalErrors;
            }
        }

        if ($ignoreWarnings !== null) {
            $ignoreWarnings = (bool) $ignoreWarnings;
            if ($ignoreWarnings === true) {
                $return -= $this->reporter->totalWarnings;
            }
        }

        return $return;

    }//end run()


    /**
     * Converts all PHP errors into exceptions.
     *
     * This method forces a sniff to stop processing if it is not
     * able to handle a specific piece of code, instead of continuing
     * and potentially getting into a loop.
     *
     * @param int    $code    The level of error raised.
     * @param string $message The error message.
     * @param string $file    The path of the file that raised the error.
     * @param int    $line    The line number the error was raised at.
     *
     * @return void
     */
    public function handleErrors($code, $message, $file, $line)
    {
        throw new RuntimeException("$message in $file on line $line");

    }//end handleErrors()


    /**
     * Processes a single file, including checking and fixing.
     *
     * @param \Symplify\PHP7_CodeSniffer\Files\File $file The file to be processed.
     *
     * @return void
     */
    private function processFile($file)
    {
        if (PHP_CodeSniffer_VERBOSITY > 0 || (PHP_CodeSniffer_CBF === true && $this->config->stdin === false)) {
            $startTime = microtime(true);
            echo 'Processing '.basename($file->path).' ';
            if (PHP_CodeSniffer_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

        try {
            $file->process();

            if (PHP_CodeSniffer_VERBOSITY > 0
                || (PHP_CodeSniffer_CBF === true && $this->config->stdin === false)
            ) {
                $timeTaken = ((microtime(true) - $startTime) * 1000);
                if ($timeTaken < 1000) {
                    $timeTaken = round($timeTaken);
                    echo "DONE in {$timeTaken}ms";
                } else {
                    $timeTaken = round(($timeTaken / 1000), 2);
                    echo "DONE in $timeTaken secs";
                }

                if (PHP_CodeSniffer_CBF === true) {
                    $errors = $file->getFixableCount();
                    echo " ($errors fixable violations)".PHP_EOL;
                } else {
                    $errors   = $file->getErrorCount();
                    $warnings = $file->getWarningCount();
                    echo " ($errors errors, $warnings warnings)".PHP_EOL;
                }
            }
        } catch (\Exception $e) {
            $error = 'An error occurred during processing; checking has been aborted. The error message was: '.$e->getMessage();
            $file->addErrorOnLine($error, 1, 'Internal.Exception');
        }//end try

        $this->reporter->cacheFileReport($file, $this->config);

        // Clean up the file to save (a lot of) memory.
        $file->cleanUp();

        if ($this->config->interactive === true) {
            /*
                Running interactively.
                Print the error report for the current file and then wait for user input.
            */

            // Get current violations and then clear the list to make sure
            // we only print violations for a single file each time.
            $numErrors = null;
            while ($numErrors !== 0) {
                $numErrors = ($file->getErrorCount() + $file->getWarningCount());
                if ($numErrors === 0) {
                    continue;
                }

                $this->reporter->printReport('full');

                echo '<ENTER> to recheck, [s] to skip or [q] to quit : ';
                $input = fgets(STDIN);
                $input = trim($input);

                switch ($input) {
                case 's':
                    break(2);
                case 'q':
                    exit(0);
                default:
                    // Repopulate the sniffs because some of them save their state
                    // and only clear it when the file changes, but we are rechecking
                    // the same file.
                    $file->ruleset->populateTokenListeners();
                    $file->reloadContent();
                    $file->process();
                    $this->reporter->cacheFileReport($file, $this->config);
                    break;
                }
            }//end while
        }//end if

    }//end processFile()


    /**
     * Waits for child processes to complete and cleans up after them.
     *
     * The reporting information returned by each child process is merged
     * into the main reporter class.
     *
     * @param array $childProcs An array of child processes to wait for.
     *
     * @return void
     */
    private function processChildProcs($childProcs)
    {
        $dots         = 0;
        $numProcessed = 0;
        $totalBatches = count($childProcs);
        $maxLength    = strlen($totalBatches);

        while (count($childProcs) > 0) {
            foreach ($childProcs as $key => $procData) {
                $res = pcntl_waitpid($procData['pid'], $status, WNOHANG);
                if ($res === $procData['pid']) {
                    if (file_exists($procData['out']) === true) {
                        include $procData['out'];
                        if (isset($childOutput) === true) {
                            $this->reporter->totalFiles    += $childOutput['totalFiles'];
                            $this->reporter->totalErrors   += $childOutput['totalErrors'];
                            $this->reporter->totalWarnings += $childOutput['totalWarnings'];
                            $this->reporter->totalFixable  += $childOutput['totalFixable'];
                        }

                        if (isset($debugOutput) === true) {
                            echo $debugOutput;
                        }

                        if (isset($childCache) === true) {
                            foreach ($childCache as $path => $cache) {
                                Cache::set($path, $cache);
                            }
                        }

                        unlink($procData['out']);
                        unset($childProcs[$key]);

                        $numProcessed++;

                        if (PHP_CodeSniffer_VERBOSITY > 0
                            || $this->config->showProgress === false
                        ) {
                            continue;
                        }

                        if ($childOutput['totalErrors'] > 0) {
                            if ($this->config->colors === true) {
                                echo "\033[31m";
                            }

                            echo 'E';
                        } else if ($childOutput['totalWarnings'] > 0) {
                            if ($this->config->colors === true) {
                                echo "\033[33m";
                            }

                            echo 'W';
                        } else {
                            echo '.';
                        }

                        if ($this->config->colors === true) {
                            echo "\033[0m";
                        }

                        $dots++;
                        if ($dots === 60) {
                            $padding = ($maxLength - strlen($numProcessed));
                            echo str_repeat(' ', $padding);
                            $percent = round(($numProcessed / $totalBatches) * 100);
                            echo " $numProcessed / $totalBatches ($percent%)".PHP_EOL;
                            $dots = 0;
                        }
                    }//end if
                }//end if
            }//end foreach
        }//end while

    }//end processChildProcs()


}//end class
