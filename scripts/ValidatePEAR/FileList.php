<?php
/**
 * Retrieve a filtered file list.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

/**
 * Class to create a file list with filtering.
 */
class FileList
{

    /**
     * The path to the project root directory.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * Recursive directory iterator.
     *
     * @var \DirectoryIterator
     */
    protected $fileIterator;

    /**
     * Base regex to use if no filter regex is provided.
     *
     * Matches based on:
     * - File path starts with the project root (replacement done in constructor).
     * - Don't match .git/ files.
     * - Don't match dot files, i.e. "." or "..".
     * - Don't match backup files.
     * - Match everything else in a case-insensitive manner.
     *
     * @var string
     */
    private $baseRegex = '`^%s(?!\.git/)(?!(.*/)?\.+$)(?!.*\.(bak|orig)).*$`Dix';


    /**
     * Constructor.
     *
     * @param string $directory The directory to examine.
     * @param string $rootPath  Path to the project root.
     * @param string $filter    PCRE regular expression to filter the file list with.
     */
    public function __construct($directory, $rootPath='', $filter='')
    {
        $this->rootPath = $rootPath;

        $directory = new \RecursiveDirectoryIterator(
            $directory,
            \RecursiveDirectoryIterator::UNIX_PATHS
        );
        $flattened = new \RecursiveIteratorIterator(
            $directory,
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        if ($filter === '') {
            $filter = sprintf($this->baseRegex, preg_quote($this->rootPath));
        }

        $this->fileIterator = new \RegexIterator($flattened, $filter);

        return $this;

    }//end __construct()


    /**
     * Retrieve the filtered file list as an array.
     *
     * @return array
     */
    public function getList()
    {
        $fileList = [];

        foreach ($this->fileIterator as $file) {
            $fileList[] = str_replace($this->rootPath, '', $file);
        }

        return $fileList;

    }//end getList()


}//end class
