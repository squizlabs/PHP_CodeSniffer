<?php
/**
 * Diff report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util;

class Diff implements Report
{


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
        $errors = $phpcsFile->getFixableCount();
        if ($errors === 0) {
            return false;
        }

        $phpcsFile->disableCaching();
        $tokens = $phpcsFile->getTokens();
        if (empty($tokens) === true) {
            if (PHP_CODESNIFFER_VERBOSITY === 1) {
                $startTime = microtime(true);
                echo 'DIFF report is parsing '.basename($report['filename']).' ';
            } else if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo 'DIFF report is forcing parse of '.$report['filename'].PHP_EOL;
            }

            $phpcsFile->parse();

            if (PHP_CODESNIFFER_VERBOSITY === 1) {
                $timeTaken = ((microtime(true) - $startTime) * 1000);
                if ($timeTaken < 1000) {
                    $timeTaken = round($timeTaken);
                    echo "DONE in {$timeTaken}ms";
                } else {
                    $timeTaken = round(($timeTaken / 1000), 2);
                    echo "DONE in $timeTaken secs";
                }

                echo PHP_EOL;
            }

            $phpcsFile->fixer->startFile($phpcsFile);
        }//end if

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            ob_end_clean();
            echo "\t*** START FILE FIXING ***".PHP_EOL;
        }

        $fixed = $phpcsFile->fixer->fixFile();

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END FILE FIXING ***".PHP_EOL;
            ob_start();
        }

        if ($fixed === false) {
            return false;
        }

        $diff = $phpcsFile->fixer->generateDiff();
        if ($diff === '') {
            // Nothing to print.
            return false;
        }

        echo $diff.PHP_EOL;
        return true;

    }//end generateFileReport()


    /**
     * Prints all errors and warnings for each file processed.
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
        echo $cachedData;
        if ($toScreen === true && $cachedData !== '') {
            echo PHP_EOL;
        }

    }//end generate()


}//end class
