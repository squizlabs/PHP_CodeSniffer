<?php

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Util;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;

/**
 * A class to process command line phpcs scripts.
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
 * A class to process command line phpcs scripts.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class FileList implements \Iterator, \Countable
{
    private $files    = array();
    private $numFiles = 0;
    private $config = null;

    /**
     * An array of extensions for files we will check.
     *
     * @var array
     */
    public $allowedFileExtensions = array();

    /**
     * An array of default extensions and associated tokenizers.
     *
     * If no extensions are set, these will be used as the defaults.
     * If extensions are set, these will be used when the correct tokenizer
     * can not be determined, such as when checking a passed filename instead
     * of files in a directory.
     *
     * @var array
     */
    public $defaultFileExtensions = array(
                                     'php' => 'PHP',
                                     'inc' => 'PHP',
                                     'js'  => 'JS',
                                     'css' => 'CSS',
                                    );

    /**
     * An array of patterns to use for skipping files.
     *
     * @var array
     */
    protected $ignorePatterns = array();


    public function __construct(
        Config $config,
        Ruleset $ruleset
    ) {

        $paths = $config->files;
        $local = $config->local;
        $extensions = $config->extensions;
        $ignore = $ruleset->ignorePatterns;

        if (empty($extensions) === true) {
            $this->allowedFileExtensions = $this->defaultFileExtensions;
        } else {
            $this->setAllowedFileExtensions($extensions);
        }

        $this->ignorePatterns = $ignore;
        $this->ruleset = $ruleset;
        $this->config = $config;

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
     * Sets an array of file extensions that we will allow checking of.
     *
     * If the extension is one of the defaults, a specific tokenizer
     * will be used. Otherwise, the PHP tokenizer will be used for
     * all extensions passed.
     *
     * @param array $extensions An array of file extensions.
     *
     * @return void
     */
    public function setAllowedFileExtensions(array $extensions)
    {
        $newExtensions = array();
        foreach ($extensions as $ext) {
            $slash = strpos($ext, '/');
            if ($slash !== false) {
                // They specified the tokenizer too.
                list($ext, $tokenizer) = explode('/', $ext);
                $newExtensions[$ext]   = strtoupper($tokenizer);
                continue;
            }

            if (isset($this->allowedFileExtensions[$ext]) === true) {
                $newExtensions[$ext] = $this->allowedFileExtensions[$ext];
            } else if (isset($this->defaultFileExtensions[$ext]) === true) {
                $newExtensions[$ext] = $this->defaultFileExtensions[$ext];
            } else {
                $newExtensions[$ext] = 'PHP';
            }
        }

        $this->allowedFileExtensions = $newExtensions;

    }//end setAllowedFileExtensions()


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

        $matches = array_intersect_key($extensions, $this->allowedFileExtensions);
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


    function rewind()
    {
        reset($this->files);

    }//end rewind()


    function current()
    {
        $path = key($this->files);
        if ($this->files[$path] === null) {
            $parts = explode('.', $path);
            $extension = array_pop($parts);
            if (isset($this->allowedFileExtensions[$extension]) === true) {
                $fileType = $this->allowedFileExtensions[$extension];
            } else if (isset($this->defaultFileExtensions[$extension]) === true) {
                $fileType = $this->defaultFileExtensions[$extension];
            } else {
                // Revert to default.
                $fileType = 'PHP';
            }

            $this->files[$path] = new LocalFile($path, $fileType, $this->ruleset, $this->config);
        }

        return $this->files[$path];

    }//end current()


    function key()
    {
        return key($this->files);

    }//end key()


    function next()
    {
        next($this->files);

    }//end next()


    function valid()
    {
        if (current($this->files) === false) {
            return false;
        }

        return true;

    }//end valid()

    function count()
    {
        return $this->numFiles;

    }//end valid()


}//end class
