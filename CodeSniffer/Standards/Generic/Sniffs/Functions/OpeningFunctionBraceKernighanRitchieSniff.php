<?php
/**
 * Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff.
 *
 * Checks that the opening brace of a function is on the same line
 * as the function declaration.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return void
     */
    public function register()
    {
        return array(T_FUNCTION);

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

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];

        // The end of the function occurs at the end of the argument list. Its
        // like this because some people like to break long function declarations
        // over multiple lines.
        $functionLine = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['line'];
        $braceLine    = $tokens[$openingBrace]['line'];

        $lineDifference = ($braceLine - $functionLine);

        if ($lineDifference > 0) {
            $error = 'Opening brace should be on the same line as the declaration';
            $phpcsFile->addError($error, $openingBrace, 'BraceOnNewLine');
            return;
        }

        // Checks that the closing parenthesis and the opening brace are
        // separated by a whitespace character.
        $closerColumn = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['column'];
        $braceColumn  = $tokens[$openingBrace]['column'];

        $columnDifference = ($braceColumn - $closerColumn);

        if ($columnDifference !== 2) {
            $error = 'Expected 1 space between the closing parenthesis and the opening brace; found %s';
            $data  = array(($columnDifference - 1));
            $phpcsFile->addError($error, $openingBrace, 'SpaceBeforeBrace', $data);
            return;
        }

        // Check that a tab was not used instead of a space.
        $spaceTokenPtr = ($tokens[$stackPtr]['parenthesis_closer'] + 1);
        $spaceContent  = $tokens[$spaceTokenPtr]['content'];
        if ($spaceContent !== ' ') {
            $error = 'Expected a single space character between closing parenthesis and opening brace; found %s';
            $data  = array($spaceContent);
            $phpcsFile->addError($error, $openingBrace, 'SpaceBeforeBrace', $data);
            return;
        }

    }//end process()


}//end class

?>