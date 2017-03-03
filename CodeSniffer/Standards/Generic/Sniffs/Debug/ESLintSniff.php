<?php
/**
 * Generic_Sniffs_Debug_ESLintSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Debug_ESLintSniff.
 *
 * Runs eslint on the file.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Debug_ESLintSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('JS');


    /**
     * ESLint configuration file path.
     *
     * @var string|null Path to eslintrc. Null to autodetect.
     */
    public $configFile = null;


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
     * @throws PHP_CodeSniffer_Exception If jslint.js could not be run
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $filename   = $phpcsFile->getFilename();
        $eslintPath = PHP_CodeSniffer::getConfigData('eslint_path');
        if ($eslintPath === null) {
            return;
        }

        $configFile = $this->configFile;
        if (empty($configFile) === true) {
            // Attempt to autodetect.
            $candidates = glob('.eslintrc{.js,.yaml,.yml,.json}', GLOB_BRACE);
            if (empty($candidates) === false) {
                $configFile = $candidates[0];
            }
        }

        $eslintOptions = array('--format json');
        if (empty($configFile) === false) {
            $eslintOptions[] = '--config '.escapeshellarg($configFile);
        }

        $cmd  = escapeshellcmd(escapeshellarg($eslintPath).' '.implode(' ', $eslintOptions).' '.escapeshellarg($filename));
        $desc = array(
                 0 => array(
                       'pipe',
                       'r',
                      ),
                 1 => array(
                       'pipe',
                       'w',
                      ),
                 2 => array(
                       'pipe',
                       'w',
                      ),
                );
        $proc = proc_open($cmd, $desc, $pipes);

        // Ignore stdin.
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        // Close, and start working!
        $code = proc_close($proc);

        if ($code <= 0) {
            // No errors, continue.
            return ($phpcsFile->numTokens + 1);
        }

        $data = json_decode($stdout);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Ignore any errors.
            return ($phpcsFile->numTokens + 1);
        }

        // Data is a list of files, but we only pass a single one.
        $messages = $data[0]->messages;
        foreach ($messages as $error) {
            if (empty($error->fatal) === false || $error->severity === 2) {
                $phpcsFile->addErrorOnLine($error->message, $error->line, $error->ruleId);
            } else {
                $phpcsFile->addWarningOnLine($error->message, $error->line, $error->ruleId);
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
