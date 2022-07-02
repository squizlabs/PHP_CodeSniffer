<?php
/**
 * Github Action report for PHP_CodeSniffer.
 *
 * @author  Alexandros Koutroulis <icyd3mon@gmail.com>
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;

class Github implements Report
{

    /**
     * The name of the report we want in the output
     *
     * @var string
     */
    protected $reportName = 'github';


    /**
     * Generate a partial report for a single processed file.
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
        $messages = '';
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $columnErrors) {
                foreach ($columnErrors as $error) {
                    $type = strtolower($error['type']);

                    if (in_array($type, ['error', 'warning', 'notice']) === false) {
                        $type = 'error';
                    }

                    $message = sprintf(
                        '::%s file=%s,line=%d,col=%d,title=%s::%s',
                        $type,
                        $report['filename'],
                        $line,
                        $column,
                        $error['source'],
                        $error['message']
                    );

                    $messages .= sprintf("%s%s", $message, PHP_EOL);
                }//end foreach
            }//end foreach
        }//end foreach

        echo $messages;

        return true;

    }//end generateFileReport()


    /**
     * Generates a report for GitHub actions.
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
        echo sprintf("%s%s", $cachedData, PHP_EOL);

    }//end generate()


}//end class
