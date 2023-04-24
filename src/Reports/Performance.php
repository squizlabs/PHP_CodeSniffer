<?php
/**
 * Performance report for PHP_CodeSniffer.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2023 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Timing;

class Performance implements Report
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
        $times = $phpcsFile->getListenerTimes();
        foreach ($times as $sniff => $time) {
            echo "$sniff>>$time".PHP_EOL;
        }

        return true;

    }//end generateFileReport()


    /**
     * Prints the sniff performance report.
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

        // First collect the accumulated timings.
        $timings        = [];
        $totalSniffTime = 0;
        foreach ($lines as $line) {
            $parts      = explode('>>', $line);
            $sniffClass = $parts[0];
            $time       = $parts[1];

            if (isset($timings[$sniffClass]) === false) {
                $timings[$sniffClass] = 0;
            }

            $timings[$sniffClass] += $time;
            $totalSniffTime       += $time;
        }

        // Next, tidy up the sniff names and determine max needed column width.
        $totalTimes   = [];
        $maxNameWidth = 0;
        foreach ($timings as $sniffClass => $secs) {
            $sniffCode    = Common::getSniffCode($sniffClass);
            $maxNameWidth = max($maxNameWidth, strlen($sniffCode));
            $totalTimes[$sniffCode] = $secs;
        }

        // Leading space + up to 12 chars for the number.
        $maxTimeWidth = 13;
        // Leading space, open parenthesis, up to 5 chars for the number, space + % and close parenthesis.
        $maxPercWidth = 10;
        // Calculate the maximum width available for the sniff name.
        $maxNameWidth = min(($width - $maxTimeWidth - $maxPercWidth), max(($width - $maxTimeWidth - $maxPercWidth), $maxNameWidth));

        arsort($totalTimes);

        echo PHP_EOL."\033[1m".'PHP CODE SNIFFER SNIFF PERFORMANCE REPORT'."\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;
        echo "\033[1m".'SNIFF'.str_repeat(' ', ($width - 31)).'TIME TAKEN (SECS)     (%)'."\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        // Mark sniffs which take more than twice as long as the average processing time per sniff
        // in orange and when they take more than three times as long as the average,
        // mark them in red.
        $avgSniffTime       = ($totalSniffTime / count($totalTimes));
        $doubleAvgSniffTime = (2 * $avgSniffTime);
        $tripleAvgSniffTime = (3 * $avgSniffTime);

        $format        = "%- {$maxNameWidth}.{$maxNameWidth}s % 12.6f (% 5.1f %%)".PHP_EOL;
        $formatBold    = "\033[1m%- {$maxNameWidth}.{$maxNameWidth}s % 12.6f (% 5.1f %%)\033[0m".PHP_EOL;
        $formatWarning = "%- {$maxNameWidth}.{$maxNameWidth}s \033[33m% 12.6f (% 5.1f %%)\033[0m".PHP_EOL;
        $formatError   = "%- {$maxNameWidth}.{$maxNameWidth}s \033[31m% 12.6f (% 5.1f %%)\033[0m".PHP_EOL;

        foreach ($totalTimes as $sniff => $time) {
            $percent = round((($time / $totalSniffTime) * 100), 1);

            if ($time > $tripleAvgSniffTime) {
                printf($formatError, $sniff, $time, $percent);
            } else if ($time > $doubleAvgSniffTime) {
                printf($formatWarning, $sniff, $time, $percent);
            } else {
                printf($format, $sniff, $time, $percent);
            }
        }

        echo str_repeat('-', $width).PHP_EOL;
        printf($formatBold, 'TOTAL SNIFF PROCESSING TIME', $totalSniffTime, 100);

        $runTime   = (Timing::getDuration() / 1000);
        $phpcsTime = ($runTime - $totalSniffTime);

        echo PHP_EOL.str_repeat('-', $width).PHP_EOL;
        printf($format, 'Time taken by sniffs', $totalSniffTime, round((($totalSniffTime / $runTime) * 100), 1));
        printf($format, 'Time taken by PHPCS runner', $phpcsTime, round((($phpcsTime / $runTime) * 100), 1));

        echo str_repeat('-', $width).PHP_EOL;
        printf($formatBold, 'TOTAL RUN TIME', $runTime, 100);
        echo str_repeat('-', $width).PHP_EOL;

    }//end generate()


}//end class
