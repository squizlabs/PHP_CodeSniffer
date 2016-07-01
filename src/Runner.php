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
    }

    /**
     * @return array
     */
    public function runPHPCS()
    {
        dump($this->config);

        die;


        // Init the run and load the rulesets to set additional config vars.
        $this->init();

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
        $this->config->showSources  = false;
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

        // Create this class so it is autoloaded and sets up a bunch
        // of Symplify\PHP7_CodeSniffer-specific token type constants.
        $tokens = new Util\Tokens();

        // The ruleset contains all the information about how the files
        // should be checked and/or fixed.
        $this->ruleset = new Ruleset($this->config);

    }//end init()


    /**
     * @return int The number of errors and warnings found.
     */
    private function run() : int
    {
        // The class that manages all reporters for the run.
        $this->reporter = new Reporter($this->config);

        if (empty($this->config->files) === true) {
            echo 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
            $this->config->printUsage();
            exit(0);
        }

        $todo = new FileList($this->config, $this->ruleset);
        $numFiles = count($todo);

        if ($this->config->cache === true) {
            Cache::load($this->ruleset, $this->config);
        }

        $numProcessed = 0;
        $dots         = 0;
        $maxLength    = strlen($numFiles);
        $lastDir      = '';
        $childProcs   = array();

        // Turn all sniff errors into exceptions.
        set_error_handler(array($this, 'handleErrors'));

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

        restore_error_handler();

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
        if (PHP_CodeSniffer_CBF === true) {
            echo 'Processing '.basename($file->path).' ';
        }

        try {
            $file->process();
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

                        // show progress
                        if ($childOutput['totalErrors'] > 0) {
                            echo "\033[31m";

                            echo 'E';
                        } else if ($childOutput['totalWarnings'] > 0) {
                            echo "\033[33m";

                            echo 'W';
                        } else {
                            echo '.';
                        }

                        echo "\033[0m";

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
