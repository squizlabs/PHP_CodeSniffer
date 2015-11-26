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

        $paths      = $config->files;
        $local      = $config->local;
        $extensions = $config->extensions;
        $ignore     = array_merge($config->ignored, $ruleset->getIgnorePatterns());

        $this->ignorePatterns = $ignore;
        $this->ruleset        = $ruleset;
        $this->config         = $config;

        foreach ($paths as $path) {
            if (is_dir($path) === true || Util\Common::isPharFile($path) === true) {
                if (Util\Common::isPharFile($path) === true) {
                    $path = 'phar://'.$path;
                }

                if ($local === true) {
                    $di = new \DirectoryIterator($path);
                } else {
                    $di = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($path),
                        0,
                        \RecursiveIteratorIterator::CATCH_GET_CHILD
                    );
                }

                foreach ($di as $file) {
                    // Check if the file exists after all symlinks are resolved.
                    $filePath = Util\Common::realpath($file->getPathname());
                    if ($filePath === false) {
                        continue;
                    }

                    if (is_dir($filePath) === true) {
                        continue;
                    }

                    if ($this->shouldProcessFile($file->getPathname(), $path) === false) {
                        continue;
                    }

                    $this->files[$file->getPathname()] = null;
                }//end foreach
            } else {
                if ($this->shouldIgnoreFile($path, dirname($path)) === true) {
                    continue;
                }

                $this->files[$path] = null;
            }//end if
        }//end foreach

        reset($this->files);
        $this->numFiles = count($this->files);

    }//end __construct()


    /**
     * Checks filtering rules to see if a file should be checked.
     *
     * Checks both file extension filters and path ignore filters.
     *
     * @param string $path    The path to the file being checked.
     * @param string $basedir The directory to use for relative path checks.
     *
     * @return bool
     */
    public function shouldProcessFile($path, $basedir)
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

        // If the file's path matches one of our ignore patterns, skip it.
        if ($this->shouldIgnoreFile($path, $basedir) === true) {
            return false;
        }

        return true;

    }//end shouldProcessFile()


    /**
     * Checks filtering rules to see if a file should be ignored.
     *
     * @param string $path    The path to the file being checked.
     * @param string $basedir The directory to use for relative path checks.
     *
     * @return bool
     */
    public function shouldIgnoreFile($path, $basedir)
    {
        $relativePath = $path;
        if (strpos($path, $basedir) === 0) {
            // The +1 cuts off the directory separator as well.
            $relativePath = substr($path, (strlen($basedir) + 1));
        }

        foreach ($this->ignorePatterns as $pattern => $type) {
            // Maintains backwards compatibility in case the ignore pattern does
            // not have a relative/absolute value.
            if (is_int($pattern) === true) {
                $pattern = $type;
                $type    = 'absolute';
            }

            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            // We assume a / directory separator, as do the exclude rules
            // most developers write, so we need a special case for any system
            // that is different.
            if (DIRECTORY_SEPARATOR === '\\') {
                $replacements['/'] = '\\\\';
            }

            $pattern = strtr($pattern, $replacements);

            if ($type === 'relative') {
                $testPath = $relativePath;
            } else {
                $testPath = $path;
            }

            $pattern = '`'.$pattern.'`i';
            if (preg_match($pattern, $testPath) === 1) {
                return true;
            }
        }//end foreach

        return false;

    }//end shouldIgnoreFile()


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
     * @return \PHP_CodeSniffer\Files\LocalFile
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
