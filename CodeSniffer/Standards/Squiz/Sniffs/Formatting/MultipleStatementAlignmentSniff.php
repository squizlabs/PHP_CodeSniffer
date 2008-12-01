<?php
/**
 * Squiz_Sniffs_Formatting_MultipleStatementAlignmentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_Formatting_MultipleStatementAlignmentSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Formatting_MultipleStatementAlignmentSniff not found');
}

/**
 * Squiz_Sniffs_Formatting_MultipleStatementAlignmentSniff.
 *
 * Checks alignment of assignments. If there are multiple adjacent assignments,
 * it will check that the equals signs of each assignment are aligned. It will
 * display a warning to advise that the signs should be aligned.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Formatting_MultipleStatementAlignmentSniff extends Generic_Sniffs_Formatting_MultipleStatementAlignmentSniff
{

    /**
     * The maximum amount of padding before the alignment is ignored.
     *
     * If the amount of padding required to align this assignment with the
     * surrounding assignments exceeds this number, the assignment will be
     * ignored and no errors or warnings will be thrown.
     *
     * @var int
     */
    protected $maxPadding = 8;

    /**
     * If true, multi-line assignments are not checked.
     *
     * @var int
     */
    protected $ignoreMultiLine = true;

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    protected $error = true;

}//end class

?>
