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
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer\Files;

use Symplify\PHP7_CodeSniffer\Ruleset;
use Symplify\PHP7_CodeSniffer\Config;

class DummyFile extends File
{


    /**
     * Creates a DummyFile object and sets the content.
     *
     * @param string                   $content The content of the file.
     * @param \Symplify\PHP7_CodeSniffer\Ruleset $ruleset The ruleset used for the run.
     * @param \Symplify\PHP7_CodeSniffer\Config  $config  The config data for the run.
     *
     * @return void
     */
    public function __construct($content, Ruleset $ruleset, Config $config)
    {
        $this->setContent($content);

        // See if a filename was defined in the content.
        // This is done by including: phpcs_input_file: [file path]
        // as the first line of content.
        if ($content !== null) {
            if (substr($content, 0, 17) === 'phpcs_input_file:') {
                $eolPos   = strpos($content, $this->eolChar);
                $filename = trim(substr($content, 17, ($eolPos - 17)));
                $content  = substr($content, ($eolPos + strlen($this->eolChar)));
                $path     = $filename;

                $this->setContent($content);
            }
        }

        return parent::__construct($path, $ruleset, $config);

    }//end __construct()


}//end class
