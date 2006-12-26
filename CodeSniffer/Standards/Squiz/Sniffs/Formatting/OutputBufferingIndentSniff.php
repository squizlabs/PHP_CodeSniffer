<?php
/**
 * Squiz_Sniffs_Formatting_OutputBufferingIndentSniff.
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
 * Squiz_Sniffs_Formatting_OutputBufferingIndentSniff.
 *
 * Checks the indenting used when an ob_start() call occurs.
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
class Squiz_Sniffs_Formatting_OutputBufferingIndentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
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

        if ($tokens[$stackPtr]['content'] !== 'ob_start') {
            return;
        }

        $bufferEnd = $stackPtr;
        while (($bufferEnd = $phpcsFile->findNext(array(T_STRING), $bufferEnd + 1, null, false)) !== false) {
            $stringContent = $tokens[$bufferEnd]['content'];
            if (($stringContent === 'ob_end_clean') || ($stringContent === 'ob_end_flush')) {
                break;
            }
        }

        if ($bufferEnd === false) {
            $phpcsFile->addError('Output buffering, started here, was never stopped', $stackPtr);
            return;
        }

        $requiredIndent = $tokens[$stackPtr]['column'] + 3;

        for ($stackPtr; $stackPtr < $bufferEnd; $stackPtr++) {
            if (strpos($tokens[$stackPtr]['content'], "\n") === false) {
                continue;
            }

            $nextContent = $phpcsFile->findNext(array(T_WHITESPACE), $stackPtr + 1, $bufferEnd, true);
            if ($tokens[$nextContent]['line'] !== $tokens[$stackPtr]['line'] + 1) {
                // Empty line.
                continue;
            }

            // The line has content, now if it is less than the required indent, throw error.
            $foundIndent = ($tokens[$nextContent]['column'] - 1);
            if ($foundIndent < $requiredIndent) {
                $error = "Buffered line not indented correctly. Expected at least $requiredIndent spaces; found $foundIndent.";
                $phpcsFile->addError($error, $nextContent);
            }
        }

    }//end process()


}//end class

?>
