<?php
/**
 * Squiz_Sniffs_ControlStructures_SwitchDeclarationSniff.
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
 * Squiz_Sniffs_ControlStructures_SwitchDeclarationSniff.
 *
 * Ensures all the breaks and cases are aligned correctly according to their
 * parent switch's alignment.
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
class Squiz_Sniffs_ControlStructures_SwitchDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_SWITCH);

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

        $content = $tokens[$stackPtr]['content'];
        if ($content !== strtolower($content)) {
            $expected = strtolower($content);
            $error    = "SWITCH keyword must be lowercase; expected \"$expected\" but found \"$content\"";
            $phpcsFile->addError($error, $stackPtr);
        }

        $switch        = $tokens[$stackPtr];
        $nextCase      = $stackPtr;
        $caseAlignment = ($switch['column'] + 4);

        while (($nextCase = $phpcsFile->findNext(array(T_CASE), ($nextCase + 1), $switch['scope_closer'])) !== false) {
            $content = $tokens[$nextCase]['content'];
            if ($content !== strtolower($content)) {
                $expected = strtolower($content);
                $error    = "CASE keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                $phpcsFile->addError($error, $nextCase);
            }

            if ($tokens[$nextCase]['column'] !== $caseAlignment) {
                $error = 'CASE keyword must be indented 4 spaces from SWITCH keyword';
                $phpcsFile->addError($error, $nextCase);
            }

            $nextBreak = $phpcsFile->findNext(array(T_BREAK), ($nextCase + 1), $switch['scope_closer']);
            if ($nextBreak !== false && isset($tokens[$nextBreak]['scope_condition']) === true) {
                // Only check this BREAK statement if it matches the current CASE
                // statement. This stops the same break (used for multiple CASEs) being
                // checked more than once.
                $content = $tokens[$nextBreak]['content'];
                if ($content !== strtolower($content)) {
                    $expected = strtolower($content);
                    $error    = "BREAK keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                    $phpcsFile->addError($error, $nextBreak);
                }

                if ($tokens[$nextBreak]['scope_condition'] === $nextCase) {
                    if ($tokens[$nextBreak]['column'] !== $caseAlignment) {
                        $error = 'BREAK statement must be indented 4 spaces from SWITCH keyword';
                        $phpcsFile->addError($error, $nextBreak);
                    }
                }
            } else {
                $nextBreak = $tokens[$nextCase]['scope_closer'];
            }
        }//end while

        $default = $phpcsFile->findNext(array(T_DEFAULT), $switch['scope_opener'], $switch['scope_closer']);
        if ($default !== false) {
            $content = $tokens[$default]['content'];
            if ($content !== strtolower($content)) {
                $expected = strtolower($content);
                $error    = "DEFAULT keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                $phpcsFile->addError($error, $default);
            }

            if ($tokens[$default]['column'] !== $caseAlignment) {
                $error = 'DEFAULT keyword must be indented 4 spaces from SWITCH keyword';
                $phpcsFile->addError($error, $default);
            }

            $nextBreak = $phpcsFile->findNext(array(T_BREAK), ($default + 1), $switch['scope_closer']);
            if ($nextBreak !== false) {
                $content = $tokens[$nextBreak]['content'];
                if ($content !== strtolower($content)) {
                    $expected = strtolower($content);
                    $error    = "BREAK keyword must be lowercase; expected \"$expected\" but found \"$content\"";
                    $phpcsFile->addError($error, $nextBreak);
                }

                if ($tokens[$nextBreak]['column'] !== $caseAlignment) {
                    $error = 'BREAK statement must be indented 4 spaces from SWITCH keyword';
                    $phpcsFile->addError($error, $nextBreak);
                }
            } else {
                $nextBreak = $tokens[$default]['scope_closer'];
            }
        }//end if

        if ($tokens[$switch['scope_closer']]['column'] !== $switch['column']) {
            $error = 'Closing brace of SWITCH statement must be aligned with SWITCH keyword';
            $phpcsFile->addError($error, $switch['scope_closer']);
        }

    }//end process()


}//end class

?>
