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
        while (($bufferEnd = $phpcsFile->findNext(array(T_STRING, T_FUNCTION), ($bufferEnd + 1), null, false)) !== false) {
            if ($tokens[$bufferEnd]['code'] === T_FUNCTION) {
                // We should not cross funtions or move into functions.
                $bufferEnd = false;
                break;
            }

            $stringContent = $tokens[$bufferEnd]['content'];
            if (($stringContent === 'ob_end_clean') || ($stringContent === 'ob_end_flush')) {
                break;
            }

            if (($stringContent === 'ob_get_clean') || ($stringContent === 'ob_get_flush')) {
                // Generate the error because the functions are not allowed, but
                // continue to check the indentation.
                $error = 'Output buffering must be closed using ob_end_clean or ob_end_flush';
                $phpcsFile->addError($error, $bufferEnd, 'InvalidClose');
                break;
            }
        }

        if ($bufferEnd === false) {
            $error = 'Output buffering, started here, was never stopped';
            $phpcsFile->addError($error, $stackPtr, 'NotClosed');
            return;
        }

        $requiredIndent = ($tokens[$stackPtr]['column'] + 3);

        for ($stackPtr; $stackPtr < $bufferEnd; $stackPtr++) {
            if (strpos($tokens[$stackPtr]['content'], $phpcsFile->eolChar) === false) {
                continue;
            }

            $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), $bufferEnd, true);
            if ($tokens[$nextContent]['line'] !== ($tokens[$stackPtr]['line'] + 1)) {
                // Empty line.
                continue;
            }

            // The spaces at the start of inline HTML are not considered indent by
            // PHP_CodeSniffer, so we need to ignore them because their indentation
            // is not a coding standard issue, it is a HTML output issue.
            if ($tokens[$nextContent]['code'] === T_INLINE_HTML) {
                continue;
            }

            $foundIndent = ($tokens[$nextContent]['column'] - 1);

            // If this is a comment, the comment may have spaces on the front
            // to indent it, so we need to count them too.
            if ($tokens[$nextContent]['code'] === T_COMMENT) {
                $content      = $tokens[$nextContent]['content'];
                $trimmed      = ltrim($content, ' ');
                $foundIndent += (strlen($content) - strlen($trimmed));
            }

            // The line has content, now if it is less than the required indent, throw error.
            if ($foundIndent < $requiredIndent) {
                $error = 'Buffered line not indented correctly; expected at least %s spaces but found %s';
                $data  = array(
                          $requiredIndent,
                          $foundIndent,
                         );
                $phpcsFile->addError($error, $nextContent, 'Incorrect', $data);
            }
        }//end for

    }//end process()


}//end class

?>
