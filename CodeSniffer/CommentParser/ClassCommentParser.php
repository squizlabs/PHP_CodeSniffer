<?php
/**
 * +------------------------------------------------------------------------+
 * | BSD Licence                                                            |
 * +------------------------------------------------------------------------+
 * | This software is available to you under the BSD license,               |
 * | available in the LICENSE file accompanying this software.              |
 * | You may obtain a copy of the License at                                |
 * |                                                                        |
 * | http://matrix.squiz.net/developer/tools/php_cs/licence                 |
 * |                                                                        |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS    |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT      |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR  |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT   |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,  |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT       |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,  |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY  |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT    |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   |
 * +------------------------------------------------------------------------+
 * | Copyright (c), 2006 Squiz Pty Ltd (ABN 77 084 670 600).                |
 * | All rights reserved.                                                   |
 * +------------------------------------------------------------------------+
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer/CommentParser/AbstractParser.php';

/**
 * Parses Class doc comments.
 *
 * @package  PHP_CodeSniffer
 * @category CommentParser
 * @author   Squiz Pty Ltd
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
                'package'   => true,
                'version'   => true,
                'author'    => false,
                'category'  => false,
                'copyright' => false,
                'licence'   => false,
               );

    }//end getAllowedTags()


    /**
     * Parses the license tag of this class comment.
     *
     * @param array $tokens The tokens that comprise this tag.
     *
     * @return array(PHP_CodeSniffer_CommentParser_SingleElement)
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
     * @return array(PHP_CodeSniffer_CommentParser_SingleElement)
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
     * @return array(PHP_CodeSniffer_CommentParser_SingleElement)
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


}//end class

?>
