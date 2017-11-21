<?php
/**
 * Info report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Timing;

class Info implements Report
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
        $metrics = $phpcsFile->getMetrics();
        foreach ($metrics as $metric => $data) {
            foreach ($data['values'] as $value => $count) {
                echo "$metric>>$value>>$count".PHP_EOL;
            }
        }

        return true;

    }//end generateFileReport()


    /**
     * Prints the source of all errors and warnings.
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
        $lines = explode(PHP_EOL, $cachedData);
        array_pop($lines);

        if (empty($lines) === true) {
            return;
        }

        $metrics = [];
        foreach ($lines as $line) {
            $parts  = explode('>>', $line);
            $metric = $parts[0];
            $value  = $parts[1];
            $count  = $parts[2];
            if (isset($metrics[$metric]) === false) {
                $metrics[$metric] = [];
            }

            if (isset($metrics[$metric][$value]) === false) {
                $metrics[$metric][$value] = $count;
            } else {
                $metrics[$metric][$value] += $count;
            }
        }

        ksort($metrics);

        echo PHP_EOL."\033[1m".'PHP CODE SNIFFER INFORMATION REPORT'."\033[0m".PHP_EOL;
        echo str_repeat('-', 70).PHP_EOL;

        foreach ($metrics as $metric => $values) {
            $winner      = '';
            $winnerCount = 0;
            $totalCount  = 0;
            foreach ($values as $value => $count) {
                $totalCount += $count;
                if ($count > $winnerCount) {
                    $winner      = $value;
                    $winnerCount = $count;
                }
            }

            $winPercent = round(($winnerCount / $totalCount * 100), 2);
            echo "$metric: \033[4m$winner\033[0m [$winnerCount/$totalCount, $winPercent%]".PHP_EOL;

            asort($values);
            $values = array_reverse($values, true);
            foreach ($values as $value => $count) {
                if ($value === $winner) {
                    continue;
                }

                $percent = round(($count / $totalCount * 100), 2);
                echo "\t$value => $count ($percent%)".PHP_EOL;
            }

            echo PHP_EOL;
        }//end foreach

        echo str_repeat('-', 70).PHP_EOL;

        if ($toScreen === true && $interactive === false) {
            Timing::printRunTime();
        }

    }//end generate()


}//end class
