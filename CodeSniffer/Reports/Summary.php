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
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: IsCamelCapsTest.php 240585 2007-08-02 00:05:40Z squiz $
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
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Summary implements PHP_CodeSniffer_Report
{


    /**
     * Generates a summary of errors and warnings for each file processed.
     * 
     * If verbose output is enabled, results are shown for all files, even if
     * they have no errors or warnings. If verbose output is disabled, we only
     * show files that have at least one warning or error.
     * 
     * @param array   $report       Prepared report.
     * @param boolean $showWarnings Show warnings?
     * @param boolean $showSources  Show sources?
     * @param int     $width        Maximum allowed lne width.
     * 
     * @return string 
     */
    public function generate(
        $report,
        $showWarnings=true,
        $showSources=false,
        $width=80
    ) {
        $errorFiles = array();
        $width      = max($width, 70);

        foreach ($report['files'] as $filename => $file) {
            $numWarnings = $file['warnings'];
            $numErrors   = $file['errors'];

            // If verbose output is enabled, we show the results for all files,
            // but if not, we only show files that had errors or warnings.
            if (PHP_CODESNIFFER_VERBOSITY > 0
                || $numErrors > 0
                || ($numWarnings > 0
                && $showWarnings === true)
            ) {
                $errorFiles[$filename] = array(
                                          'warnings' => $numWarnings,
                                          'errors'   => $numErrors,
                                         );
            }//end if
        }//end foreach

        if (empty($errorFiles) === true) {
            // Nothing to print.
            return 0;
        }

        echo PHP_EOL.'PHP CODE SNIFFER REPORT SUMMARY'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;
        if ($showWarnings === true) {
            echo 'FILE'.str_repeat(' ', ($width - 20)).'ERRORS  WARNINGS'.PHP_EOL;
        } else {
            echo 'FILE'.str_repeat(' ', ($width - 10)).'ERRORS'.PHP_EOL;
        }

        echo str_repeat('-', $width).PHP_EOL;

        $totalErrors   = 0;
        $totalWarnings = 0;
        $totalFiles    = 0;

        foreach ($errorFiles as $file => $errors) {
            if ($showWarnings === true) {
                $padding = ($width - 18 - strlen($file));
            } else {
                $padding = ($width - 8 - strlen($file));
            }

            if ($padding < 0) {
                $file    = '...'.substr($file, (($padding * -1) + 3));
                $padding = 0;
            }

            echo $file.str_repeat(' ', $padding).'  ';
            echo $errors['errors'];
            if ($showWarnings === true) {
                echo str_repeat(' ', (8 - strlen((string) $errors['errors'])));
                echo $errors['warnings'];
            }

            echo PHP_EOL;

            $totalErrors   += $errors['errors'];
            $totalWarnings += $errors['warnings'];
            $totalFiles++;
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        echo 'A TOTAL OF '.$totalErrors.' ERROR(S) ';
        if ($showWarnings === true) {
            echo 'AND '.$totalWarnings.' WARNING(S) ';
        }

        echo 'WERE FOUND IN '.$totalFiles.' FILE(S)'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if ($showSources === true) {
            $source = new PHP_CodeSniffer_Reports_Source();
            $source->generate($report, $showWarnings, $showSources, $width);
        }

        return ($totalErrors + $totalWarnings);

    }//end generate()


}//end class

?>
