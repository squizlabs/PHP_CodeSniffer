<?php
/**
 * Checkstyle report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checkstyle report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Checkstyle implements PHP_CodeSniffer_Report
{


    /**
     * Prints all violations for processed files, in a Checkstyle format.
     *
     * Violations are grouped by file.
     *
     * @param array   $report      Prepared report.
     * @param boolean $showSources Show sources?
     * @param int     $width       Maximum allowed lne width.
     * @param boolean $toScreen    Is the report being printed to screen?
     *
     * @return string
     */
    public function generateFileReport(
        $report,
        $showSources=false,
        $width=80
    ) {
        $out = new XMLWriter;
        $out->openMemory();
        $out->setIndent(true);

        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return false;
        }

        $out->startElement('file');
        $out->writeAttribute('name', $report['filename']);

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $error['type'] = strtolower($error['type']);
                    if (PHP_CODESNIFFER_ENCODING !== 'utf-8') {
                        $error['message'] = iconv(PHP_CODESNIFFER_ENCODING, 'utf-8', $error['message']);
                    }

                    $out->startElement('error');
                    $out->writeAttribute('line', $line);
                    $out->writeAttribute('column', $column);
                    $out->writeAttribute('severity', $error['type']);
                    $out->writeAttribute('message', $error['message']);
                    $out->writeAttribute('source', $error['source']);
                    $out->endElement();
                }
            }
        }//end foreach

        $out->endElement();
        echo $out->flush();

        return true;

    }//end generate()

    /**
     * Prints all violations for processed files, in a Checkstyle format.
     *
     * Violations are grouped by file.
     *
     * @param array   $report      Prepared report.
     * @param boolean $showSources Show sources?
     * @param int     $width       Maximum allowed lne width.
     * @param boolean $toScreen    Is the report being printed to screen?
     *
     * @return string
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $showSources=false,
        $width=80,
        $toScreen=true
    ) {
        echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        echo '<checkstyle version="@package_version@">'.PHP_EOL;
        echo $cachedData;
        echo '</checkstyle>'.PHP_EOL;

    }//end generate()


}//end class

?>
