<?php
/**
 * Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff.
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

require_once 'PHP/CodeSniffer/Sniff.php';

/**
 * Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff.
 *
 * Checks that no whitespace proceeds the first content of the file, exists
 * after the last content of the file, or resides after content on any line.
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
class Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_TAG,
                T_CLOSE_TAG,
                T_WHITESPACE,
                T_COMMENT,
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

        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG) {

            /*
                Check for start of file whitespace.
            */

            // If its the first token, then there is no space.
            if ($stackPtr === 0) {
                return;
            }

            for ($i = ($stackPtr - 1); $i >= 0; $i--) {
                // If we find something that isn't inline html then there is something previous in the file.
                if ($tokens[$i]['type'] !== 'T_INLINE_HTML') {
                    return;
                }

                // If we have ended up with inline html make sure it isn't just whitespace.
                $tokenContent = trim($tokens[$i]['content']);
                if ($tokenContent !== '') {
                    return;
                }
            }

            $phpcsFile->addError('Additional whitespace found at start of file', $stackPtr);

        } else if ($tokens[$stackPtr]['code'] === T_CLOSE_TAG) {

            /*
                Check for end of file whitespace.
            */

            if (isset($tokens[($stackPtr + 1)]) === false) {
                // The close PHP token is the last in the file.
                return;
            }

            $numTokens = count($tokens);
            for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                // If we find something that isn't inline html then there
                // is more to the file.
                if ($tokens[$i]['type'] !== 'T_INLINE_HTML') {
                    return;
                }

                // If we have ended up with inline html make sure it
                // isn't just whitespace.
                $tokenContent = trim($tokens[$i]['content']);
                if (empty($tokenContent) === false) {
                    return;
                }
            }

            $phpcsFile->addError('Additional whitespace found at end of file', $stackPtr);

        } else {

            /*
                Check for end of line whitespace.
            */

            if (strpos($tokens[$stackPtr]['content'], "\n") === false) {
                return;
            }

            $tokenContent = rtrim($tokens[$stackPtr]['content'], "\n");
            if (empty($tokenContent) === true) {
                return;
            }

            if (preg_match('|^.*\s+$|', $tokenContent) === 0) {
                return;
            }

            $phpcsFile->addError('Whitespace found at end of line', $stackPtr);
        }//end else

    }//end process()


}//end class

?>
