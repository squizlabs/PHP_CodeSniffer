<?php

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Reports\Report;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Exceptions\RuntimeException;

/**
 * A class to manage reporting.
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

/**
 * A class to manage reporting.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Reporter
{

    /**
     * Total number of files that contain errors or warnings.
     *
     * @var int
     */
    public $totalFiles = 0;

    /**
     * Total number of errors found during the run.
     *
     * @var int
     */
    public $totalErrors = 0;

    /**
     * Total number of warnings found during the run.
     *
     * @var int
     */
    public $totalWarnings = 0;

    /**
     * Total number of errors/warnings that can be fixed.
     *
     * @var int
     */
    public $totalFixable = 0;

    /**
     * When the PHPCS run started.
     *
     * @var float
     */
    public static $startTime = 0;

    /**
     * A list of reports that have written partial report output.
     *
     * @var array
     */
    private $_cachedReports = array();

    /**
     * A cache of report objects.
     *
     * @var array
     */
    private $_reports = array();

    /**
     * A cache of opened tmp files.
     *
     * @var array
     */
    private $_tmpFiles = array();
    protected $config  = null;


    /**
     * Produce the appropriate report object based on $type parameter.
     *
     * @param string $type The type of the report.
     *
     * @return PHP_CodeSniffer_Report
     * @throws PHP_CodeSniffer_Exception If report is not available.
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

            $this->_reports[$type] = array(
                                      'output' => $output,
                                      'class'  => $reportClass,
                                     );

            if ($output === null) {
                // Using a temp file.
                $this->_tmpFiles[$type] = tempnam(sys_get_temp_dir(), 'phpcs');
                file_put_contents($this->_tmpFiles[$type], '');
            } else {
                file_put_contents($output, '');
            }
        }//end foreach

    }//end __construct()


    /**
     * Actually generates the report.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file that has been processed.
     * @param array                $cliValues An array of command line arguments.
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

        foreach ($this->_reports as $type => $report) {
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
                file_put_contents($this->_tmpFiles[$type], $generatedReport, FILE_APPEND);
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
     * Generates and prints a final report.
     *
     * Returns an array with the number of errors and the number of
     * warnings, in the form ['errors' => int, 'warnings' => int].
     *
     * @param string  $report      Report type.
     * @param boolean $showSources Show sources?
     * @param array   $cliValues   An array of command line arguments.
     * @param string  $reportFile  Report file to generate.
     * @param integer $reportWidth Report max width.
     *
     * @return int[]
     */
    public function printReport($report)
    {
        $report      = ucfirst($report);
        $reportClass = $this->_reports[$report]['class'];
        $reportFile  = $this->_reports[$report]['output'];

        if ($reportFile !== null) {
            $filename = $reportFile;
            $toScreen = false;
        } else {
            if (isset($this->_tmpFiles[$report]) === true) {
                $filename = $this->_tmpFiles[$report];
            } else {
                $filename = null;
            }

            $toScreen = true;
        }

        $reportCache = '';
        if ($filename !== null) {
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
            $generatedReport = preg_replace('`\033\[[0-9]+m`', '', $generatedReport);
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


    public function printReports()
    {
        $toScreen = false;
        foreach ($this->_reports as $type => $report) {
            if ($report['output'] === null) {
                $toScreen = true;
            }

            $this->printReport($type);
        }

        return $toScreen;

    }//end printReports()


    /**
     * Pre-process and package violations for all files.
     *
     * Used by error reports to get a packaged list of all errors in each file.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file that has been processed.
     *
     * @return array
     */
    public function prepareFileReport(File $phpcsFile)
    {
        $report = array(
                   'filename' => $phpcsFile->getFilename(),
                   'errors'   => $phpcsFile->getErrorCount(),
                   'warnings' => $phpcsFile->getWarningCount(),
                   'fixable'  => $phpcsFile->getFixableCount(),
                   'messages' => array(),
                  );

        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Prefect score!
            return $report;
        }

        $errors = array();

        // Merge errors and warnings.
        foreach ($phpcsFile->getErrors() as $line => $lineErrors) {
            if (is_array($lineErrors) === false) {
                continue;
            }

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
                }//end foreach

                $errors[$line][$column] = $newErrors;
            }//end foreach

            ksort($errors[$line]);
        }//end foreach

        foreach ($phpcsFile->getWarnings() as $line => $lineWarnings) {
            if (is_array($lineWarnings) === false) {
                continue;
            }

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
                }//end foreach

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
