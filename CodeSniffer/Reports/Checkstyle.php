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
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
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
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
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
    public function generate(
        $report,
        $showSources=false,
        $width=80,
        $toScreen=true
    ) {
        echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        echo '<checkstyle version="@package_version@">'.PHP_EOL;

        $errorsShown = 0;
        foreach ($report['files'] as $filename => $file) {
            if (count($file['messages'])) {
                echo ' <file name="'.$filename.'">'.PHP_EOL;
            }

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $error['type']    = strtolower($error['type']);
                        $error['message'] = htmlspecialchars($error['message']);
                        if (PHP_CODESNIFFER_ENCODING !== 'utf-8') {
                            $error['message'] = iconv(PHP_CODESNIFFER_ENCODING, 'utf-8', $error['message']);
                        }

                        echo '  <error line="'.$line.'" column="'.$column.'"';
                        echo ' severity="'.$error['type'].'"';
                        echo ' message="'.$error['message'].'"';
                        echo ' source="'.$error['source'].'"';
                        echo '/>'.PHP_EOL;
                        $errorsShown++;
                    }
                }
            }//end foreach

            if (count($file['messages'])) {
                echo ' </file>'.PHP_EOL;
            }
        }//end foreach

        echo '</checkstyle>'.PHP_EOL;

        return $errorsShown;

    }//end generate()


}//end class

?>
