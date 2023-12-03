<?php
/**
 * CBF report for PHP_CodeSniffer.
 *
 * This report implements the various auto-fixing features of the
 * PHPCBF script and is not intended (or allowed) to be selected as a
 * report from the command line.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util;

class Cbf implements Report
{


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                       $report      Prepared report data.
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being reported on.
     * @param bool                        $showSources Show sources?
     * @param int                         $width       Maximum allowed line width.
     *
     * @return bool
     * @throws \PHP_CodeSniffer\Exceptions\DeepExitException
     */
    public function generateFileReport($report, File $phpcsFile, $showSources=false, $width=80)
    {
        $errors = $phpcsFile->getFixableCount();
        if ($errors !== 0) {
            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                ob_end_clean();
                $startTime = microtime(true);
                echo "\t=> Fixing file: $errors/$errors violations remaining";
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo PHP_EOL;
                }
            }

            $fixed = $phpcsFile->fixer->fixFile();
        }

        if ($phpcsFile->config->stdin === true) {
            // Replacing STDIN, so output current file to STDOUT
            // even if nothing was fixed. Exit here because we
            // can't process any more than 1 file in this setup.
            $fixedContent = $phpcsFile->fixer->getContents();
            throw new DeepExitException($fixedContent, 1);
        }

        if ($errors === 0) {
            return false;
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
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
        }

        if ($fixed === true) {
            // The filename in the report may be truncated due to a basepath setting
            // but we are using it for writing here and not display,
            // so find the correct path if basepath is in use.
            $newFilename = $report['filename'].$phpcsFile->config->suffix;
            if ($phpcsFile->config->basepath !== null) {
                $newFilename = $phpcsFile->config->basepath.DIRECTORY_SEPARATOR.$newFilename;
            }

            $newContent = $phpcsFile->fixer->getContents();
            file_put_contents($newFilename, $newContent);

            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                if ($newFilename === $report['filename']) {
                    echo "\t=> File was overwritten".PHP_EOL;
                } else {
                    echo "\t=> Fixed file written to ".basename($newFilename).PHP_EOL;
                }
            }
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            ob_start();
        }

        $errorCount   = $phpcsFile->getErrorCount();
        $warningCount = $phpcsFile->getWarningCount();
        $fixableCount = $phpcsFile->getFixableCount();
        $fixedCount   = ($errors - $fixableCount);
        echo $report['filename'].">>$errorCount>>$warningCount>>$fixableCount>>$fixedCount".PHP_EOL;

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
        $lines = explode(PHP_EOL, $cachedData);
        array_pop($lines);

        if (empty($lines) === true) {
            echo PHP_EOL.'No fixable errors were found'.PHP_EOL;
            return;
        }

        $reportFiles = [];
        $maxLength   = 0;
        $totalFixed  = 0;
        $failures    = 0;

        foreach ($lines as $line) {
            $parts   = explode('>>', $line);
            $fileLen = strlen($parts[0]);
            $reportFiles[$parts[0]] = [
                'errors'   => $parts[1],
                'warnings' => $parts[2],
                'fixable'  => $parts[3],
                'fixed'    => $parts[4],
                'strlen'   => $fileLen,
            ];

            $maxLength = max($maxLength, $fileLen);

            $totalFixed += $parts[4];

            if ($parts[3] > 0) {
                $failures++;
            }
        }

        $width = min($width, ($maxLength + 21));
        $width = max($width, 70);

        echo PHP_EOL."\033[1m".'PHPCBF RESULT SUMMARY'."\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;
        echo "\033[1m".'FILE'.str_repeat(' ', ($width - 20)).'FIXED  REMAINING'."\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        foreach ($reportFiles as $file => $data) {
            $padding = ($width - 18 - $data['strlen']);
            if ($padding < 0) {
                $file    = '...'.substr($file, (($padding * -1) + 3));
                $padding = 0;
            }

            echo $file.str_repeat(' ', $padding).'  ';

            if ($data['fixable'] > 0) {
                echo "\033[31mFAILED TO FIX\033[0m".PHP_EOL;
                continue;
            }

            $remaining = ($data['errors'] + $data['warnings']);

            if ($data['fixed'] !== 0) {
                echo $data['fixed'];
                echo str_repeat(' ', (7 - strlen((string) $data['fixed'])));
            } else {
                echo '0      ';
            }

            if ($remaining !== 0) {
                echo $remaining;
            } else {
                echo '0';
            }

            echo PHP_EOL;
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        echo "\033[1mA TOTAL OF $totalFixed ERROR";
        if ($totalFixed !== 1) {
            echo 'S';
        }

        $numFiles = count($reportFiles);
        echo ' WERE FIXED IN '.$numFiles.' FILE';
        if ($numFiles !== 1) {
            echo 'S';
        }

        echo "\033[0m";

        if ($failures > 0) {
            echo PHP_EOL.str_repeat('-', $width).PHP_EOL;
            echo "\033[1mPHPCBF FAILED TO FIX $failures FILE";
            if ($failures !== 1) {
                echo 'S';
            }

            echo "\033[0m";
        }

        echo PHP_EOL.str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if ($toScreen === true && $interactive === false) {
            Util\Timing::printRunTime();
        }

    }//end generate()


}//end class
