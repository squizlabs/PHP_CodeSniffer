<?php
/**
 * Squiz_Sniffs_ControlStructures_InlineControlStructureSniff.
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
 * Squiz_Sniffs_ControlStructures_InlineIfDeclarationSniff.
 *
 * Tests the spacing of shorthand IF statements.
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
class Squiz_Sniffs_ControlStructures_InlineIfDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_INLINE_THEN);

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

        $statementEnd = $phpcsFile->findNext(array(T_SEMICOLON), $stackPtr + 1, null, false);

        // Make sure it's all on the same line.
        if ($tokens[$statementEnd]['line'] !== $tokens[$stackPtr]['line']) {
            $error = 'Inline shorthand IF statement must be declared on a single line';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        // Make sure there are spaces around the question mark.
        $contentBefore = $phpcsFile->findPrevious(array(T_WHITESPACE), $stackPtr - 1, null, true);
        $contentAfter  = $phpcsFile->findNext(array(T_WHITESPACE), $stackPtr + 1, null, true);
        if ($tokens[$contentBefore]['code'] !== T_CLOSE_PARENTHESIS) {
            $error = 'Inline shorthand IF statement requires brackets around comparison';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        $spaceBefore = ($tokens[$stackPtr]['column'] - ($tokens[$contentBefore]['column'] + strlen($tokens[$contentBefore]['content'])));
        if ($spaceBefore !== 1) {
            $error = "Inline shorthand IF statement requires 1 space before THEN; $spaceBefore found.";
            $phpcsFile->addError($error, $stackPtr);
        }

        $spaceAfter = (($tokens[$contentAfter]['column']) - ($tokens[$stackPtr]['column'] + 1));
        if ($spaceAfter !== 1) {
            $error = "Inline shorthand IF statement requires 1 space after THEN; $spaceAfter found.";
            $phpcsFile->addError($error, $stackPtr);
        }

        // If there is an else in this condition, make sure it has correct spacing.
        $inlineElse = $phpcsFile->findNext(array(T_COLON), $stackPtr + 1, $statementEnd, false);
        if ($inlineElse === false) {
            // No else condition.
            return;
        }

        $contentBefore = $phpcsFile->findPrevious(array(T_WHITESPACE), $inlineElse - 1, null, true);
        $contentAfter  = $phpcsFile->findNext(array(T_WHITESPACE), $inlineElse + 1, null, true);

        $spaceBefore = ($tokens[$inlineElse]['column'] - ($tokens[$contentBefore]['column'] + strlen($tokens[$contentBefore]['content'])));
        if ($spaceBefore !== 1) {
            $error = "Inline shorthand IF statement requires 1 space before ELSE; $spaceBefore found.";
            $phpcsFile->addError($error, $inlineElse);
        }

        $spaceAfter = (($tokens[$contentAfter]['column']) - ($tokens[$inlineElse]['column'] + 1));
        if ($spaceAfter !== 1) {
            $error = "Inline shorthand IF statement requires 1 space after ELSE; $spaceAfter found.";
            $phpcsFile->addError($error, $inlineElse);
        }

    }//end process()


}//end class


?>
