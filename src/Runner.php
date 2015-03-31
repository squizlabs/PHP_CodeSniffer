<?php

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Files\FileList;

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

error_reporting(E_ALL | E_STRICT);

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
class Runner
{

    /**
     * Run the PHPCS script.
     *
     * @return array
     */
    public function runPHPCS()
    {
        if (defined('PHP_CODESNIFFER_CBF') === false) {
            define('PHP_CODESNIFFER_CBF', false);
        }

        $numErrors = $this->run();
        if ($numErrors === 0) {
            exit(0);
        } else {
            exit(1);
        }

    }//end runphpcs()


    /**
     * Run the PHPCBF script.
     *
     * @return array
     */
    public function runphpcbf()
    {
        if (defined('PHP_CODESNIFFER_CBF') === false) {
            define('PHP_CODESNIFFER_CBF', true);
        }

        if (is_file(dirname(__FILE__).'/../CodeSniffer/reporter.php') === true) {
            include_once dirname(__FILE__).'/../CodeSniffer/reporter.php';
        } else {
            include_once 'PHP/CodeSniffer/reporter.php';
        }

        Util\Timing::startTiming();
        $this->checkRequirements();

        $this->dieOnUnknownArg = false;

        // Override some of the command line settings that might break the fixes.
        $cliValues = $this->getCommandLineValues();
        $cliValues['verbosity']    = 0;
        $cliValues['showProgress'] = false;
        $cliValues['generator']    = '';
        $cliValues['explain']      = false;
        $cliValues['interactive']  = false;
        $cliValues['showSources']  = false;
        $cliValues['reportFile']   = null;
        $cliValues['reports']      = array();

        $suffix = '';
        if (isset($cliValues['suffix']) === true) {
            $suffix = $cliValues['suffix'];
        }

        $allowPatch = true;
        if (isset($cliValues['no-patch']) === true || empty($cliValues['files']) === true) {
            // They either asked for this,
            // or they are using STDIN, which can't use diff.
            $allowPatch = false;
        }

        if ($suffix === '' && $allowPatch === true) {
            // Using the diff/patch tools.
            $diffFile = getcwd().'/phpcbf-fixed.diff';
            $cliValues['reports'] = array('diff' => $diffFile);
            if (file_exists($diffFile) === true) {
                unlink($diffFile);
            }
        } else {
            // Replace the file without the patch command
            // or writing to a file with a new suffix.
            $cliValues['reports']       = array('cbf' => null);
            $cliValues['phpcbf-suffix'] = $suffix;
        }

        $numErrors = $this->process($cliValues);

        if ($suffix === '' && $allowPatch === true) {
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

        $this->printRunTime();
        exit($exit);

    }//end runphpcbf()


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
        Util\Timing::startTiming();

        Runner::checkRequirements();

        // Ensure this option is enabled or else line endings will not always
        // be detected properly for files created on a Mac with the /r line ending.
        ini_set('auto_detect_line_endings', true);

        // Creating the Config object populates it with all required settings
        // based on the CLI arguments provided to the script and any config
        // values the user has set.
        $config = new Config();

        // Check that the standards are valid.
        foreach ($config->standards as $standard) {
            if (Util\Standards::isInstalledStandard($standard) === false) {
                // They didn't select a valid coding standard, so help them
                // out by letting them know which standards are installed.
                echo 'ERROR: the "'.$standard.'" coding standard is not installed. ';
                Util::printInstalledStandards();
                exit(2);
            }
        }

        // Saves passing the Config object into other objects that only need
        // the verbostity flag for deubg output.
        if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
            define('PHP_CODESNIFFER_VERBOSITY', $config->verbosity);
        }

        // Create this class so it is autoloaded and sets up a bunch
        // of PHP_CodeSniffer-specific token type constants.
        $tokens = new Util\Tokens();

        // Print a list of sniffs in each of the supplied standards.
        // We fudge the config here so that each standard is explained in isolation.
        if ($config->explain === true) {
            $standards = $config->standards;
            foreach ($standards as $standard) {
                $config->standards = array($standard);
                $ruleset = new Ruleset($config);
                $ruleset->explain();
            }

            exit(0);
        }

        // The ruleset contains all the information about how the files
        // should be checked and/or fixed.
        $ruleset = new Ruleset($config);

        // The class manages all reporter for the run.
        $reporter = new Reporter($config);

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo 'Creating file list... ';
        }

        $todo     = new FileList($config, $ruleset);
        $numFiles = count($todo);

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo "DONE ($numFiles files in queue)".PHP_EOL;
        }

        $numProcessed = 0;
        $dots         = 0;
        $maxLength    = strlen($numFiles);
        $lastDir      = '';




