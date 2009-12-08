<?php
/**
 * Squiz_Sniffs_Metrics_NestingLevelSniff.
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

if (class_exists('Generic_Sniffs_Metrics_NestingLevelSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Metrics_NestingLevelSniff not found');
}

/**
 * Squiz_Sniffs_Metrics_NestingLevelSniff.
 *
 * Checks the nesting level for functions, but should not throw errors.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Metrics_NestingLevelSniff extends Generic_Sniffs_Metrics_NestingLevelSniff
{

    /**
     * A nesting level than this value will throw a warning.
     *
     * @var int
     */
    protected $nestingLevel = 5;

    /**
     * A nesting level than this value will throw an error.
     *
     * @var int
     */
    protected $absoluteNestingLevel = 50;

}//end class

?>
