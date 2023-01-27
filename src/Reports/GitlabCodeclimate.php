<?php
/**
 * GitLab Code Climate report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;

class GitlabCodeclimate implements Report
{

    /**
     * A numeric to string map of severity keywords.
     *
     * @var string[]
     */
    private static $severityMap = [
        1 => 'info',
        2 => 'minor',
        3 => 'major',
        4 => 'critical',
        5 => 'blocker',
    ];


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
        if ((int) $report['errors'] === 0 && (int) $report['warnings'] === 0) {
            return false;
        }

        $messages = '';
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $messageObject = [
                        'categories'  => ['Bug Risk'],
                        'check_name'  => $error['source'],
                        'description' => $error['message'],
                        'fingerprint' => sha1("{$report['filename']}:{$line}:{$column}:{$error['message']}"),
                        'location'    => [
                            'path'  => $report['filename'],
                            'lines' => ['begin' => $line],
                        ],
                        'severity'    => self::$severityMap[(int) $error['severity']],
                        'type'        => 'issue',
                    ];

                    $messages .= json_encode($messageObject).',';
                }
            }//end foreach
        }//end foreach

        echo $messages;

        return true;

    }//end generateFileReport()


    /**
     * Generates a GitLab Code Climate report.
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
        echo '[';
        echo rtrim($cachedData, ',');
        echo ']'.PHP_EOL;

    }//end generate()


}//end class
