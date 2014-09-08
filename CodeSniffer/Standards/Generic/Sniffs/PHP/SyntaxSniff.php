<?php
/**
 * Generic_Sniffs_PHP_SyntaxSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_PHP_SyntaxSniff.
 *
 * Ensures PHP believes the syntax is clean.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_PHP_SyntaxSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpPath = PHP_CodeSniffer::getConfigData('php_path') ?: 'php';
        $output = shell_exec("{$phpPath} -l {$phpcsFile->getFilename()} 2>/dev/null");
        $matches = array();
        if (preg_match('/on line ([0-9]+)/', $output, $matches)) {
            $error = 'PHP Syntax error found.';
            $tokens = $phpcsFile->getTokens();
            $line = (int) $matches[1];
            $numLines = $tokens[($phpcsFile->numTokens - 1)]['line'];
            if ($line > $numLines) {
                return $phpcsFile->addError($error, $phpcsFile->numTokens - 1, 'PHPSyntax');
            }
            foreach ($phpcsFile->getTokens() as $id => $token) {
                if ($token['line'] === $line) {
                    return $phpcsFile->addError($error, $id, 'PHPSyntax');
                }
            }
        }
    }//end process()


}//end class

?>
