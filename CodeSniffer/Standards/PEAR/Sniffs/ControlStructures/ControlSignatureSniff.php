<?php
/**
 * Verifies that control statements conform to their coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * Verifies that control statements conform to their coding standards.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PEAR_Sniffs_ControlStructures_ControlSignatureSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{
    /**
     * If true, comments will be ignored if they are found in the code.
     *
     * @var boolean
     */
    public $ignoreComments = true;

    /**
     * Patterns that this test wishes to verify
     *
     * @var array 
     */
    public $patternArray = array(
                'do {EOL...} while (...);EOL',
                'while (...) {EOL',
                'for (...) {EOL',
                'if (...) {EOL',
                'foreach (...) {EOL',
                '} else if (...) {EOL',
                '} elseif (...) {EOL',
                '} else {EOL',
                'do {EOL',
               );
    
    /**
     * Returns the patterns that this test wishes to verify.
     *
     * @return string[]
     */
    protected function getPatterns(array $patternArray)
    {
        return $patternArray;
    }//end getPatterns()

}//end class
