<?php
/**
 * SonarQube report for PHP_CodeSniffer.
 *
 * @author  Marek Víger <marek.viger@gmail.com>
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;

class Sonar implements Report
{


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array $report      Prepared report data.
     * @param File  $phpcsFile   The file being reported on.
     * @param bool  $showSources Show sources?
     * @param int   $width       Maximum allowed line width.
     *
     * @return bool
     */
    public function generateFileReport($report, File $phpcsFile, $showSources=false, $width=80)
    {
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $error['message'] = strtr(
                        $error['message'],
                        [
                            "\n" => '\n',
                            "\r" => '\r',
                            "\t" => '\t',
                        ]
                    );

                    $issue = [
                        'engineId'        => 'PHP_CodeSniffer',
                        'ruleId'          => $error['source'],
                        'type'            => 'CODE_SMELL',
                        'severity'        => $this->convertErrorTypeToSonarSeverity($error['type']),
                        'primaryLocation' => [
                            'message'   => $error['message'],
                            'filePath'  => $report['filename'],
                            'textRange' => [
                                'startLine'   => $line,
                                'endLine'     => $line,
                                'startColumn' => $column,
                                'endColumn'   => $column,
                            ],
                        ],
                    ];

                    echo json_encode($issue).',';
                }//end foreach
            }//end foreach
        }//end foreach

        return true;

    }//end generateFileReport()


    /**
     * Generates a SonarQube report.
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
     *
     * @see https://docs.sonarqube.org/latest/analysis/generic-issue/
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
        printf('{"issues":[%s]}', rtrim($cachedData, ','));

    }//end generate()


    /**
     * Convert error type to SonarQube severity key
     *
     * @param string $type error type
     *
     * @return string
     */
    private function convertErrorTypeToSonarSeverity($type)
    {
        if ($type === 'ERROR') {
            return 'MAJOR';
        }

        if ($type === 'WARNING') {
            return 'MINOR';
        }

        return 'INFO';

    }//end convertErrorTypeToSonarSeverity()


}//end class
