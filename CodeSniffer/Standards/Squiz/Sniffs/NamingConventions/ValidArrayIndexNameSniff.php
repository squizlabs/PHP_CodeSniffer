<?php
/**
 * Squiz_Sniffs_NamingConventions_ValidArrayIndexNameSniff.
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
 * Squiz_Sniffs_NamingConventions_ValidArrayIndexNameSniff.
 *
 * Ensures that array indexes are named correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_NamingConventions_ValidArrayIndexNameSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Registers the token types that this sniff wishes to listen to.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_SQUARE_BRACKET);

    }//end register()


    /**
     * Process the tokens that this sniff is listening for.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure there is a variable before it.
        $variable = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($variable === false || $tokens[$variable]['code'] !== T_VARIABLE) {
            return;
        }

        $ignore = array(
                   '$_SERVER',
                   '$_GET',
                   '$_POST',
                   '$_REQUEST',
                  );

        $variableName = $tokens[$variable]['content'];
        if (in_array($variableName, $ignore) === true) {
            return;
        }

        // We are only interested in indexes that are single strings.
        $index = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        $next  = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($index + 1), null, true);
        if ($next !== $tokens[$stackPtr]['bracket_closer']) {
            return;
        }

        $indexContent = trim($tokens[$index]['content'], "'");
        if (preg_match('|^[a-zA-Z0-9_]+$|', $indexContent) === 1) {
            if (strtolower($indexContent) !== $indexContent) {
                $error = 'Array index "'.$indexContent.'" should not contain uppercase characters';
                $phpcsFile->addError($error, $index);
            }
        }

    }//end process()


}//end class

?>
