<?php
/**
 * Squiz_Sniffs_Classes_DuplicatePropertySniff.
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

/**
 * Squiz_Sniffs_Classes_DuplicatePropertySniff.
 *
 * Ensures JS classes dont contain duplicate property names.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Classes_DuplicatePropertySniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('JS');


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OBJECT);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being processed.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $end    = $tokens[$stackPtr]['scope_closer'];

        $properties = array();

        $next = $phpcsFile->findNext(T_PROPERTY, ($stackPtr + 1), $end);
        while ($next !== false && $next < $end) {
            $propName = $tokens[$next]['content'];
            if (isset($properties[$propName]) === true) {
                $line  = $tokens[$properties[$propName]]['line'];
                $error = "Duplicate property definition found for \"$propName\"; previously defined on line $line";
                $phpcsFile->addError($error, $next);
            }

            // Store the property info for later comparisons.
            $properties[$propName] = $next;

            // Skeip nested objects.
            if (isset($tokens[$next]['scope_opener']) === true) {
                $next = $tokens[$next]['scope_closer'];
            }

            $next = $phpcsFile->findNext(T_PROPERTY, ($next + 1), $end);
        }//end while

    }//end process()


}//end class


?>
