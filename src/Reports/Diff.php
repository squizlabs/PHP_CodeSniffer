<?php
/**
 * Diff report for Symplify\PHP7_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer\Reports;

use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Util;

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
     * @param \Symplify\PHP7_CodeSniffer\File $phpcsFile   The file being reported on.
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
            $phpcsFile->parse();
            $phpcsFile->fixer->startFile($phpcsFile);
        }//end if

        if (PHP_CodeSniffer_CBF === true) {
            ob_end_clean();
            $startTime = microtime(true);
            echo "\t=> Fixing file: $errors/$errors violations remaining";
        }

        $fixed = $phpcsFile->fixer->fixFile();

        if (PHP_CodeSniffer_CBF === true) {
            if ($fixed === false) {
                echo "\033[31mERROR\033[0m";
            } else {
                echo "\033[32mDONE\033[0m";
            }

            $timeTaken = ((microtime(true) - $startTime) * 1000);
            if ($timeTaken < 1000) {
                $timeTaken = round($timeTaken);
                echo " in {$timeTaken}ms".PHP_EOL;
            } else {
                $timeTaken = round(($timeTaken / 1000), 2);
                echo " in $timeTaken secs".PHP_EOL;
            }

            ob_start();
        }

        if ($fixed === false) {
            return false;
        }

        if (PHP_CodeSniffer_CBF === true) {
            // Diff without colours.
            $diff = $phpcsFile->fixer->generateDiff(null, false);
        } else {
            $diff = $phpcsFile->fixer->generateDiff();
        }

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
        if ($toScreen === true) {
            echo PHP_EOL;
        }

    }//end generate()


}//end class
