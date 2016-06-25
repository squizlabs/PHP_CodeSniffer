<?php
/**
 * Represents a list of files on the file system that are to be checked during the run.
 *
 * File objects are created as needed rather than all at once.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Util;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;

class FileList implements \Iterator, \Countable
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
     * @var \PHP_CodeSniffer\Config
     */
    public $config = null;

    /**
     * The ruleset used for the run.
     *
     * @var \PHP_CodeSniffer\Ruleset
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
     * @param \PHP_CodeSniffer\Config  $config  The config data for the run.
     * @param \PHP_CodeSniffer\Ruleset $ruleset The ruleset used for the run.
     *
     * @return void
     */
    public function __construct(Config $config, Ruleset $ruleset)
    {
        $this->ruleset = $ruleset;
        $this->config  = $config;

        $paths = $config->files;
        foreach ($paths as $path) {
            $this->addFile($path);
        }//end foreach

        reset($this->files);
        $this->numFiles = count($this->files);

    }//end __construct()


    /**
     * Add a file to the list.
     *
     * If a file object has already been created, it can be passed here.
     * If it is left NULL, it will be created when accessed.
     *
     * @param string                      $path The path to the file being added.
     * @param \PHP_CodeSniffer\Files\File $file The file being added.
     *
     * @return void
     */
    public function addFile($path, $file=null)
    {
        // No filtering is done for STDIN.
        if ($path === 'STDIN'
            || ($file !== null
            && get_class($file) === 'PHP_CodeSniffer\Files\DummyFile')
        ) {
            $this->files[$path] = $file;
            return;
        }

        $filterClass = $this->getFilterClass();

        $di       = new \RecursiveArrayIterator(array($path));
        $filter   = new $filterClass($di, $path, $this->config, $this->ruleset);
        $iterator = new \RecursiveIteratorIterator($filter);

        foreach ($iterator as $path) {
            $this->files[$path] = $file;
        }

    }//end addFile()


    /**
     * Get the class name of the filter being used for the run.
     *
     * @return string
     */
    private function getFilterClass()
    {
        $filterType = $this->config->filter;

        if ($filterType === null) {
            $filterClass = '\PHP_CodeSniffer\Filters\Filter';
        } else {
            if (strpos($filterType, '.') !== false) {
                // This is a path to a custom filter class.
                $filename = realpath($filterType);
                if ($filename === false) {
                    echo "ERROR: Custom filter \"$filterType\" not found".PHP_EOL;
                    exit(2);
                }

                $filterClass = \PHP_CodeSniffer\Autoload::loadFile($filename);
            } else {
                $filterClass = '\PHP_CodeSniffer\Filters\\'.$filterType;
            }
        }

        return $filterClass;

    }//end getFilterClass()


    /**
     * Rewind the iterator to the first file.
     *
     * @return void
     */
    function rewind()
    {
        reset($this->files);

    }//end rewind()


    /**
     * Get the file that is currently being processed.
     *
     * @return \PHP_CodeSniffer\Files\File
     */
    function current()
    {
        $path = key($this->files);
        if ($this->files[$path] === null) {
            $this->files[$path] = new LocalFile($path, $this->ruleset, $this->config);
        }

        return $this->files[$path];

    }//end current()


    /**
     * Return the file path of the current file being processed.
     *
     * @return void
     */
    function key()
    {
        return key($this->files);

    }//end key()


    /**
     * Move forward to the next file.
     *
     * @return void
     */
    function next()
    {
        next($this->files);

    }//end next()


    /**
     * Checks if current position is valid.
     *
     * @return boolean
     */
    function valid()
    {
        if (current($this->files) === false) {
            return false;
        }

        return true;

    }//end valid()


    /**
     * Return the number of files in the list.
     *
     * @return integer
     */
    function count()
    {
        return $this->numFiles;

    }//end count()


}//end class
