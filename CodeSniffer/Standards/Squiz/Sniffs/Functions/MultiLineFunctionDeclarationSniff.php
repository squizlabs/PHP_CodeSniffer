<?php
/**
 * Squiz_Sniffs_Functions_MultiLineFunctionDeclarationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PEAR_Sniffs_Functions_FunctionDeclarationSniff', true) === false) {
    $error = 'Class PEAR_Sniffs_Functions_FunctionDeclarationSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Squiz_Sniffs_Functions_MultiLineFunctionDeclarationSniff.
 *
 * Ensure single and multi-line function declarations are defined correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Functions_MultiLineFunctionDeclarationSniff extends PEAR_Sniffs_Functions_FunctionDeclarationSniff
{


    /**
     * Processes mutli-line declarations.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     * @param array                $tokens    The stack of tokens that make up
     *                                        the file.
     *
     * @return void
     */
    public function processMultiLineDeclaration(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tokens)
    {
        // We do everything the parent sniff does, and a bit more.
        parent::processMultiLineDeclaration($phpcsFile, $stackPtr, $tokens);

        $openBracket  = $tokens[$stackPtr]['parenthesis_opener'];
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];

        // The open bracket should be the last thing on the line.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), null, true);
        if ($tokens[$next]['line'] !== ($tokens[$openBracket]['line'] + 1)) {
            $error = 'The first parameter of a multi-line function declaration must be on the line after the opening bracket';
            $phpcsFile->addError($error, $next, 'FirstParamSpacing');
        }

        // Each line between the brackets should contain a single parameter.
        $lastCommaLine = null;
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            // Skip brackets, like arrays, as they can contain commas.
            if (isset($tokens[$i]['parenthesis_opener']) === true) {
                $i = $tokens[$i]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$i]['code'] === T_COMMA) {
                if ($lastCommaLine !== null && $lastCommaLine === $tokens[$i]['line']) {
                    $error = 'Multi-line function declarations must define one parameter per line';
                    $phpcsFile->addError($error, $i, 'OneParamPerLine');
                } else {
                    // Comma must be the last thing on the line.
                    $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), null, true);
                    if ($tokens[$next]['line'] !== ($tokens[$i]['line'] + 1)) {
                        $error = 'Commas in multi-line function declarations must be the last content on a line';
                        $phpcsFile->addError($error, $next, 'ContentAfterComma');
                    }
                }

                $lastCommaLine = $tokens[$i]['line'];
            }
        }

    }//end processMultiLineDeclaration()


}//end class

?>
