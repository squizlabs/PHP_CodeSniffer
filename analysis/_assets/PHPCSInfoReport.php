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
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                 $report      Prepared report data.
     * @param \PHP_CodeSniffer\File $phpcsFile   The file being reported on.
     * @param bool                  $showSources Show sources?
     * @param int                   $width       Maximum allowed line width.
     *
     * @return bool
     */
    public function generateFileReport($report, File $phpcsFile, $showSources=false, $width=80)
    {
        $metrics = $phpcsFile->getMetrics();
        foreach ($metrics as $metric => $data) {
            foreach ($data['values'] as $value => $count) {
                echo "\"$metric\"|\"$value\"|$count".PHP_EOL;
            }
        }

        return true;

    }//end generateFileReport()


    /**
     * Prints a summary of fixed files.
     *
     * @param string $cachedData    Any partial report data that was returned from
     *                              generateFileReport during the run.
     * @param int    $totalFiles    Total number of files processed during the run.
     * @param int    $totalErrors   Total number of errors found during the run.
     * @param int    $totalWarnings Total number of warnings found during the run.
     * @param int    $totalFixable  Total number of problems that can be fixed.
     * @param bool   $showSources   Show sources?
     * @param int    $width         Maximum allowed line width.
     * @param bool   $interactive   Are we running in interactive mode?
     * @param bool   $toScreen      Is the report being printed to screen?
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
        $project = Config::getConfigData('project');

        $metricCache = array();

        $lines = explode(PHP_EOL, trim($cachedData));
        foreach ($lines as $line) {
            $parts  = explode('|', $line);
            $metric = trim($parts[0], '"');
            $value  = trim($parts[1], '"');
            $count  = $parts[2];

            if (isset($metricCache['metrics'][$metric]) === false) {
                $metricCache['metrics'][$metric] = array(
                                                    'total'       => 0,
                                                    'values'      => array(),
                                                    'percentages' => array(),
                                                    'trends'      => array(),
                                                   );
            }

            if (isset($metricCache['metrics'][$metric]['values'][$value]) === false) {
                $metricCache['metrics'][$metric]['values'][$value] = $count;
            } else {
                $metricCache['metrics'][$metric]['values'][$value] += $count;
            }

            $metricCache['metrics'][$metric]['total'] += $count;
        }//end foreach

        foreach ($metricCache['metrics'] as $metric => $data) {
            ksort($metricCache['metrics'][$metric]['values']);

            $metricCache['metrics'][$metric]['percentages'] = array();
            foreach ($metricCache['metrics'][$metric]['values'] as $value => $count) {
                $percent = round(($count / $metricCache['metrics'][$metric]['total'] * 100), 2);
                $metricCache['metrics'][$metric]['percentages'][$value] = $percent;
            }

            ksort($metricCache['metrics'][$metric]['percentages']);
        }

        $output = array();
        $cmd    = 'cd '.__DIR__.'/../'.$project.'/src; git log -n 1 --pretty=format:%H;';
        exec($cmd, $output);

        $metricCache['project'] = array(
                                   'path'     => $project,
                                   'commitid' => $output[0],
                                  );

        echo json_encode($metricCache, JSON_FORCE_OBJECT);

    }//end generate()


}//end class
