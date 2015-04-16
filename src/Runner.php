<?php
/**
 * Respnsible for running PHPCS and PHPCBF.
 *
 * After creating and object of this class, you probably just want to
 * call runPHPCS() or runPHPCBF().
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Files\FileList;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\DummyFile;

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
        Util\Timing::startTiming();
        Runner::checkRequirements();

        if (defined('PHP_CODESNIFFER_CBF') === false) {
            define('PHP_CODESNIFFER_CBF', false);
        }

        // Creating the Config object populates it with all required settings
        // based on the CLI arguments provided to the script and any config
        // values the user has set.
        $this->config = new Config();

        // Other report formats don't really make sense in interactive mode
        // so we hard-code the full report here and when outputting.
        if ($this->config->interactive === true) {
            $this->config->reports = array('full' => null);
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
        if (defined('PHP_CODESNIFFER_CBF') === false) {
            define('PHP_CODESNIFFER_CBF', true);
        }

        Util\Timing::startTiming();
        Runner::checkRequirements();

        // Creating the Config object populates it with all required settings
        // based on the CLI arguments provided to the script and any config
        // values the user has set.
        $this->config = new Config();

        // Override some of the command line settings that might break the fixes.
        $this->config->verbosity    = 0;
        $this->config->showProgress = false;
        $this->config->generator    = null;
        $this->config->explain      = false;
        $this->config->interactive  = false;
        $this->config->showSources  = false;
        $this->config->reportFile   = null;
        $this->config->reports      = array();

        if ($this->config->stdin === true) {
            // They are using STDIN, which can't use diff.
            $this->config->noPatch = true;
        }

        if ($this->config->suffix === '' && $this->config->noPatch === false) {
            // Using the diff/patch tools.
            $diffFile = getcwd().'/phpcbf-fixed.diff';
            $this->config->reports = array('diff' => $diffFile);
            if (file_exists($diffFile) === true) {
                unlink($diffFile);
            }
        } else {
            // Replace the file without the patch command
            // or writing to a file with a new suffix.
            $this->config->reports = array('cbf' => null);
        }

        $numErrors = $this->run();

        // Printing the reports will generate the diff file and/or
        // print output information (depending on if we are patching or not).
        $toScreen = $this->reporter->printReports();

        if ($this->config->suffix === '' && $this->config->noPatch === false) {
            if (file_exists($diffFile) === false) {
                // Nothing to fix.
                if ($numErrors === 0) {
                    // And no errors reported.
                    $exit = 0;
                } else {
                    // Errors we can't fix.
                    $exit = 2;
                }
            } else {
                if (filesize($diffFile) < 10) {
                    // Empty or bad diff file.
                    if ($numErrors === 0) {
                        // And no errors reported.
                        $exit = 0;
                    } else {
                        // Errors we can't fix.
                        $exit = 2;
                    }
                } else {
                    $cmd    = "patch -p0 -ui \"$diffFile\"";
                    $output = array();
                    $retVal = null;
                    exec($cmd, $output, $retVal);

                    if ($retVal === 0) {
                        // Everything went well.
                        $filesPatched = count($output);
                        echo "Patched $filesPatched file";
                        if ($filesPatched > 1) {
                            echo 's';
                        }

                        echo PHP_EOL;
                        $exit = 1;
                    } else {
                        print_r($output);
                        echo "Returned: $retVal".PHP_EOL;
                        $exit = 3;
                    }
                }//end if

                unlink($diffFile);
            }//end if
        } else {
            // File are being patched manually, so we can't tell
            // how many errors were fixed.
            $exit = 1;
        }//end if

        if ($exit === 0) {
            echo 'No fixable errors were found'.PHP_EOL;
        } else if ($exit === 2) {
            echo 'PHPCBF could not fix all the errors found'.PHP_EOL;
        }

        Util\Timing::printRunTime();
        exit($exit);

    }//end runPHPCBF()


    /**
     * Exits if the minimum requirements of PHP_CodSniffer are not met.
     *
     * @return array
     */
    public function checkRequirements()
    {
        // Check the PHP version.
        if (version_compare(PHP_VERSION, '5.4.0') === -1) {
            echo 'ERROR: PHP_CodeSniffer requires PHP version 5.4.0 or greater.'.PHP_EOL;
            exit(2);
        }

        if (extension_loaded('tokenizer') === false) {
            echo 'ERROR: PHP_CodeSniffer requires the tokenizer extension to be enabled.'.PHP_EOL;
            exit(2);
        }

    }//end checkRequirements()


    /**
     * Exits if the minimum requirements of PHP_CodSniffer are not met.
     *
     * @return array
     */
    private function run()
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
        if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
            define('PHP_CODESNIFFER_VERBOSITY', $this->config->verbosity);
        }

        // Create this class so it is autoloaded and sets up a bunch
        // of PHP_CodeSniffer-specific token type constants.
        $tokens = new Util\Tokens();

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

        // The ruleset contains all the information about how the files
        // should be checked and/or fixed.
        $ruleset = new Ruleset($this->config);

        // The class manages all reporter for the run.
        $this->reporter = new Reporter($this->config);

        if ($this->config->stdin === true) {
            $handle       = fopen('php://stdin', 'r');
            $fileContents = stream_get_contents($handle);
            fclose($handle);

            $todo     = array(new DummyFile($fileContents, $ruleset, $this->config));
            $numFiles = 1;
        } else if (empty($this->config->files) === true) {
            echo 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
            $this->config->printUsage();
            exit(0);
        } else {
            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                echo 'Creating file list... ';
            }

            $todo     = new FileList($this->config, $ruleset);
            $numFiles = count($todo);

            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                echo "DONE ($numFiles files in queue)".PHP_EOL;
            }
        }//end if

        $numProcessed = 0;
        $dots         = 0;
        $maxLength    = strlen($numFiles);
        $lastDir      = '';

        foreach ($todo as $path => $file) {
            $currDir = dirname($path);
            if ($lastDir !== $currDir) {
                if (PHP_CODESNIFFER_VERBOSITY > 0 || (PHP_CODESNIFFER_CBF === true && $this->config->stdin === false)) {
                    echo 'Changing into directory '.$currDir.PHP_EOL;
                }

                $lastDir = $currDir;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 0 || (PHP_CODESNIFFER_CBF === true && $this->config->stdin === false)) {
                $startTime = microtime(true);
                echo 'Processing '.basename($path).' ';
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo PHP_EOL;
                }
            }

            try {
                $file->process();

                if (PHP_CODESNIFFER_VERBOSITY > 0 || (PHP_CODESNIFFER_CBF === true && $this->config->stdin === false)) {
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
                    $trace = $e->getTrace();

                    $filename = $trace[0]['args'][0];
                    if (is_object($filename) === true && ($filename instanceof File) === true) {
                        $filename = $filename->getFilename();
                    } else if (is_numeric($filename) === true) {
                        // See if we can find the File object.
                        foreach ($trace as $data) {
                            if (isset($data['args'][0]) === true
                                && ($data['args'][0] instanceof File) === true
                            ) {
                                $filename = $data['args'][0]->getFilename();
                            }
                        }
                    } else if (is_string($filename) === false) {
                        $filename = (string) $filename;
                    }

                    $errorMessage = '"'.$e->getMessage().'" at '.$e->getFile().':'.$e->getLine();
                    $error        = "An error occurred during processing; checking has been aborted. The error message was: $errorMessage";

                    $file->addErrorOnLine($error, 1);
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
                        exit(0);
                        break;
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

            $numProcessed++;

            if (PHP_CODESNIFFER_VERBOSITY > 0
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

        if (PHP_CODESNIFFER_VERBOSITY === 0
            && $this->config->interactive === false
            && $this->config->showProgress === true
        ) {
            echo PHP_EOL.PHP_EOL;
        }

        /*
            if ($values['generator'] !== '') {
            $phpcs = new PHP_CodeSniffer($values['verbosity']);
            foreach ($values['standard'] as $standard) {
                $phpcs->generateDocs(
                    $standard,
                    $values['sniffs'],
                    $values['generator']
                );
            }

            exit(0);
            }

        */

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


}//end class
