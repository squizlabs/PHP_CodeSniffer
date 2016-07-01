<?php

/**
 * Represents a list of files on the file system that are to be checked during the run.
 *
 * File objects are created as needed rather than all at once.
 */

namespace Symplify\PHP7_CodeSniffer\Files;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symplify\PHP7_CodeSniffer\Filters\Filter;
use Symplify\PHP7_CodeSniffer\Util;
use Symplify\PHP7_CodeSniffer\Ruleset;
use Symplify\PHP7_CodeSniffer\Configuration;

final class FileList implements \Iterator, \Countable
{

    /**
     * A list of file paths that are included in the list.
     *
     * @var array
     */
    private $files = array();

    /**
     * The number of files in the list.
     *
     * @var integer
     */
    private $numFiles = 0;

    /**
     * The config data for the run.
     *
     * @var \Symplify\PHP7_CodeSniffer\Configuration
     */
    public $config = null;

    /**
     * The ruleset used for the run.
     *
     * @var \Symplify\PHP7_CodeSniffer\Ruleset
     */
    public $ruleset = null;

    /**
     * An array of patterns to use for skipping files.
     *
     * @var array
     */
    protected $ignorePatterns = array();


    /**
     * Constructs a file list and loads in an array of file paths to process.
     *
     * @param \Symplify\PHP7_CodeSniffer\Configuration  $config  The config data for the run.
     * @param \Symplify\PHP7_CodeSniffer\Ruleset $ruleset The ruleset used for the run.
     *
     * @return void
     */
    public function __construct(Configuration $config, Ruleset $ruleset)
    {
        $this->ruleset = $ruleset;
        $this->config  = $config;

        $paths = $config->files;
        foreach ($paths as $path) {
            $this->addFile($path);
        }//end foreach

        reset($this->files);
        $this->numFiles = count($this->files);
    }


    /**
     * If a file object has already been created, it can be passed here.
     * If it is left NULL, it will be created when accessed.
     */
    public function addFile(string $path, File $file=null)
    {
        $di = new RecursiveArrayIterator(array($path));
        $filter = new Filter($di, $path, $this->config, $this->ruleset);
        $iterator = new RecursiveIteratorIterator($filter);

        foreach ($iterator as $path) {
            $this->files[$path] = $file;
        }
    }

    /**
     * Rewind the iterator to the first file.
     */
    public function rewind()
    {
        reset($this->files);
    }

    /**
     * Get the file that is currently being processed.
     */
    public function current() : File
    {
        $path = key($this->files);
        if ($this->files[$path] === null) {
            $this->files[$path] = new LocalFile($path, $this->ruleset, $this->config);
        }

        return $this->files[$path];
    }

    /**
     * Return the file path of the current file being processed.
     */
    public function key()
    {
        return key($this->files);
    }

    public function next()
    {
        next($this->files);
    }

    /**
     * Checks if current position is valid.
     */
    public function valid() : bool
    {
        if (current($this->files) === false) {
            return false;
        }

        return true;
    }

    public function count() : int
    {
        return $this->numFiles;
    }
}