$stdin = false;




        foreach ($todo as $path => $file) {
            $currDir    = dirname($path);
            if ($lastDir !== $currDir) {
                if (PHP_CODESNIFFER_VERBOSITY > 0 || PHP_CODESNIFFER_CBF === true) {
                    echo 'Changing into directory '.$currDir.PHP_EOL;
                }

                $lastDir = $currDir;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 0 || (PHP_CODESNIFFER_CBF === true && $stdin === false)) {
                $startTime = microtime(true);
                echo 'Processing '.basename($path).' ';
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo PHP_EOL;
                }
            }

            try {
                $file->process();

                if (PHP_CODESNIFFER_VERBOSITY > 0 || (PHP_CODESNIFFER_CBF === true && $stdin === false)) {
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
            } catch (Exception $e) {
                /*
                $trace = $e->getTrace();

                $filename = $trace[0]['args'][0];
                if (is_object($filename) === true
                    && get_class($filename) === 'PHP_CodeSniffer_File'
                ) {
                    $filename = $filename->getFilename();
                } else if (is_numeric($filename) === true) {
                    // See if we can find the PHP_CodeSniffer_File object.
                    foreach ($trace as $data) {
                        if (isset($data['args'][0]) === true
                            && ($data['args'][0] instanceof PHP_CodeSniffer_File) === true
                        ) {
                            $filename = $data['args'][0]->getFilename();
                        }
                    }
                } else if (is_string($filename) === false) {
                    $filename = (string) $filename;
                }

                $errorMessage = '"'.$e->getMessage().'" at '.$e->getFile().':'.$e->getLine();
                $error        = "An error occurred during processing; checking has been aborted. The error message was: $errorMessage";

                $phpcsFile = new PHP_CodeSniffer_File(
                    $filename,
                    $this->_tokenListeners,
                    $this->ruleset,
                    $this
                );

                $phpcsFile->addError($error, null);
                */
            }//end try

            if ($config->interactive === false) {
                // Cache the report data for this file so we can unset it to save memory.
                $reporter->cacheFileReport($file, $config);
            } else {
                /*
                    Running interactively.
                    Print the error report for the current file and then wait for user input.
                */

                // Get current violations and then clear the list to make sure
                // we only print violations for a single file each time.
                $numErrors = null;
                while ($numErrors !== 0) {
                    $numErrors = ($phpcsFile->getErrorCount() + $phpcsFile->getWarningCount());
                    if ($numErrors === 0) {
                        continue;
                    }

                    $reportClass = $this->reporter->factory('full');
                    $reportData  = $this->reporter->prepareFileReport($phpcsFile);
                    $reportClass->generateFileReport($reportData, $phpcsFile, $cliValues['showSources'], $cliValues['reportWidth']);

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
                        $this->populateTokenListeners();
                        $phpcsFile = $this->_processFile($file, $contents);
                        break;
                    }
                }//end while
            }

            // Clean up the file to save (a lot of) memory.
            $file->cleanUp();

            $numProcessed++;

            if (PHP_CODESNIFFER_VERBOSITY > 0
                || $config->interactive === true
                || $config->showProgress === false
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
                    if ($config->colors === true) {
                        echo "\033[31m";
                    }

                    echo 'E';
                } else if ($warnings > 0) {
                    if ($config->colors === true) {
                        echo "\033[33m";
                    }

                    echo 'W';
                } else {
                    echo '.';
                }

                if ($config->colors === true) {
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
            && $config->interactive === false
            && $config->showProgress === true
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

        $toScreen = $reporter->printReports();

        // Only print timer output if no reports were
        // printed to the screen so we don't put additional output
        // in something like an XML report. If we are printing to screen,
        // the report types would have already worked out who should
        // print the timer info.
        if ($config->interactive === false
            && ($toScreen === false
            || (($reporter->totalErrors + $reporter->totalWarnings) === 0 && $config->showProgress === true))
        ) {
            Util\Timing::printRunTime();
        }

        $ignoreWarnings = $config->getConfigData('ignore_warnings_on_exit');
        $ignoreErrors   = $config->getConfigData('ignore_errors_on_exit');

        $return = ($reporter->totalErrors + $reporter->totalWarnings);
        if ($ignoreErrors !== null) {
            $ignoreErrors = (bool) $ignoreErrors;
            if ($ignoreErrors === true) {
                $return -= $reporter->totalErrors;
            }
        }

        if ($ignoreWarnings !== null) {
            $ignoreWarnings = (bool) $ignoreWarnings;
            if ($ignoreWarnings === true) {
                $return -= $reporter->totalWarnings;
            }
        }

        return $return;

    }//end run()



}//end class
