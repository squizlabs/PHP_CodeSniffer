<?php
/**
 * Responsible for running PHPCS and PHPCBF.
 *
 * After creating an object of this class, you probably just want to
 * call runPHPCS() or runPHPCBF().
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\FileList;
use PHP_CodeSniffer\Util\Cache;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Standards;

class Runner
{

    /**
     * The config data for the run.
     *
     * @var \PHP_CodeSniffer\Config
     */
    public $config = null;

    /**
     * The ruleset used for the run.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    public $ruleset = null;

    /**
     * The reporter used for generating reports after the run.
     *
     * @var \PHP_CodeSniffer\Reporter
     */
    public $reporter = null;


    /**
     * Run the PHPCS script.
     *
     * @return array
     */
    public function runPHPCS()
    {
        try {
            Util\Timing::startTiming();
            Runner::checkRequirements();

            if (defined('PHP_CODESNIFFER_CBF') === false) {
                define('PHP_CODESNIFFER_CBF', false);
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
                    $this->config->standards = [$standard];
                    $ruleset = new Ruleset($this->config);
                    $ruleset->explain();
                }

                return 0;
            }

            // Generate documentation for each of the supplied standards.
            if ($this->config->generator !== null) {
                $standards = $this->config->standards;
                foreach ($standards as $standard) {
                    $this->config->standards = [$standard];
                    $ruleset   = new Ruleset($this->config);
                    $class     = 'PHP_CodeSniffer\Generators\\'.$this->config->generator;
                    $generator = new $class($ruleset);
                    $generator->generate();
                }

                return 0;
            }

            // Other report formats don't really make sense in interactive mode
            // so we hard-code the full report here and when outputting.
            // We also ensure parallel processing is off because we need to do one file at a time.
            if ($this->config->interactive === true) {
                $this->config->reports      = ['full' => null];
                $this->config->parallel     = 1;
                $this->config->showProgress = false;
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
        } catch (DeepExitException $e) {
            echo $e->getMessage();
            return $e->getCode();
        }//end try

        if ($numErrors === 0) {
            // No errors found.
            return 0;
        } else if ($this->reporter->totalFixable === 0) {
            // Errors found, but none of them can be fixed by PHPCBF.
            return 1;
        } else {
            // Errors found, and some can be fixed by PHPCBF.
            return 2;
        }

    }//end runPHPCS()


    /**
     * Run the PHPCBF script.
     *
     * @return array
     */
    public function runPHPCBF()
    {
        if (defined('PHP_CODESNIFFER_CBF') === false) {
            define('PHP_CODESNIFFER_CBF', true);
        }

        try {
            Util\Timing::startTiming();
            Runner::checkRequirements();

            // Creating the Config object populates it with all required settings
            // based on the CLI arguments provided to the script and any config
            // values the user has set.
            $this->config = new Config();

            // When processing STDIN, we can't output anything to the screen
            // or it will end up mixed in with the file output.
            if ($this->config->stdin === true) {
                $this->config->verbosity = 0;
            }

            // Init the run and load the rulesets to set additional config vars.
            $this->init();

            // When processing STDIN, we only process one file at a time and
            // we don't process all the way through, so we can't use the parallel
            // running system.
            if ($this->config->stdin === true) {
                $this->config->parallel = 1;
            }

            // Override some of the command line settings that might break the fixes.
            $this->config->generator    = null;
            $this->config->explain      = false;
            $this->config->interactive  = false;
            $this->config->cache        = false;
            $this->config->showSources  = false;
            $this->config->recordErrors = false;
            $this->config->reportFile   = null;
            $this->config->reports      = ['cbf' => null];

            // If a standard tries to set command line arguments itself, some
            // may be blocked because PHPCBF is running, so stop the script
            // dying if any are found.
            $this->config->dieOnUnknownArg = false;

            $this->run();
            $this->reporter->printReports();

            echo PHP_EOL;
            Util\Timing::printRunTime();
        } catch (DeepExitException $e) {
            echo $e->getMessage();
            return $e->getCode();
        }//end try

        if ($this->reporter->totalFixed === 0) {
            // Nothing was fixed by PHPCBF.
            if ($this->reporter->totalFixable === 0) {
                // Nothing found that could be fixed.
                return 0;
            } else {
                // Something failed to fix.
                return 2;
            }
        }

        if ($this->reporter->totalFixable === 0) {
            // PHPCBF fixed all fixable errors.
            return 1;
        }

        // PHPCBF fixed some fixable errors, but others failed to fix.
        return 2;

    }//end runPHPCBF()


    /**
     * Exits if the minimum requirements of PHP_CodeSniffer are not met.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\DeepExitException If the requirements are not met.
     */
    public function checkRequirements()
    {
        // Check the PHP version.
        if (PHP_VERSION_ID < 50400) {
            $error = 'ERROR: PHP_CodeSniffer requires PHP version 5.4.0 or greater.'.PHP_EOL;
            throw new DeepExitException($error, 3);
        }

        $requiredExtensions = [
            'tokenizer',
            'xmlwriter',
            'SimpleXML',
        ];
        $missingExtensions  = [];

        foreach ($requiredExtensions as $extension) {
            if (extension_loaded($extension) === false) {
                $missingExtensions[] = $extension;
            }
        }

        if (empty($missingExtensions) === false) {
            $last      = array_pop($requiredExtensions);
            $required  = implode(', ', $requiredExtensions);
            $required .= ' and '.$last;

            if (count($missingExtensions) === 1) {
                $missing = $missingExtensions[0];
            } else {
                $last     = array_pop($missingExtensions);
                $missing  = implode(', ', $missingExtensions);
                $missing .= ' and '.$last;
            }

            $error = 'ERROR: PHP_CodeSniffer requires the %s extensions to be enabled. Please enable %s.'.PHP_EOL;
            $error = sprintf($error, $required, $missing);
            throw new DeepExitException($error, 3);
        }

    }//end checkRequirements()


    /**
     * Init the rulesets and other high-level settings.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\DeepExitException If a referenced standard is not installed.
     */
    public function init()
    {
        if (defined('PHP_CODESNIFFER_CBF') === false) {
            define('PHP_CODESNIFFER_CBF', false);
        }

        // Ensure this option is enabled or else line endings will not always
        // be detected properly for files created on a Mac with the /r line ending.
        ini_set('auto_detect_line_endings', true);

        // Disable the PCRE JIT as this caused issues with parallel running.
        ini_set('pcre.jit', false);

        // Check that the standards are valid.
        foreach ($this->config->standards as $standard) {
            if (Util\Standards::isInstalledStandard($standard) === false) {
                // They didn't select a valid coding standard, so help them
                // out by letting them know which standards are installed.
                $error = 'ERROR: the "'.$standard.'" coding standard is not installed. ';
                ob_start();
                Util\Standards::printInstalledStandards();
                $error .= ob_get_contents();
                ob_end_clean();
                throw new DeepExitException($error, 3);
            }
        }

        // Saves passing the Config object into other objects that only need
        // the verbosity flag for debug output.
        if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
            define('PHP_CODESNIFFER_VERBOSITY', $this->config->verbosity);
        }

        // Create this class so it is autoloaded and sets up a bunch
        // of PHP_CodeSniffer-specific token type constants.
        $tokens = new Util\Tokens();

        // Allow autoloading of custom files inside installed standards.
        $installedStandards = Standards::getInstalledStandardDetails();
        foreach ($installedStandards as $name => $details) {
            Autoload::addSearchPath($details['path'], $details['namespace']);
        }

        // The ruleset contains all the information about how the files
        // should be checked and/or fixed.
        try {
            $this->ruleset = new Ruleset($this->config);
        } catch (RuntimeException $e) {
            $error  = 'ERROR: '.$e->getMessage().PHP_EOL.PHP_EOL;
            $error .= $this->config->printShortUsage(true);
            throw new DeepExitException($error, 3);
        }

    }//end init()


    /**
     * Performs the run.
     *
     * @return int The number of errors and warnings found.
     * @throws \PHP_CodeSniffer\Exceptions\DeepExitException
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException
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
        } else {
            if (empty($this->config->files) === true) {
                $error  = 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
                $error .= $this->config->printShortUsage(true);
                throw new DeepExitException($error, 3);
            }

            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                echo 'Creating file list... ';
            }

            $todo = new FileList($this->config, $this->ruleset);

            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                $numFiles = count($todo);
                echo "DONE ($numFiles files in queue)".PHP_EOL;
            }

            if ($this->config->cache === true) {
                if (PHP_CODESNIFFER_VERBOSITY > 0) {
                    echo 'Loading cache... ';
                }

                Cache::load($this->ruleset, $this->config);

                if (PHP_CODESNIFFER_VERBOSITY > 0) {
                    $size = Cache::getSize();
                    echo "DONE ($size files in cache)".PHP_EOL;
                }
            }
        }//end if

        // Turn all sniff errors into exceptions.
        set_error_handler([$this, 'handleErrors']);

        // If verbosity is too high, turn off parallelism so the
        // debug output is clean.
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $this->config->parallel = 1;
        }

        // If the PCNTL extension isn't installed, we can't fork.
        if (function_exists('pcntl_fork') === false) {
            $this->config->parallel = 1;
        }

        $lastDir  = '';
        $numFiles = count($todo);

        if ($this->config->parallel === 1) {
            // Running normally.
            $numProcessed = 0;
            foreach ($todo as $path => $file) {
                if ($file->ignored === false) {
                    $currDir = dirname($path);
                    if ($lastDir !== $currDir) {
                        if (PHP_CODESNIFFER_VERBOSITY > 0) {
                            echo 'Changing into directory '.Common::stripBasepath($currDir, $this->config->basepath).PHP_EOL;
                        }

                        $lastDir = $currDir;
                    }

                    $this->processFile($file);
                } else if (PHP_CODESNIFFER_VERBOSITY > 0) {
                    echo 'Skipping '.basename($file->path).PHP_EOL;
                }

                $numProcessed++;
                $this->printProgress($file, $numFiles, $numProcessed);
            }
        } else {
            // Batching and forking.
            $childProcs  = [];
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
                    $childProcs[] = [
                        'pid' => $pid,
                        'out' => $childOutFilename,
                    ];
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
                    $this->reporter->totalFixed    = 0;

                    // Process the files.
                    $pathsProcessed = [];
                    ob_start();
                    for ($i = $startAt; $i < $endAt; $i++) {
                        $path = $todo->key();
                        $file = $todo->current();

                        if ($file->ignored === true) {
                            continue;
                        }

                        $currDir = dirname($path);
                        if ($lastDir !== $currDir) {
                            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                                echo 'Changing into directory '.Common::stripBasepath($currDir, $this->config->basepath).PHP_EOL;
                            }

                            $lastDir = $currDir;
                        }

                        $this->processFile($file);

                        $pathsProcessed[] = $path;
                        $todo->next();
                    }//end for

                    $debugOutput = ob_get_contents();
                    ob_end_clean();

                    // Write information about the run to the filesystem
                    // so it can be picked up by the main process.
                    $childOutput = [
                        'totalFiles'    => $this->reporter->totalFiles,
                        'totalErrors'   => $this->reporter->totalErrors,
                        'totalWarnings' => $this->reporter->totalWarnings,
                        'totalFixable'  => $this->reporter->totalFixable,
                        'totalFixed'    => $this->reporter->totalFixed,
                    ];

                    $output  = '<'.'?php'."\n".' $childOutput = ';
                    $output .= var_export($childOutput, true);
                    $output .= ";\n\$debugOutput = ";
                    $output .= var_export($debugOutput, true);

                    if ($this->config->cache === true) {
                        $childCache = [];
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

            $success = $this->processChildProcs($childProcs);
            if ($success === false) {
                throw new RuntimeException('One or more child processes failed to run');
            }
        }//end if

        restore_error_handler();

        if (PHP_CODESNIFFER_VERBOSITY === 0
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
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException
     */
    public function handleErrors($code, $message, $file, $line)
    {
        if ((error_reporting() & $code) === 0) {
            // This type of error is being muted.
            return true;
        }

        throw new RuntimeException("$message in $file on line $line");

    }//end handleErrors()


    /**
     * Processes a single file, including checking and fixing.
     *
     * @param \PHP_CodeSniffer\Files\File $file The file to be processed.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\DeepExitException
     */
    public function processFile($file)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $startTime = microtime(true);
            echo 'Processing '.basename($file->path).' ';
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

        try {
            $file->process();

            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                $timeTaken = ((microtime(true) - $startTime) * 1000);
                if ($timeTaken < 1000) {
                    $timeTaken = round($timeTaken);
                    echo "DONE in {$timeTaken}ms";
                } else {
                    $timeTaken = round(($timeTaken / 1000), 2);
                    echo "DONE in $timeTaken secs";
                }

                if (PHP_CODESNIFFER_CBF === true) {
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
                    throw new DeepExitException('', 0);
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

        // Clean up the file to save (a lot of) memory.
        $file->cleanUp();

    }//end processFile()


    /**
     * Waits for child processes to complete and cleans up after them.
     *
     * The reporting information returned by each child process is merged
     * into the main reporter class.
     *
     * @param array $childProcs An array of child processes to wait for.
     *
     * @return bool
     */
    private function processChildProcs($childProcs)
    {
        $numProcessed = 0;
        $totalBatches = count($childProcs);

        $success = true;

        while (count($childProcs) > 0) {
            foreach ($childProcs as $key => $procData) {
                $res = pcntl_waitpid($procData['pid'], $status, WNOHANG);
                if ($res === $procData['pid']) {
                    if (file_exists($procData['out']) === true) {
                        include $procData['out'];

                        unlink($procData['out']);
                        unset($childProcs[$key]);

                        $numProcessed++;

                        if (isset($childOutput) === false) {
                            // The child process died, so the run has failed.
                            $file = new DummyFile(null, $this->ruleset, $this->config);
                            $file->setErrorCounts(1, 0, 0, 0);
                            $this->printProgress($file, $totalBatches, $numProcessed);
                            $success = false;
                            continue;
                        }

                        $this->reporter->totalFiles    += $childOutput['totalFiles'];
                        $this->reporter->totalErrors   += $childOutput['totalErrors'];
                        $this->reporter->totalWarnings += $childOutput['totalWarnings'];
                        $this->reporter->totalFixable  += $childOutput['totalFixable'];
                        $this->reporter->totalFixed    += $childOutput['totalFixed'];

                        if (isset($debugOutput) === true) {
                            echo $debugOutput;
                        }

                        if (isset($childCache) === true) {
                            foreach ($childCache as $path => $cache) {
                                Cache::set($path, $cache);
                            }
                        }

                        // Fake a processed file so we can print progress output for the batch.
                        $file = new DummyFile(null, $this->ruleset, $this->config);
                        $file->setErrorCounts(
                            $childOutput['totalErrors'],
                            $childOutput['totalWarnings'],
                            $childOutput['totalFixable'],
                            $childOutput['totalFixed']
                        );
                        $this->printProgress($file, $totalBatches, $numProcessed);
                    }//end if
                }//end if
            }//end foreach
        }//end while

        return $success;

    }//end processChildProcs()


    /**
     * Print progress information for a single processed file.
     *
     * @param \PHP_CodeSniffer\Files\File $file         The file that was processed.
     * @param int                         $numFiles     The total number of files to process.
     * @param int                         $numProcessed The number of files that have been processed,
     *                                                  including this one.
     *
     * @return void
     */
    public function printProgress(File $file, $numFiles, $numProcessed)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 0
            || $this->config->showProgress === false
        ) {
            return;
        }

        // Show progress information.
        if ($file->ignored === true) {
            echo 'S';
        } else {
            $errors   = $file->getErrorCount();
            $warnings = $file->getWarningCount();
            $fixable  = $file->getFixableCount();
            $fixed    = $file->getFixedCount();

            if (PHP_CODESNIFFER_CBF === true) {
                // Files with fixed errors or warnings are F (green).
                // Files with unfixable errors or warnings are E (red).
                // Files with no errors or warnings are . (black).
                if ($fixable > 0) {
                    if ($this->config->colors === true) {
                        echo "\033[31m";
                    }

                    echo 'E';

                    if ($this->config->colors === true) {
                        echo "\033[0m";
                    }
                } else if ($fixed > 0) {
                    if ($this->config->colors === true) {
                        echo "\033[32m";
                    }

                    echo 'F';

                    if ($this->config->colors === true) {
                        echo "\033[0m";
                    }
                } else {
                    echo '.';
                }//end if
            } else {
                // Files with errors are E (red).
                // Files with fixable errors are E (green).
                // Files with warnings are W (yellow).
                // Files with fixable warnings are W (green).
                // Files with no errors or warnings are . (black).
                if ($errors > 0) {
                    if ($this->config->colors === true) {
                        if ($fixable > 0) {
                            echo "\033[32m";
                        } else {
                            echo "\033[31m";
                        }
                    }

                    echo 'E';

                    if ($this->config->colors === true) {
                        echo "\033[0m";
                    }
                } else if ($warnings > 0) {
                    if ($this->config->colors === true) {
                        if ($fixable > 0) {
                            echo "\033[32m";
                        } else {
                            echo "\033[33m";
                        }
                    }

                    echo 'W';

                    if ($this->config->colors === true) {
                        echo "\033[0m";
                    }
                } else {
                    echo '.';
                }//end if
            }//end if
        }//end if

        $numPerLine = 60;
        if ($numProcessed !== $numFiles && ($numProcessed % $numPerLine) !== 0) {
            return;
        }

        $percent = round(($numProcessed / $numFiles) * 100);
        $padding = (strlen($numFiles) - strlen($numProcessed));
        if ($numProcessed === $numFiles && $numFiles > $numPerLine) {
            $padding += ($numPerLine - ($numFiles - (floor($numFiles / $numPerLine) * $numPerLine)));
        }

        echo str_repeat(' ', $padding)." $numProcessed / $numFiles ($percent%)".PHP_EOL;

    }//end printProgress()


}//end class
