<?php
/**
 * Baseline report for PHP_CodeSniffer.
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util;
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

        foreach ($report['messages'] as $lineNr => $lineErrors) {
            $signature = Util\CodeSignature::createSignature($phpcsFile->getTokens(), $lineNr);

            foreach ($lineErrors as $colErrors) {
                foreach ($colErrors as $error) {
                    $out->startElement('violation');
                    $out->writeAttribute('file', $report['filename']);
                    $out->writeAttribute('sniff', $error['source']);
                    $out->writeAttribute('signature', $signature);

                    $out->endElement();
                }
            }
        }

        // Remove the start of the document because we will
        // add that manually later. We only have it in here to
        // properly set the encoding.
        $content = $out->flush();
        $content = preg_replace("/[\n\r]/", PHP_EOL, $content);
        $content = substr($content, (strpos($content, PHP_EOL) + strlen(PHP_EOL)));

        echo $content;

        return true;

    }//end generateFileReport()


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
        echo '<phpcs-baseline version="'.Config::VERSION.'">';

        // Split violations on line-ending, make them unique and sort them.
        if ($cachedData !== "") {
            $lines = explode(PHP_EOL, $cachedData);
            $lines = array_unique($lines);
            sort($lines);
            $cachedData = implode(PHP_EOL, $lines);
        }

        echo $cachedData;
        echo PHP_EOL.'</phpcs-baseline>'.PHP_EOL;

    }//end generate()


}//end class
