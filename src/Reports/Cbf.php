<?php


/**
 * CBF report for Symplify\PHP7_CodeSniffer.
 *
 * This report implements the various auto-fixing features of the
 * PHPCBF script and is not intended (or allowed) to be selected as a
 * report from the command line.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer\Reports;

use Symplify\PHP7_CodeSniffer\Files\File;

class Cbf implements Report
{
    /**
     * {@inheritdoc}
     */
    public function generateFileReport(array $report, File $phpcsFile, bool $showSources=false, int $width=80) : bool
    {
        $errors = $phpcsFile->getFixableCount();
        if ($errors !== 0) {
            ob_end_clean();
            $startTime = microtime(true);
            echo "\t=> Fixing file: $errors/$errors violations remaining";

            $fixed = $phpcsFile->fixer->fixFile();
        }

        if ($errors === 0) {
            return false;
        }

        if ($fixed === false) {
            echo 'ERROR';
        } else {
            echo 'DONE';
        }

        $timeTaken = ((microtime(true) - $startTime) * 1000);
        if ($timeTaken < 1000) {
            $timeTaken = round($timeTaken);
            echo " in {$timeTaken}ms".PHP_EOL;
        } else {
            $timeTaken = round(($timeTaken / 1000), 2);
            echo " in $timeTaken secs".PHP_EOL;
        }

        if ($fixed === true) {
            $newFilename = $report['filename'];
            $newContent  = $phpcsFile->fixer->getContents();
            file_put_contents($newFilename, $newContent);

            echo "\t=> File was overwritten".PHP_EOL;
        }

        ob_start();

        // This output is for the report and not printed to screen.
        if ($fixed === false) {
            echo 'E|';
        } else {
            echo $errors.'|';
        }

        return $fixed;

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
        $fixed = 0;
        $fails = 0;

        $errorCounts = explode('|', rtrim($cachedData, '|'));
        foreach ($errorCounts as $count) {
            if ($count === 'E') {
                $fails++;
            } else {
                $fixed += $count;
            }
        }

        echo PHP_EOL;

        if ($fixed === 0) {
            echo 'No fixable errors were found';
        } else {
            echo "Fixed $fixed errors in $totalFiles file";
            if ($totalFiles !== 1) {
                echo 's';
            }
        }

        if ($fails > 0) {
            echo "; failed fixing $fails file";
            if ($fails !== 1) {
                echo 's';
            }
        }

        echo PHP_EOL;

    }//end generate()


}//end class
