<?php
/**
 * Generic_Sniffs_PHP_SyntaxSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Blaine Schmeisser <blainesch@gmail.com>
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
 * @author    Blaine Schmeisser <blainesch@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_PHP_SyntaxSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The path to the PHP version we are checking with.
     *
     * @var string
     */
    private $_phpPath = null;


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
        if ($this->_phpPath === null) {
            $this->_phpPath = PHP_CodeSniffer::getConfigData('php_path');
            if ($this->_phpPath === null) {
                // PHP_BINARY is available in PHP 5.4+.
                if (defined('PHP_BINARY') === true) {
                    $this->_phpPath = PHP_BINARY;
                } else {
                    return;
                }
            }
        }

        $fileName = $phpcsFile->getFilename();
        if (defined('HHVM_VERSION') === false) {
            $cmd = $this->_phpPath." -l -d error_prepend_string='' \"$fileName\" 2>&1";
        } else {
            $cmd = $this->_phpPath." -l \"$fileName\" 2>&1";
        }

        $output  = shell_exec($cmd);
        $matches = array();
        if (preg_match('/^.*error:(.*) in .* on line ([0-9]+)/m', trim($output), $matches) === 1) {
            $error = trim($matches[1]);
            $line  = (int) $matches[2];
            $phpcsFile->addErrorOnLine("PHP syntax error: $error", $line, 'PHPSyntax');
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
