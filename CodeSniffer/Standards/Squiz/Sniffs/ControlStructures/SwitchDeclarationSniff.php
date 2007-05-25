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

        $switch        = $tokens[$stackPtr];
        $nextCase      = $stackPtr;
        $caseAlignment = ($switch['column'] + 4);

        while (($nextCase = $phpcsFile->findNext(array(T_CASE), ($nextCase + 1), $switch['scope_closer'])) !== false) {
            if ($tokens[$nextCase]['column'] !== $caseAlignment) {
                $error = 'CASE keyword must be indented 4 spaces from SWITCH keyword';
                $phpcsFile->addError($error, $nextCase);
            }

            $nextBreak = $phpcsFile->findNext(array(T_BREAK), ($nextCase + 1), $switch['scope_closer']);
            if ($nextBreak !== false) {
                // Only check this BREAK statement if it matches the current CASE
                // statement. This stops the same break (used for multiple CASEs) being
                // checked more than once.
                if ($tokens[$nextBreak]['scope_condition'] === $nextCase) {
                    if ($tokens[$nextBreak]['column'] !== $caseAlignment) {
                        $error = 'BREAK statement must be indented 4 spaces from SWITCH keyword';
                        $phpcsFile->addError($error, $nextBreak);
                    }
                }
            } else {
                $nextBreak = $tokens[$nextCase]['scope_closer'];
            }

            // Check that all content within each CASE is indented correctly.
            $nextSpace = $nextCase;
            while (($nextSpace = $phpcsFile->findNext(T_WHITESPACE, ($nextSpace + 1), $nextBreak)) !== false) {
                if (strpos($tokens[$nextSpace]['content'], $phpcsFile->eolChar) === false) {
                    continue;
                }

                // Whitespace has a new line. We need to check that it does not
                // precede a CASE or a BREAK, and then we can check indentation.
                $nextContent = $phpcsFile->findNext(array(T_WHITESPACE), ($nextSpace + 1), null, true);
                if ($tokens[$nextContent]['code'] === T_BREAK) {
                    continue;
                }

                if ($tokens[$nextContent]['code'] === T_CASE) {
                    // This will be handled by the next CASE statement.
                    break;
                }

                if ($tokens[$nextContent]['code'] === T_DEFAULT) {
                    // This will be handled by the next CASE statement.
                    break;
                }

                if ($tokens[$nextContent]['code'] === T_CLOSE_CURLY_BRACKET) {
                    // This will be handled by the closing brace check.
                    break;
                }

                // If the space is an empty line, we don't need to check it.
                if ($tokens[$nextContent]['line'] !== ($tokens[$nextSpace]['line'] + 1)) {
                    continue;
                }

                // This is on the same line, and not a CASE or a BREAK, so
                // it needs to be indented at least 4 spaces after the CASE.
                $requiredIndent = ($tokens[$nextCase]['column'] + 4);
                if ($tokens[$nextContent]['column'] < $requiredIndent) {
                    $error = 'Line not indented correctly; expected at least '.($requiredIndent - 1).' spaces, found '.($tokens[$nextContent]['column'] - 1);
                    $phpcsFile->addError($error, $nextContent);
                }
            }//end while
        }//end while

        $default = $phpcsFile->findNext(array(T_DEFAULT), $switch['scope_opener'], $switch['scope_closer']);
        if ($default !== false) {
            if ($tokens[$default]['column'] !== $caseAlignment) {
                $error = 'DEFAULT keyword must be indented 4 spaces from SWITCH keyword';
                $phpcsFile->addError($error, $default);
            }

            $nextBreak = $phpcsFile->findNext(array(T_BREAK), ($default + 1), $switch['scope_closer']);
            if ($nextBreak !== false) {
                if ($tokens[$nextBreak]['column'] !== $caseAlignment) {
                    $error = 'BREAK statement must be indented 4 spaces from SWITCH keyword';
                    $phpcsFile->addError($error, $nextBreak);
                }
            } else {
                $nextBreak = $tokens[$default]['scope_closer'];
            }

            // Check that all content within the DEFAULT case is indented correctly.
            $nextSpace = $default;
            while (($nextSpace = $phpcsFile->findNext(T_WHITESPACE, ($nextSpace + 1), $nextBreak)) !== false) {
                if (strpos($tokens[$nextSpace]['content'], $phpcsFile->eolChar) === false) {
                    continue;
                }

                // Whitespace has a new line. We need to check that it does not
                // precede a BREAK, and then we can check indentation.
                $nextContent = $phpcsFile->findNext(array(T_WHITESPACE), ($nextSpace + 1), null, true);
                if ($tokens[$nextContent]['code'] === T_BREAK) {
                    continue;
                }

                // If we have reached the closer and not found a BREAK
                // we are finished.
                if ($nextContent === $nextBreak) {
                    break;
                }

                // If the space is an empty line, we don't need to check it.
                if ($tokens[$nextContent]['line'] !== ($tokens[$nextSpace]['line'] + 1)) {
                    continue;
                }

                // This is on the same line, and not a BREAK, so
                // it needs to be indented at least 4 spaces after the DEFAULT.
                $requiredIndent = ($tokens[$default]['column'] + 4);
                if ($tokens[$nextContent]['column'] < $requiredIndent) {
                    $error = 'Line not indented correctly; expected at least '.$requiredIndent.' spaces, found '.$tokens[$nextContent]['column'];
                    $phpcsFile->addError($error, $nextContent);
                }
            }//end while
        }//end if

        if ($tokens[$switch['scope_closer']]['column'] !== $switch['column']) {
            $error = 'Closing brace of SWITCH statement must be aligned with SWITCH keyword';
            $phpcsFile->addError($error, $switch['scope_closer']);
        }

    }//end process()


}//end class

?>
