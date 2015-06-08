<?php
/**
 * Created by PhpStorm.
 * User: oli
 * Date: 6/5/15
 * Time: 3:56 PM
 */

class PHP_CodeSniffer_ExcludeFilter extends RecursiveFilterIterator
{
    protected $_phpcs;
    protected $_root_path;
    protected $_extensions = array();
    protected $_recurse = array();

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

    public function hasChildren()
    {
        return $this->_recurse && parent::hasChildren();
    }

    /**
     * Get the filter for the child directories
     *
     * @return PHP_CodeSniffer_ExcludeFilter
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
        //Check if file is a hidden file
        if($this->getFilename() !== '.' && substr($this->getFilename(), 0, 1) == '.'){
            return false;
        }

        //Check if a file and no extension, or is a g
        if (($this->isFile() && !$this->getExtension()) || $this->isDot()) {
            return false;
        }

        return $this->_phpcs->shouldProcessFile($this->getPathname(), $this->_root_path);
    }
}