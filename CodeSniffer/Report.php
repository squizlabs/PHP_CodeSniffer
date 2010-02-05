<?php
/**
 * Represents a PHP_CodeSniffer report.
 *
 * PHP version 5.
 * 
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Represents a PHP_CodeSniffer report.
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
interface PHP_CodeSniffer_Report
{


    /**
     * Generate the actual report.
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
    );


}//end interface

?>
