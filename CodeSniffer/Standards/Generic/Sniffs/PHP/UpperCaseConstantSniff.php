<?php
/**
 * Generic_Sniffs_PHP_UpperCaseConstantSniff.
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

/**
 * Generic_Sniffs_PHP_UpperCaseConstantSniff.
 *
 * Checks that all uses of TRUE, FALSE and NULL are uppercase.
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
class Generic_Sniffs_PHP_UpperCaseConstantSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_TRUE,
                T_FALSE,
                T_NULL,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this a member var name?
        $prevPtr = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($tokens[$prevPtr]['code'] === T_OBJECT_OPERATOR) {
            return;
        }

        // Is this a class name?
        if ($tokens[$prevPtr]['code'] === T_CLASS
            || $tokens[$prevPtr]['code'] === T_EXTENDS
            || $tokens[$prevPtr]['code'] === T_IMPLEMENTS
            || $tokens[$prevPtr]['code'] === T_NEW
            || $tokens[$prevPtr]['code'] === T_CONST
        ) {
            return;
        }

        // Class or namespace?
        if ($tokens[($stackPtr - 1)]['code'] === T_NS_SEPARATOR) {
            return;
        }

        if ($tokens[($stackPtr - 1)]['code'] === T_PAAMAYIM_NEKUDOTAYIM) {
            return;
        }

        $keyword = $tokens[$stackPtr]['content'];
        if (strtoupper($keyword) !== $keyword) {
            $error = 'TRUE, FALSE and NULL must be uppercase; expected "%s" but found "%s"';
            $data  = array(
                      strtoupper($keyword),
                      $keyword,
                     );
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }

    }//end process()


}//end class

?>
