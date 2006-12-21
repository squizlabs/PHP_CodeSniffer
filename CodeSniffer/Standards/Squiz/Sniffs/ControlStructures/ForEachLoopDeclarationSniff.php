<?php
/**
 * Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff.
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
 * Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff.
 *
 * Verifies that there is a spce between each condition of foreach loops.
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
class Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FOREACH,
               );

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

        $errors = array();

        $openingBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        if ($tokens[$openingBracket + 1]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found after opening bracket of FOREACH loop';
        }

        if ($tokens[$closingBracket - 1]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found before closing bracket of FOREACH loop';
        }

        $asToken     = $phpcsFile->findNext(T_AS, $openingBracket);
        $doubleArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, $openingBracket, $closingBracket);

        if ($doubleArrow !== false) {
            if ($tokens[$doubleArrow - 1]['code'] !== T_WHITESPACE) {
                $errors[] = 'Expected 1 space before "=>"; 0 found';
            } else {
                if (strlen($tokens[$doubleArrow - 1]['content']) !== 1) {
                    $spaces   = strlen($tokens[$doubleArrow - 1]['content']);
                    $errors[] = "Expected 1 space before \"=>\"; $spaces found";
                }

            }

            if ($tokens[$doubleArrow + 1]['code'] !== T_WHITESPACE) {
                $errors[] = 'Expected 1 space after "=>"; 0 found';
            } else {
                if (strlen($tokens[$doubleArrow + 1]['content']) !== 1) {
                    $spaces   = strlen($tokens[$doubleArrow + 1]['content']);
                    $errors[] = "Expected 1 space after \"=>\"; $spaces found";
                }

            }

        }//end if

        if ($tokens[$asToken + 1]['code'] !== T_WHITESPACE) {
            $errors[] = 'Expected 1 space before "as"; 0 found';
        } else {
            if (strlen($tokens[$asToken - 1]['content']) !== 1) {
                $spaces   = strlen($tokens[$asToken - 1]['content']);
                $errors[] = "Expected 1 space before \"as\"; $spaces found";
            }
        }

        if ($tokens[$asToken + 1]['code'] !== T_WHITESPACE) {
            $errors[] = 'Expected 1 space after "as"; 0 found';
        } else {
            if (strlen($tokens[$asToken + 1]['content']) !== 1) {
                $spaces   = strlen($tokens[$asToken + 1]['content']);
                $errors[] = "Expected 1 space after \"as\"; $spaces found";
            }

        }

        foreach ($errors as $error) {
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
