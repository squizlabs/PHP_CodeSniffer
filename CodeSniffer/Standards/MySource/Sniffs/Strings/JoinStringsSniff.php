<?php
/**
 * Ensures that strings are not joined using the + operator.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_MySource
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Ensures that strings are not joined using the + operator.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_MySource
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class MySource_Sniffs_Strings_JoinStringsSniff implements PHP_CodeSniffer_Sniff
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
        return array(
                T_PLUS,
                T_STRING,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_PLUS) {
            $prev = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($tokens[$prev]['code'] === T_CONSTANT_ENCAPSED_STRING || $tokens[$next]['code'] === T_CONSTANT_ENCAPSED_STRING) {
                $error = 'Strings must not be joined using the + operator; use [\'string\', str].join(\'\') instead';
                $phpcsFile->addError($error, $stackPtr);
            }
        } else if ($tokens[$stackPtr]['content'] === 'join') {
            $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
                return;
            }

            $prev = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prev]['code'] !== T_OBJECT_OPERATOR) {
                return;
            }

            $prev = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($prev - 1), null, true);
            if ($tokens[$prev]['code'] !== T_CLOSE_SQUARE_BRACKET) {
                return;
            }

            // If we get to here, we know we are looking at code like:
            // ...].join(...
            // So make sure a delimiter was passed to the join() function.
            $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next + 1), null, true);
            if ($tokens[$next]['code'] === T_CLOSE_PARENTHESIS) {
                $error = 'A delimiter must be provided when joining arrays';
                $phpcsFile->addError($error, $stackPtr);
            }
        }//end if

    }//end process()


}//end class

?>
