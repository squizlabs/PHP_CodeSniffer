<?php
/**
 * Summary report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util;

class Summary implements Report
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
        if (PHP_CODESNIFFER_VERBOSITY === 0
            && $report['errors'] === 0
            && $report['warnings'] === 0
        ) {
            // Nothing to print.
            return false;
        }

        echo $report['filename'].'>>'.$report['errors'].'>>'.$report['warnings'].PHP_EOL;
        return true;

    }//end generateFileReport()


    /**
     * Generates a summary of errors and warnings for each file processed.
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

        $reportFiles = [];
        $maxLength   = 0;

        foreach ($lines as $line) {
            $parts   = explode('>>', $line);
            $fileLen = strlen($parts[0]);
            $reportFiles[$parts[0]] = [
                'errors'   => $parts[1],
                'warnings' => $parts[2],
                'strlen'   => $fileLen,
            ];

            $maxLength = max($maxLength, $fileLen);
        }

        $width = min($width, ($maxLength + 21));
        $width = max($width, 70);

        echo PHP_EOL."\033[1m".'PHP CODE SNIFFER REPORT SUMMARY'."\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;
        echo "\033[1m".'FILE'.str_repeat(' ', ($width - 20)).'ERRORS  WARNINGS'."\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        foreach ($reportFiles as $file => $data) {
            $padding = ($width - 18 - $data['strlen']);
            if ($padding < 0) {
                $file    = '...'.substr($file, (($padding * -1) + 3));
                $padding = 0;
            }

            echo $file.str_repeat(' ', $padding).'  ';
            if ($data['errors'] !== 0) {
                echo "\033[31m".$data['errors']."\033[0m";
                echo str_repeat(' ', (8 - strlen((string) $data['errors'])));
            } else {
                echo '0       ';
            }

            if ($data['warnings'] !== 0) {
                echo "\033[33m".$data['warnings']."\033[0m";
            } else {
                echo '0';
            }

            echo PHP_EOL;
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        echo "\033[1mA TOTAL OF $totalErrors ERROR";
        if ($totalErrors !== 1) {
            echo 'S';
        }

        echo ' AND '.$totalWarnings.' WARNING';
        if ($totalWarnings !== 1) {
            echo 'S';
        }

        echo ' WERE FOUND IN '.$totalFiles.' FILE';
        if ($totalFiles !== 1) {
            echo 'S';
        }

        echo "\033[0m";

        if ($totalFixable > 0) {
            echo PHP_EOL.str_repeat('-', $width).PHP_EOL;
            echo "\033[1mPHPCBF CAN FIX $totalFixable OF THESE SNIFF VIOLATIONS AUTOMATICALLY\033[0m";
        }

        echo PHP_EOL.str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if ($toScreen === true && $interactive === false) {
            Util\Timing::printRunTime();
        }

    }//end generate()


}//end class
