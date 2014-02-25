<?php
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
class PHP_CodeSniffer_Reports_PHPCSInfoReport implements PHP_CodeSniffer_Report
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
    public function generateFileReport(
        $report,
        PHP_CodeSniffer_File $phpcsFile,
        $showSources=false,
        $width=80
    ) {
        $metrics = $phpcsFile->getMetrics();
        foreach ($metrics as $metric => $data) {
            asort($data['sniffs']);
            $sniff    = $data['sniffs'][0];
            $parts    = explode('.', $sniff);
            $category = $parts[1];

            if (isset($this->_metricCache[$category]) === false) {
                $this->_metricCache[$category] = array();
            }

            if (isset($this->_metricCache[$category][$metric]) === false) {
                $this->_metricCache[$category][$metric] = array(
                                                           'sniffs' => $data['sniffs'],
                                                           'total'  => 0,
                                                           'values' => array(),
                                                          );
            } else {
                $this->_metricCache[$category][$metric]['sniffs'] += $data['sniffs'];
                $this->_metricCache[$category][$metric]['sniffs']  = array_unique($this->_metricCache[$category][$metric]['sniffs']);
            }

            foreach ($data['values'] as $value => $locations) {
                $count = count(array_unique($locations));

                if (isset($this->_metricCache[$category][$metric]['values'][$value]) === false) {
                    $this->_metricCache[$category][$metric]['values'][$value] = $count;
                } else {
                    $this->_metricCache[$category][$metric]['values'][$value] += $count;
                }

                $this->_metricCache[$category][$metric]['total'] += $count;
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
        $toScreen=true
    ) {
        if (empty($this->_metricCache) === true) {
            // Nothing to show.
            return;
        }

        ksort($this->_metricCache);

        foreach ($this->_metricCache as $cat => $metrics) {
            foreach ($metrics as $metric => $data) {
                asort($this->_metricCache[$cat][$metric]['values']);
                $this->_metricCache[$cat][$metric]['values'] = array_reverse($this->_metricCache[$cat][$metric]['values'], true);

                $this->_metricCache[$cat][$metric]['percentages'] = array();
                foreach ($this->_metricCache[$cat][$metric]['values'] as $value => $count) {
                    $percent = round(($count / $this->_metricCache[$cat][$metric]['total'] * 100), 2);
                    $this->_metricCache[$cat][$metric]['percentages'][$value] = $percent;
                }
            }//end foreach

        }//end foreach

        echo json_encode($this->_metricCache, JSON_FORCE_OBJECT);

    }//end generate()


}//end class
