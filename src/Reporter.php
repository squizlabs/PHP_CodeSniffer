<?php
/**
 * Manages reporting of errors and warnings.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Reports\Report;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Util\Common;

class Reporter
{

    /**
     * The config data for the run.
     *
     * @var \PHP_CodeSniffer\Config
     */
    public $config = null;

    /**
     * Total number of files that contain errors or warnings.
     *
     * @var integer
     */
    public $totalFiles = 0;

    /**
     * Total number of errors found during the run.
     *
     * @var integer
     */
    public $totalErrors = 0;

    /**
     * Total number of warnings found during the run.
     *
     * @var integer
     */
    public $totalWarnings = 0;

    /**
     * Total number of errors/warnings that can be fixed.
     *
     * @var integer
     */
    public $totalFixable = 0;

    /**
     * When the PHPCS run started.
     *
     * @var float
     */
    public static $startTime = 0;

    /**
     * A cache of report objects.
     *
     * @var array
     */
    private $reports = array();

    /**
     * A cache of opened temporary files.
     *
     * @var array
     */
    private $tmpFiles = array();


    /**
     * Initialise the reporter.
     *
     * All reports specified in the config will be created and their
     * output file (or a temp file if none is specified) initialised by
     * clearing the current contents.
     *
     * @param \PHP_CodeSniffer\Config $config The config data for the run.
     *
     * @return void
     * @throws RuntimeException If a report is not available.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        foreach ($config->reports as $type => $output) {
            $type = ucfirst($type);

            if ($output === null) {
                $output = $config->reportFile;
            }

            if (strpos($type, '.') !== false) {
                // This is a path to a custom report class.
                $filename = realpath($type);
                if ($filename === false) {
                    echo "ERROR: Custom report \"$type\" not found".PHP_EOL;
                    exit(2);
                }

                $reportClassName = Autoload::loadFile($filename);
            } else {
                $reportClassName = 'PHP_CodeSniffer\Reports\\'.$type;
            }

            $reportClass = new $reportClassName();
            if (false === ($reportClass instanceof Report)) {
                throw new RuntimeException('Class "'.$reportClassName.'" must implement the "PHP_CodeSniffer\Report" interface.');
            }

            $this->reports[$type] = array(
                                     'output' => $output,
                                     'class'  => $reportClass,
                                    );

            if ($output === null) {
                // Using a temp file.
                $this->tmpFiles[$type] = tempnam(sys_get_temp_dir(), 'phpcs');
                file_put_contents($this->tmpFiles[$type], '');
            } else {
                file_put_contents($output, '');
            }
        }//end foreach

    }//end __construct()


    /**
     * Generates and prints final versions of all reports.
     *
     * Returns TRUE if any of the reports output content to the screen
     * or FALSE if all reports were silently printed to a file.
     *
     * @return bool
     */
    public function printReports()
    {
        $toScreen = false;
        foreach ($this->reports as $type => $report) {
            if ($report['output'] === null) {
                $toScreen = true;
            }

            $this->printReport($type);
        }

        return $toScreen;

    }//end printReports()


    /**
     * Generates and prints a single final report.
     *
     * @param string $report The report type to print.
     *
     * @return void
     */
    public function printReport($report)
    {
        $report      = ucfirst($report);
        $reportClass = $this->reports[$report]['class'];
        $reportFile  = $this->reports[$report]['output'];

        if ($reportFile !== null) {
            $filename = $reportFile;
            $toScreen = false;
        } else {
            if (isset($this->tmpFiles[$report]) === true) {
                $filename = $this->tmpFiles[$report];
            } else {
                $filename = null;
            }

            $toScreen = true;
        }

        $reportCache = '';
        if ($filename !== null && file_exists($filename) === true) {
            $reportCache = file_get_contents($filename);
        }

        ob_start();
        $reportClass->generate(
            $reportCache,
            $this->totalFiles,
            $this->totalErrors,
            $this->totalWarnings,
            $this->totalFixable,
            $this->config->showSources,
            $this->config->reportWidth,
            $this->config->interactive,
            $toScreen
        );
        $generatedReport = ob_get_contents();
        ob_end_clean();

        if ($this->config->colors !== true || $reportFile !== null) {
            $generatedReport = preg_replace('`\033\[[0-9;]+m`', '', $generatedReport);
        }

        if ($reportFile !== null) {
            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                echo $generatedReport;
            }

            file_put_contents($reportFile, $generatedReport.PHP_EOL);
        } else {
            echo $generatedReport;
            if ($filename !== null && file_exists($filename) === true) {
                unlink($filename);
            }
        }

    }//end printReport()


    /**
     * Caches the result of a single processed file for all reports.
     *
     * The report content that is generated is appended to the output file
     * assigned to each report. This content may be an intermediate report format
     * and not reflect the final report output.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file that has been processed.
     *
     * @return void
     */
    public function cacheFileReport(File $phpcsFile)
    {
        if (isset($this->config->reports) === false) {
            // This happens during unit testing, or any time someone just wants
            // the error data and not the printed report.
            return;
        }

        $reportData  = $this->prepareFileReport($phpcsFile);
        $errorsShown = false;

        foreach ($this->reports as $type => $report) {
            $reportClass = $report['class'];

            ob_start();
            $result = $reportClass->generateFileReport($reportData, $phpcsFile, $this->config->showSources, $this->config->reportWidth);
            if ($result === true) {
                $errorsShown = true;
            }

            $generatedReport = ob_get_contents();
            ob_end_clean();

            if ($report['output'] === null) {
                // Using a temp file.
                file_put_contents($this->tmpFiles[$type], $generatedReport, FILE_APPEND);
            } else {
                $flags = FILE_APPEND;
                file_put_contents($report['output'], $generatedReport, FILE_APPEND);
            }//end if
        }//end foreach

        if ($errorsShown === true) {
            $this->totalFiles++;
            $this->totalErrors   += $reportData['errors'];
            $this->totalWarnings += $reportData['warnings'];
            $this->totalFixable  += $reportData['fixable'];
        }

    }//end cacheFileReport()


    /**
     * Generate summary information to be used during report generation.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file that has been processed.
     *
     * @return array
     */
    public function prepareFileReport(File $phpcsFile)
    {
        $report = array(
                   'filename' => Common::stripBasepath($phpcsFile->getFilename(), $this->config->basepath),
                   'errors'   => $phpcsFile->getErrorCount(),
                   'warnings' => $phpcsFile->getWarningCount(),
                   'fixable'  => $phpcsFile->getFixableCount(),
                   'messages' => array(),
                  );

        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Prefect score!
            return $report;
        }

        if ($this->config->recordErrors === false) {
            $message  = 'Errors are not being recorded but this report requires error messages. ';
            $message .= 'This report will not show the correct information.';
            $report['messages'][1][1] = array(
                                         array(
                                          'message'  => $message,
                                          'source'   => 'Internal.RecordErrors',
                                          'severity' => 5,
                                          'fixable'  => false,
                                          'type'     => 'ERROR',
                                         ),
                                        );
            return $report;
        }

        $errors = array();

        // Merge errors and warnings.
        foreach ($phpcsFile->getErrors() as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                $newErrors = array();
                foreach ($colErrors as $data) {
                    $newErrors[] = array(
                                    'message'  => $data['message'],
                                    'source'   => $data['source'],
                                    'severity' => $data['severity'],
                                    'fixable'  => $data['fixable'],
                                    'type'     => 'ERROR',
                                   );
                }

                $errors[$line][$column] = $newErrors;
            }

            ksort($errors[$line]);
        }//end foreach

        foreach ($phpcsFile->getWarnings() as $line => $lineWarnings) {
            foreach ($lineWarnings as $column => $colWarnings) {
                $newWarnings = array();
                foreach ($colWarnings as $data) {
                    $newWarnings[] = array(
                                      'message'  => $data['message'],
                                      'source'   => $data['source'],
                                      'severity' => $data['severity'],
                                      'fixable'  => $data['fixable'],
                                      'type'     => 'WARNING',
                                     );
                }

                if (isset($errors[$line]) === false) {
                    $errors[$line] = array();
                }

                if (isset($errors[$line][$column]) === true) {
                    $errors[$line][$column] = array_merge(
                        $newWarnings,
                        $errors[$line][$column]
                    );
                } else {
                    $errors[$line][$column] = $newWarnings;
                }
            }//end foreach

            ksort($errors[$line]);
        }//end foreach

        ksort($errors);
        $report['messages'] = $errors;
        return $report;

    }//end prepareFileReport()


}//end class
