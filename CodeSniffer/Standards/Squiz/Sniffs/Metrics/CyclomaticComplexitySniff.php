<?php
/**
 * Squiz_Sniffs_Metrics_CyclomaticComplexitySniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: LineLengthSniff.php 261688 2008-06-27 01:58:38Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_Metrics_CyclomaticComplexitySniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Metrics_CyclomaticComplexitySniff not found');
}

/**
 * Squiz_Sniffs_Metrics_CyclomaticComplexitySniff.
 *
 * Checks the cyclomatic complexity of functions, but should not throw errors.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Metrics_CyclomaticComplexitySniff extends Generic_Sniffs_Metrics_CyclomaticComplexitySniff
{

    /**
     * A complexity higher than this value will throw a warning.
     *
     * @var int
     */
    protected $complexity = 10;

    /**
     * A complexity higer than this value will throw an error.
     *
     * @var int
     */
    protected $absoluteComplexity = 100;

}//end class

?>
