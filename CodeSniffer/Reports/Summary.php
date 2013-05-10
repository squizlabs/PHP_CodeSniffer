<?php
/**
 * Summary report for PHP_CodeSniffer.
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
 * Summary report for PHP_CodeSniffer.
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
class PHP_CodeSniffer_Reports_Summary implements PHP_CodeSniffer_Report
{


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                $report      Prepared report data.
     * @param boolean              $showSources Show sources?
     * @param int                  $width       Maximum allowed line width.
     * @param PHP_CodeSniffer_File $phpcsFile   The file being reported on.
     *
     * @return boolean
     */
    public function generateFileReport(
        $report,
        $showSources=false,
        $width=80,
        PHP_CodeSniffer_File $phpcsFile
    ) {
        if (PHP_CODESNIFFER_VERBOSITY === 0
            && $report['errors'] === 0
            && $report['warnings'] === 0
        ) {
            // Nothing to print.
            return false;
        }

        $width = max($width, 70);
        $file  = $report['filename'];

        $padding = ($width - 18 - strlen($file));
        if ($padding < 0) {
            $file    = '...'.substr($file, (($padding * -1) + 3));
            $padding = 0;
        }

        echo $file.str_repeat(' ', $padding).'  ';
        echo $report['errors'];
        echo str_repeat(' ', (8 - strlen((string) $report['errors'])));
        echo $report['warnings'];
        echo PHP_EOL;

        return true;

    }//end generateFileReport()


    /**
     * Generates a summary of errors and warnings for each file processed.
     * 
     * @param string  $cachedData    Any partial report data that was returned from
     *                               generateFileReport during the run.
     * @param int     $totalFiles    Total number of files processed during the run.
     * @param int     $totalErrors   Total number of errors found during the run.
     * @param int     $totalWarnings Total number of warnings found during the run.
     * @param int     $totalFixable  Total number of problems that can be fixed.
     * @param boolean $showSources   Show sources?
     * @param int     $width         Maximum allowed line width.
     * @param boolean $toScreen      Is the report being printed to screen?
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
        $toScreen=true
    ) {
        echo PHP_EOL.'PHP CODE SNIFFER REPORT SUMMARY'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;
        echo 'FILE'.str_repeat(' ', ($width - 20)).'ERRORS  WARNINGS'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        echo $cachedData;

        echo str_repeat('-', $width).PHP_EOL;
        echo 'A TOTAL OF '.$totalErrors.' ERROR';
        if ($totalErrors > 1) {
            echo 'S';
        }

        echo ' AND '.$totalWarnings.' WARNING';
        if ($totalWarnings > 1) {
            echo 'S';
        }

        echo ' WERE FOUND IN '.$totalFiles.' FILE';
        if ($totalFiles > 1) {
            echo 'S';
        }

        if ($totalFixable > 0) {
            echo PHP_EOL.str_repeat('-', $width).PHP_EOL;
            echo 'PHPCBF CAN FIX '.$totalFixable.' OF THESE SNIFF VIOLATIONS AUTOMATICALLY';
        }

        echo PHP_EOL.str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if ($toScreen === true
            && PHP_CODESNIFFER_INTERACTIVE === false
            && class_exists('PHP_Timer', false) === true
        ) {
            echo PHP_Timer::resourceUsage().PHP_EOL.PHP_EOL;
        }

    }//end generate()


}//end class

?>
