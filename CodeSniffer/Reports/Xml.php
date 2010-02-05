<?php
/**
 * Xml report for PHP_CodeSniffer.
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
 * Xml report for PHP_CodeSniffer.
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
class PHP_CodeSniffer_Reports_Xml implements PHP_CodeSniffer_Report
{


    /**
     * Prints all violations for processed files, in a proprietary XML format.
     *
     * Errors and warnings are displayed together, grouped by file.
     *
     * @param array   $report       Prepared report.
     * @param boolean $showSources  Show sources?
     * @param int     $width        Maximum allowed lne width.
     * 
     * @return string 
     */
    public function generate(
        $report,
        $showSources=false,
        $width=80
    ) {
        echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        echo '<phpcs version="@package_version@">'.PHP_EOL;

        $errorsShown = 0;

        foreach ($report['files'] as $filename => $file) {
            if (empty($file['messages']) === true) {
                continue;
            }

            echo ' <file name="'.$filename.'" errors="'.$file['errors'].'" warnings="'.$file['warnings'].'">'.PHP_EOL;

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $error['type'] = strtolower($error['type']);
                        echo '  <'.$error['type'].' line="'.$line.'" column="'.$column.'" source="'.$error['source'].'">';
                        echo htmlspecialchars($error['message']).'</'.$error['type'].'>'.PHP_EOL;
                        $errorsShown++;
                    }
                }
            }//end foreach

            echo ' </file>'.PHP_EOL;
        }//end foreach

        echo '</phpcs>'.PHP_EOL;

        return $errorsShown;

    }//end generate()


}//end class

?>
