<?php
/**
 * JUnit report for PHP_CodeSniffer.
 *
 * @author    Oleg Lobach <oleg@lobach.info>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;

class Junit implements Report
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
        $out = new \XMLWriter;
        $out->openMemory();
        $out->setIndent(true);

        $out->startElement('testsuite');
        $out->writeAttribute('name', $report['filename']);
        $out->writeAttribute('errors', 0);

        $classname = pathinfo($report['filename'])['filename'];

        # successful tests
        if (count($report['messages']) === 0) {
            $out->writeAttribute('tests', 1);
            $out->writeAttribute('failures', 0);

            $out->startElement('testcase');
            $out->writeAttribute('classname', $classname);
            $out->writeAttribute('file', $report['filename']);
            # use a generic testcase name if no sniffs were triggered
            $out->writeAttribute('name', 'PHP_CodeSniffer');
            $out->endElement();

        # test failures
        } else {
            $failures = ($report['errors'] + $report['warnings']);
            $out->writeAttribute('tests', $failures);
            $out->writeAttribute('failures', $failures);

            foreach ($report['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $out->startElement('testcase');
                        $out->writeAttribute('classname', $classname);
                        $out->writeAttribute('file', $report['filename']);

                        # add line and column to the sniff name to ensure a testcase has a unique
                        # name even if the same sniff reports more than one violation per file
                        $out->writeAttribute('name', $error['source']." ($line:$column)");

                        $error['type'] = strtolower($error['type']);
                        if ($phpcsFile->config->encoding !== 'utf-8') {
                            $error['message'] = iconv($phpcsFile->config->encoding, 'utf-8', $error['message']);
                        }

                        $out->startElement('failure');
                        $out->writeAttribute('type', $error['type']);
                        $out->writeAttribute('message', $error['message']." (line $line, column $column)");
                        $out->endElement();

                        $out->endElement();
                    }
                }
            }
        }//end if

        $out->endElement();
        echo $out->flush();
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
        // Figure out the total number of tests.
        $tests   = 0;
        $matches = [];
        preg_match_all('/tests="([0-9]+)"/', $cachedData, $matches);
        if (isset($matches[1]) === true) {
            foreach ($matches[1] as $match) {
                $tests += $match;
            }
        }

        $failures = ($totalErrors + $totalWarnings);

        $dom = new \DOMDocument();
        $dom->formatOutput = True;
        $dom->encoding = "UTF-8";
        $dom->preserveWhiteSpace = False;

        $testsuites = $dom->createElement("testsuites");
        $testsuites->setAttribute("name", 'PHP_CodeSniffer '.Config::VERSION);
        $testsuites->setAttribute("errors", 0);
        $testsuites->setAttribute("tests", $tests);
        $testsuites->setAttribute("failures", $failures);

        $fragment = $dom->createDocumentFragment();
        # using XML that is partially formatted in appendXML() results in
        # dom->formatOutput ignoring the fragment during formatting
        $fragment->appendXML($cachedData);

        $testsuites->appendChild($fragment);
        $dom->appendChild($testsuites);

        # saving and loading the string forces pretty formatting
        $tmp = $dom->saveXML();
        $dom->loadXML($tmp);
        echo $dom->saveXML();
    }//end generate()


}//end class
