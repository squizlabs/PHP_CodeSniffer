<?php
/**
 * A base filter class for filtering out files and folders during a run.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer\Filters;

use RecursiveIterator;
use Symplify\PHP7_CodeSniffer\Util;
use Symplify\PHP7_CodeSniffer\Ruleset;
use Symplify\PHP7_CodeSniffer\Config;

class Filter extends \RecursiveFilterIterator
{
    /**
     * The top-level path we are filtering.
     *
     * @var string
     */
    protected $basedir = null;

    /**
     * The config data for the run.
     *
     * @var \Symplify\PHP7_CodeSniffer\Config
     */
    protected $config = null;

    /**
     * The ruleset used for the run.
     *
     * @var \Symplify\PHP7_CodeSniffer\Ruleset
     */
    protected $ruleset = null;

    public function __construct(RecursiveIterator $iterator, string $basedir, Config $config, Ruleset $ruleset)
    {
        parent::__construct($iterator);
        $this->basedir = $basedir;
        $this->config  = $config;
        $this->ruleset = $ruleset;

    }//end __construct()


    /**
     * Check whether the current element of the iterator is acceptable.
     *
     * Files are checked for allowed extensions and ignore patterns.
     * Directories are checked for ignore patterns only.
     *
     * @return bool
     */
    public function accept()
    {
        $filePath = Util\Common::realpath($this->current());
        if ($filePath === false) {
            return false;
        }

        if ($this->shouldProcessFile($filePath) === false) {
            return false;
        }

        return true;

    }//end accept()


    /**
     * Checks filtering rules to see if a file should be checked.
     *
     * Checks both file extension filters and path ignore filters.
     *
     * @param string $path The path to the file being checked.
     *
     * @return bool
     */
    protected function shouldProcessFile($path)
    {
        // Check that the file's extension is one we are checking.
        // We are strict about checking the extension and we don't
        // let files through with no extension or that start with a dot.
        $fileName  = basename($path);
        $fileParts = explode('.', $fileName);
        if ($fileParts[0] === $fileName || $fileParts[0] === '') {
            return false;
        }

        // Checking multi-part file extensions, so need to create a
        // complete extension list and make sure one is allowed.
        $extensions = array();
        array_shift($fileParts);
        foreach ($fileParts as $part) {
            $extensions[implode('.', $fileParts)] = 1;
            array_shift($fileParts);
        }

        $matches = array_intersect_key($extensions, $this->config->extensions);
        if (empty($matches) === true) {
            return false;
        }

        return true;

    }//end shouldProcessFile()
}//end class
