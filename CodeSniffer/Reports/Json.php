<?php
/**
 * Json report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Jeffrey Fisher <jeffslofish@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
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
 * @author    Jeffrey Fisher <jeffslofish@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
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
        $errorsShown = 0;
        $newReport   = array(
                        'totals' => $report['totals'],
                        'files'  => array(),
                       );

        foreach ($report['files'] as $filename => $file) {
            $newReport['files'][$filename] = array(
                                              'errors'   => $file['errors'],
                                              'warnings' => $file['warnings'],
                                              'messages' => array(),
                                             );

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $error['line']   = $line;
                        $error['column'] = $column;
                        $newReport['files'][$filename]['messages'][] = $error;
                        $errorsShown++;
                    }
                }
            }
        }//end foreach

        echo json_encode($newReport);
        return $errorsShown;

    }//end generate()


}//end class

?>
