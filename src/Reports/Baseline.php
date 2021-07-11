<?php
/**
 * XML report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use XMLWriter;

class Baseline implements Report
{
    /**
     * Generate a partial report for a single processed file.
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
        $out = new XMLWriter;
        $out->openMemory();
        $out->setIndent(true);
        $out->setIndentString('    ');
        $out->startDocument('1.0', 'UTF-8');

        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return false;
        }

        foreach ($report['messages'] as $lineErrors) {
            foreach ($lineErrors as $colErrors) {
                foreach ($colErrors as $error) {
                    $out->startElement('violation');
                    $out->writeAttribute('file', $report['filename']);
                    $out->writeAttribute('sniff', $error['sniff']);

                    $out->endElement();
                }
            }
        }

        // Remove the start of the document because we will
        // add that manually later. We only have it in here to
        // properly set the encoding.
        $content = $out->flush();
        if (strpos($content, PHP_EOL) !== false) {
            $content = substr($content, (strpos($content, PHP_EOL) + strlen(PHP_EOL)));
        } else if (strpos($content, "\n") !== false) {
            $content = substr($content, (strpos($content, "\n") + 1));
        }

        echo $content;

        return true;
    }

    /**
     * Prints all violations for processed files, in a proprietary XML format.
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
        echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        echo '<phpcs_baseline version="'.Config::VERSION.'">'.PHP_EOL;
        echo $cachedData;
        echo '</phpcs_baseline>'.PHP_EOL;
    }
}
