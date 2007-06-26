<?php
/**
 * Squiz_Sniffs_ControlStructures_ForLoopDeclarationSniff.
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
 * Squiz_Sniffs_ControlStructures_ForLoopDeclarationSniff.
 *
 * Verifies that there is a spce between each condition of for loops.
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
class Squiz_Sniffs_ControlStructures_ForLoopDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FOR,
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

        if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found after opening bracket of FOR loop';
        }

        if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found before closing bracket of FOR loop';
        }

        $firstSemicolon  = $phpcsFile->findNext(T_SEMICOLON, $openingBracket);
        $secondSemicolon = $phpcsFile->findNext(T_SEMICOLON, ($firstSemicolon + 1));

        // Check whitespace around each of the tokens.
        if ($tokens[($firstSemicolon - 1)]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found before first semicolon of FOR loop';
        }

        if ($tokens[($firstSemicolon + 1)]['code'] !== T_WHITESPACE) {
            $errors[] = 'Expected 1 space after first semicolon of FOR loop; 0 found';
        } else {
            if (strlen($tokens[($firstSemicolon + 1)]['content']) !== 1) {
                $spaces   = strlen($tokens[($firstSemicolon + 1)]['content']);
                $errors[] = "Expected 1 space after first semicolon of FOR loop; $spaces found";
            }
        }

        if ($tokens[($secondSemicolon - 1)]['code'] === T_WHITESPACE) {
            $errors[] = 'Space found before second semicolon of FOR loop';
        }

        if ($tokens[($secondSemicolon + 1)]['code'] !== T_WHITESPACE) {
            $errors[] = 'Expected 1 space after second semicolon of FOR loop; 0 found';
        } else {
            if (strlen($tokens[($secondSemicolon + 1)]['content']) !== 1) {
                $spaces   = strlen($tokens[($firstSemicolon + 1)]['content']);
                $errors[] = "Expected 1 space after second semicolon of FOR loop; $spaces found";
            }
        }

        foreach ($errors as $error) {
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
