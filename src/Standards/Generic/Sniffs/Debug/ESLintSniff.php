<?php
/**
 * Runs eslint on the file.
 *
 * @author    Ryan McCue <ryan+gh@hmn.md>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Debug;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Config;

class ESLintSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['JS'];

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
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If jshint.js could not be run
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $eslintPath = Config::getExecutablePath('eslint');
        if ($eslintPath === null) {
            return;
        }

        $filename = $phpcsFile->getFilename();

        $configFile = $this->configFile;
        if (empty($configFile) === true) {
            // Attempt to autodetect.
            $candidates = glob('.eslintrc{.js,.yaml,.yml,.json}', GLOB_BRACE);
            if (empty($candidates) === false) {
                $configFile = $candidates[0];
            }
        }

        $eslintOptions = ['--format json'];
        if (empty($configFile) === false) {
            $eslintOptions[] = '--config '.escapeshellarg($configFile);
        }

        $cmd = escapeshellcmd(escapeshellarg($eslintPath).' '.implode(' ', $eslintOptions).' '.escapeshellarg($filename));

        // Execute!
        exec($cmd, $stdout, $code);

        if ($code <= 0) {
            // No errors, continue.
            return ($phpcsFile->numTokens + 1);
        }

        $data = json_decode(implode("\n", $stdout));
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Ignore any errors.
            return ($phpcsFile->numTokens + 1);
        }

        // Data is a list of files, but we only pass a single one.
        $messages = $data[0]->messages;
        foreach ($messages as $error) {
            $message = 'eslint says: '.$error->message;
            if (empty($error->fatal) === false || $error->severity === 2) {
                $phpcsFile->addErrorOnLine($message, $error->line, 'ExternalTool');
            } else {
                $phpcsFile->addWarningOnLine($message, $error->line, 'ExternalTool');
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
