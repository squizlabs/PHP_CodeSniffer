<?php
/**
 * Json report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Jeffrey Fisher <jeffslofish@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Json report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Jeffrey Fisher <jeffslofish@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Json implements PHP_CodeSniffer_Report
{


    /**
     * Generates a json report.
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
        $reportArr = array();
        $errorsShown = 0;
        foreach ($report['files'] as $filename => $file) {
            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $filename = str_replace('"', '\"', $filename);
                        $message  = str_replace('"', '\"', $error['message']);
                        $type     = strtolower($error['type']);
                        $source   = $error['source'];
                        $severity = $error['severity'];
                        
                        $reportItem = array("line" => $line,
                                            "column" => $column,
                                            "filename" => $filename, 
                                            "message" => $message,
                                            "type" => $type,
                                            "source" => $source,
                                            "severity" => $severity);
                        $reportArr[] = $reportItem;
                        $errorsShown++;
                    }
                }
            }//end foreach
        }//end foreach

        
        echo json_encode(array("reportList" => $reportArr));
        return $errorsShown;

    }//end generate()


}//end class

?>
