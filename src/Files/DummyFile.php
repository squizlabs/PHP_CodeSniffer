<?php
/**
 * A dummy file represents a chunk of text that does not have a file system location.
 *
 * Dummy files can also represent a changed (but not saved) version of a file
 * and so can have a file path either set manually, or set by putting
 * phpcs_input_file: /path/to/file
 * as the first line of the file contents.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;

class DummyFile extends File
{


    /**
     * Creates a DummyFile object and sets the content.
     *
     * @param string                   $content The content of the file.
     * @param \PHP_CodeSniffer\Ruleset $ruleset The ruleset used for the run.
     * @param \PHP_CodeSniffer\Config  $config  The config data for the run.
     *
     * @return void
     */
    public function __construct($content, Ruleset $ruleset, Config $config)
    {
        $this->setContent($content);

        // See if a filename was defined in the content.
        // This is done by including: phpcs_input_file: [file path]
        // as the first line of content.
        $path = 'STDIN';
        if ($content !== null) {
            if (substr($content, 0, 17) === 'phpcs_input_file:') {
                $eolPos   = strpos($content, $this->eolChar);
                $filename = trim(substr($content, 17, ($eolPos - 17)));
                $content  = substr($content, ($eolPos + strlen($this->eolChar)));
                $path     = $filename;

                $this->setContent($content);
            }
        }

        // The CLI arg overrides anything passed in the content.
        if ($config->stdinPath !== null) {
            $path = $config->stdinPath;
        }

        parent::__construct($path, $ruleset, $config);

    }//end __construct()


    /**
     * Set the error, warning, and fixable counts for the file.
     *
     * @param int $errorCount   The number of errors found.
     * @param int $warningCount The number of warnings found.
     * @param int $fixableCount The number of fixable errors found.
     * @param int $fixedCount   The number of errors that were fixed.
     *
     * @return void
     */
    public function setErrorCounts($errorCount, $warningCount, $fixableCount, $fixedCount)
    {
        $this->errorCount   = $errorCount;
        $this->warningCount = $warningCount;
        $this->fixableCount = $fixableCount;
        $this->fixedCount   = $fixedCount;

    }//end setErrorCounts()


}//end class
