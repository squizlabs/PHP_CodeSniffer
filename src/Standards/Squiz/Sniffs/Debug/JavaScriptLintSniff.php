<?php
/**
 * Runs JavaScript Lint on the file.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Debug;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\RuntimeException;

class JavaScriptLintSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('JS');


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
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $jslPath = Config::getExecutablePath('jsl');
        if (is_null($jslPath) === true) {
            return;
        }

        $fileName = $phpcsFile->getFilename();

        $cmd = '"'.escapeshellcmd($jslPath).'" -nologo -nofilelisting -nocontext -nosummary -output-format __LINE__:__ERROR__ -process '.escapeshellarg($fileName);
        $msg = exec($cmd, $output, $retval);

        // Variable $exitCode is the last line of $output if no error occurs, on
        // error it is numeric. Try to handle various error conditions and
        // provide useful error reporting.
        if ($retval === 2 || $retval === 4) {
            if (is_array($output) === true) {
                $msg = join('\n', $output);
            }

            throw new RuntimeException("Failed invoking JavaScript Lint, retval was [$retval], output was [$msg]");
        }

        if (is_array($output) === true) {
            foreach ($output as $finding) {
                $split   = strpos($finding, ':');
                $line    = substr($finding, 0, $split);
                $message = substr($finding, ($split + 1));
                $phpcsFile->addWarningOnLine(trim($message), $line, 'ExternalTool');
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
