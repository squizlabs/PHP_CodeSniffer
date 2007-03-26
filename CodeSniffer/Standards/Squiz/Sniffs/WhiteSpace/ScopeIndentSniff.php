<?php
/**
 * Squiz_Sniffs_Whitespace_ScopeIndentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/Standards/Generic/Sniffs/WhiteSpace/ScopeIndentSniff.php';

/**
 * Squiz_Sniffs_Whitespace_ScopeIndentSniff.
 *
 * Checks that control structures are structured correctly, and their content
 * is indented correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_WhiteSpace_ScopeIndentSniff extends Generic_Sniffs_WhiteSpace_ScopeIndentSniff
{


    /**
     * Calculates the expected indent of a token.
     *
     * Looks for ob_start() calls because those act as scope openers in the
     * Squiz coding standard, and so require additional indentation.
     *
     * @param array $tokens   The stack of tokens for this file.
     * @param int   $stackPtr The position of the token to get indent for.
     *
     * @return int
     */
    protected function calculateExpectedIndent(array $tokens, $stackPtr)
    {
        $expectedIndent = parent::calculateExpectedIndent($tokens, $stackPtr);

        // If we are in a function, check all tokens to the start of the
        // function. If we are not in a function, check all tokens to the
        // start of the file.
        $checkTo = 0;

        $tokenConditions = $tokens[$stackPtr]['conditions'];
        foreach ($tokenConditions as $id => $condition) {
            if ($condition === T_FUNCTION) {
                $checkTo = ($tokens[$id]['scope_opener'] + 1);
            }
        }

        for ($i = ($stackPtr - 1); $i >= $checkTo; $i--) {
            if ($tokens[$i]['code'] !== T_STRING) {
                continue;
            }

            if ($tokens[$i]['content'] === 'ob_start') {
                $expectedIndent += $this->indent;
            }

            $bufferClosers = array(
                              'ob_end_clean',
                              'ob_end_flush',
                              'ob_get_clean',
                              'ob_get_flush',
                             );

            if (in_array($tokens[$i]['content'], $bufferClosers) === true) {
                $expectedIndent -= $this->indent;
            }
        }//end for

        return $expectedIndent;

    }//end calculateExpectedIndent()


}//end class

?>
