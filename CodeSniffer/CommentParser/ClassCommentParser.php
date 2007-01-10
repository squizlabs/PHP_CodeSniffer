<?php
/**
 * Parses Class doc comments.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/CommentParser/AbstractParser.php';

/**
 * Parses Class doc comments.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_CommentParser_ClassCommentParser extends PHP_CodeSniffer_CommentParser_AbstractParser
{

    /**
     * The package element of this class.
     *
     * @var SingleElement
     */
    private $_package = null;

    /**
     * The subpackage element of this class.
     *
     * @var SingleElement
     */
    private $_subpackage = null;

    /**
     * The version element of this class.
     *
     * @var SingleElement
     */
    private $_version = null;

    /**
     * The category element of this class.
     *
     * @var SingleElement
     */
    private $_category = null;

    /**
     * The copyright element of this class.
     *
     * @var SingleElement
     */
    private $_copyright = null;

    /**
     * The licence element of this class.
     *
     * @var PairElement
     */
    private $_license = null;

    /**
     * The author elements of this class.
     *
     * @var array(SingleElement)
     */
    private $_authors = array();


    /**
     * Returns the allowed tags withing a class comment.
     *
     * @return array(string => int)
     */
    protected function getAllowedTags()
    {
        return array(
                'category'   => false,
                'package'    => true,
                'subpackage' => true,
                'author'     => false,
                'copyright'  => true,
                'license'    => false,
                'version'    => true,
               );

    }//end getAllowedTags()


    /**
     * Parses the license tag of this class comment.
     *
     * @param array $tokens The tokens that comprise this tag.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    protected function parseLicense($tokens)
    {
        $this->_license = new PHP_CodeSniffer_CommentParser_PairElement($this->previousElement, $tokens, 'license');
        return $this->_license;

    }//end parseLicense()


    /**
     * Parses the copyright tag of this class comment.
     *
     * @param array $tokens The tokens that comprise this tag.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    protected function parseCopyright($tokens)
    {
        $this->_copyright = new PHP_CodeSniffer_CommentParser_SingleElement($this->previousElement, $tokens, 'copyright');
        return $this->_copyright;

    }//end parseCopyright()


    /**
     * Parses the category tag of this class comment.
     *
     * @param array $tokens The tokens that comprise this tag.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    protected function parseCategory($tokens)
    {
        $this->_category = new PHP_CodeSniffer_CommentParser_SingleElement($this->previousElement, $tokens, 'category');
        return $this->_category;

    }//end parseCategory()


    /**
     * Parses the author tag of this class comment.
     *
     * @param array $tokens The tokens that comprise this tag.
     *
     * @return array(PHP_CodeSniffer_CommentParser_SingleElement)
     */
    protected function parseAuthor($tokens)
    {
        $author = new PHP_CodeSniffer_CommentParser_SingleElement($this->previousElement, $tokens, 'author');
        $this->_authors[] = $author;
        return $author;

    }//end parseAuthor()


    /**
     * Parses the version tag of this class comment.
     *
     * @param array $tokens The tokens that comprise this tag.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    protected function parseVersion($tokens)
    {
        $this->_version = new PHP_CodeSniffer_CommentParser_SingleElement($this->previousElement, $tokens, 'version');
        return $this->_version;

    }//end parseVersion()


    /**
     * Parses the package tag found in this test.
     *
     * @param array $tokens The tokens that comprise this var.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    protected function parsePackage($tokens)
    {
        $this->_package = new PHP_CodeSniffer_CommentParser_SingleElement($this->previousElement, $tokens, 'package');
        return $this->_package;

    }//end parsePackage()


    /**
     * Parses the package tag found in this test.
     *
     * @param array $tokens The tokens that comprise this var.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    protected function parseSubpackage($tokens)
    {
        $this->_subpackage = new PHP_CodeSniffer_CommentParser_SingleElement($this->previousElement, $tokens, 'subpackage');
        return $this->_subpackage;

    }//end parseSubpackage()


    /**
     * Returns the authors of this class comment.
     *
     * @return array(PHP_CodeSniffer_CommentParser_SingleElement)
     */
    public function getAuthors()
    {
        return $this->_authors;

    }//end getAuthors()


    /**
     * Returns the version of this class comment.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    public function getVersion()
    {
        return $this->_version;

    }//end getVersion()


    /**
     * Returns the license of this class comment.
     *
     * @return PHP_CodeSniffer_CommentParser_PairElement
     */
    public function getLicense()
    {
        return $this->_license;

    }//end getLicense()


    /**
     * Returns the copyright of this class comment.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    public function getCopyright()
    {
        return $this->_copyright;

    }//end getCopyright()


    /**
     * Returns the category of this class comment.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    public function getCategory()
    {
        return $this->_category;

    }//end getCategory()


    /**
     * Returns the package that this class belongs to.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    public function getPackage()
    {
        return $this->_package;

    }//end getPackage()


    /**
     * Returns the subpackage that this class belongs to.
     *
     * @return PHP_CodeSniffer_CommentParser_SingleElement
     */
    public function getSubpackage()
    {
        return $this->_subpackage;

    }//end getSubpackage()


}//end class

?>
