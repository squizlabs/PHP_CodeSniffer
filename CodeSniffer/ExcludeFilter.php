<?php
/**
 * Class PHP_CodeSniffer_ExcludeFilter
 *
 * This filter is used by the RecursiveDirectoryIterator to pre-process files and check for inclusion in the files to
 * iterate. If accept() returns false, the file or directory is excluded from the result set. If a directory is excluded
 * then all subdirectories and files are also excluded
 */
class PHP_CodeSniffer_ExcludeFilter extends RecursiveFilterIterator
{
    /**
     * A PHP_CodeSniffer instance, used to check if the file should be excluded
     *
     * @var PHP_CodeSniffer
     */
    protected $_phpcs;

    /**
     * The root path that the phpcs command was run from, used to generate relative file paths
     *
     * @var
     */
    protected $_root_path;

    /**
     * Recurse into subdirectories?
     *
     * @var bool
     */
    protected $_recurse = true;

    /**
     * Object constructor
     *
     * @param RecursiveIterator $iterator
     * @param $root_path - The root path that the filtering is being performed on
     * @param array $excludes - An array of exclude paths
     * @param array $extensions - An array of allowed extensions
     */
    public function __construct(RecursiveIterator $iterator, PHP_CodeSniffer $phpcs, $root_path, $recurse = true)
    {
        parent::__construct($iterator);

        $this->_phpcs = $phpcs;
        $this->_root_path = $root_path;
        $this->_recurse = $recurse;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the inner iterator's current element has children
     * @link http://php.net/manual/en/recursivefilteriterator.haschildren.php
     * @return bool true if the inner iterator has children, otherwise false
     */
    public function hasChildren()
    {
        return $this->_recurse && parent::hasChildren();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the inner iterator's children contained in a RecursiveFilterIterator
     * A new instance of set is created, passing the appropriate constructor arguments
     *
     * @link http://php.net/manual/en/recursivefilteriterator.getchildren.php
     * @return RecursiveFilterIterator containing the inner iterator's children.
     */
    public function getChildren()
    {
        $filter = new self(
            new RecursiveDirectoryIterator($this->getPathname(), RecursiveDirectoryIterator::SKIP_DOTS),
            $this->_phpcs,
            $this->_root_path,
            $this->_recurse
        );

        return $filter;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the current element of the iterator is acceptable
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {
        //Check if file is a hidden file, or a dotfile
        if ($this->getFilename() !== '.' && substr($this->getFilename(), 0, 1) == '.') {
            return false;
        }

        //Check if a file and no extension
        if ($this->isFile() && !$this->getExtension()) {
            return false;
        }

        return $this->_phpcs->shouldProcessFile($this->getPathname(), $this->_root_path);
    }
}