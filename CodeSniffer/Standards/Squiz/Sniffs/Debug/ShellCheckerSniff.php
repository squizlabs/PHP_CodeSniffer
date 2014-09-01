<?php
/**
 * Squiz_Sniffs_Debug_ShellCheckerSniff.
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
 * Squiz_Sniffs_Debug_ShellCheckerSniff.
 *
 * Runs shellchecker on the file.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Debug_ShellCheckerSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('SH');

    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()

    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If shellchecker could not be run
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $fileName = $phpcsFile->getFilename();
        
        $shellCheckerPath = PHP_CodeSniffer::getConfigData('shellcheck_path');
        if ($shellCheckerPath === null) {
            return;
        }

        $cmd = "$shellCheckerPath --format=checkstyle $fileName";
        $output = array();
        exec($cmd, $output);

        if (empty($output) === true) {
            return;
        }
        
        $checkstyle = new SimpleXMLElement(implode(PHP_EOL, $output));

        $tokens = $phpcsFile->getTokens();

        foreach ($checkstyle->file as $file) {
            foreach ($file->error as $error) {
                $line = $error['line'];
                $msg = $error['message'];
                $severity = $error['severity'];

                $lineToken = null;
                foreach ($tokens as $ptr => $info) {
                    if ($info['line'] == $line) {
                        $lineToken = $ptr;
                        break;
                    }
                }
                if ($severity === 'error') {
                    $phpcsFile->addError($msg, $lineToken, 'ExternalTool');
                } else {
                    $phpcsFile->addWarning($msg, $lineToken, 'ExternalTool');
                }
            }
        }
    }//end process()
}//end class

?>
