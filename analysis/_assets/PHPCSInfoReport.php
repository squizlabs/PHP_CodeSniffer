<?php
namespace Coding_Standards_Analysis\Reports;

use PHP_CodeSniffer\Reports\Report;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Config;

/**
 * Info report for PHP_CodeSniffer.
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
 * Info report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHPCSInfoReport implements Report
{

    /**
     * TRUE if this report needs error messages instead of just totals.
     *
     * @var boolean
     */
    public $recordErrors = false;

    /**
     * A cache of metrics collected during the run.
     *
     * @var array
     */
    private $_metricCache = array();

    /**
     * The project we are generating data for.
     *
     * @var string
     */
    private $_project = null;


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                $report      Prepared report data.
     * @param PHP_CodeSniffer_File $phpcsFile   The file being reported on.
     * @param boolean              $showSources Show sources?
     * @param int                  $width       Maximum allowed line width.
     *
     * @return boolean
     */
    public function generateFileReport($report, File $phpcsFile, $showSources=false, $width=80)
    {
        if ($this->_project === null) {
            $this->_project = Config::getConfigData('project');
        }

        $metrics = $phpcsFile->getMetrics();
        foreach ($metrics as $metric => $data) {
            if (isset($this->_metricCache['metrics'][$metric]) === false) {
                $this->_metricCache['metrics'][$metric] = array(
                                                           'total'       => 0,
                                                           'values'      => array(),
                                                           'percentages' => array(),
                                                           'trends'      => array(),
                                                          );
            }

            foreach ($data['values'] as $value => $count) {
                if (isset($this->_metricCache['metrics'][$metric]['values'][$value]) === false) {
                    $this->_metricCache['metrics'][$metric]['values'][$value] = $count;
                } else {
                    $this->_metricCache['metrics'][$metric]['values'][$value] += $count;
                }

                $this->_metricCache['metrics'][$metric]['total'] += $count;
            }
        }//end foreach

        return true;

    }//end generateFileReport()


    /**
     * Prints the source of all errors and warnings.
     *
     * @param string  $cachedData    Any partial report data that was returned from
     *                               generateFileReport during the run.
     * @param int     $totalFiles    Total number of files processed during the run.
     * @param int     $totalErrors   Total number of errors found during the run.
     * @param int     $totalWarnings Total number of warnings found during the run.
     * @param int     $totalFixable  Total number of problems that can be fixed.
     * @param boolean $showSources   Show sources?
     * @param int     $width         Maximum allowed line width.
     * @param boolean $toScreen      Is the report being printed to screen?
     *
     * @return void
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $totalFixable,
        $showSources=false,
        $width=80,
        $interactive=false,
        $toScreen=true
    ) {
        foreach ($this->_metricCache['metrics'] as $metric => $data) {
            ksort($this->_metricCache['metrics'][$metric]['values']);

            $this->_metricCache['metrics'][$metric]['percentages'] = array();
            foreach ($this->_metricCache['metrics'][$metric]['values'] as $value => $count) {
                $percent = round(($count / $this->_metricCache['metrics'][$metric]['total'] * 100), 2);
                $this->_metricCache['metrics'][$metric]['percentages'][$value] = $percent;
            }

            ksort($this->_metricCache['metrics'][$metric]['percentages']);
        }//end foreach

        $output = array();
        $cmd    = 'cd '.__DIR__.'/../'.$this->_project.'/src; git log -n 1 --pretty=format:%H;';
        exec($cmd, $output);

        $this->_metricCache['project'] = array(
                                          'path'     => $this->_project,
                                          'commitid' => $output[0],
                                         );

        echo json_encode($this->_metricCache, JSON_FORCE_OBJECT);

    }//end generate()


}//end class
