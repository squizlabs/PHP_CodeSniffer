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
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer/Sniff.php';
require_once 'PHP/CodeSniffer/CommentParser/ClassCommentParser.php';
require_once 'PHP/CodeSniffer/Standards/GeneralDocCommentHelper.php';

/**
 * Parses and verifies the doc comments for files.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>Short description must start with a capital letter and end with a period.</li>
 *  <li>There must be one blank newline after the short description.</li>
 *  <li>A PHP version is specified.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */

class PEAR_Sniffs_Commenting_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The header comment parser for the current file.
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser
     */
    protected $_fp = null;

    /**
    * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $_phpcsFile = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_phpcsFile = $phpcsFile;

        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($this->_phpcsFile->findPrevious(T_OPEN_TAG, 0, $stackPtr) === true) {
                return;
            }
        }

        $tokens = $this->_phpcsFile->getTokens();

        // Extract the header comment docblock.
        // Find the next non whitespace token.
        $commentStart = $this->_phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT) {
            $this->_phpcsFile->addError('Missing file doc comment', $stackPtr + 1);
            return;
        }
        $commentEnd = $this->_phpcsFile->findNext(T_DOC_COMMENT, $commentStart + 1, null, true) - 1;
        $comment    = $this->_phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);

        // Parse the header comment docblock.
        try {
            $this->_fp = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment);
            $this->_fp->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = $e->getLineWithinComment() + $commentStart;
            $this->_phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        // Validate the generic comment.
        PHP_CodeSniffer_Standards_GeneralDocCommentHelper::validate($this->_fp, $this->_phpcsFile, $commentStart);

        // Check the PHP Version.
        $found        = false;
        $commentArray = explode("\n\n", $this->_fp->getComment()->getRawContent());
        foreach ($commentArray as $commentElem) {
            if (substr($commentElem, 0, 12) === ' PHP version') {
                $found = true;
                break;
            }
        }
        if ($found === false) {
            $error = 'PHP version not specified';
            $phpcsFile->addWarning($error, $commentEnd);
        }

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);

    }//end process()


    /**
     * Processes each required or optional tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processTags($commentStart, $commentEnd)
    {
        $fp = $this->_fp;

         // Tags in correct order and related info.
        $tags = array(
                    'category'   => array(
                                        'required'       => true,
                                        'allow_multiple' => false,
                                    ),
                    'package'    => array(
                                        'required'       => true,
                                        'allow_multiple' => false,
                                    ),
                    'author'     => array(
                                        'required'       => true,
                                        'allow_multiple' => true,
                                    ),
                    'copyright'  => array(
                                        'required'       => false,
                                        'allow_multiple' => false,
                                    ),
                    'license'    => array(
                                        'required'       => true,
                                        'allow_multiple' => false,
                                    ),
                    'version'    => array(
                                        'required'       => false,
                                        'allow_multiple' => false,
                                    ),
                    'link'       => array(
                                        'required'       => true,
                                        'allow_multiple' => true,
                                    ),
                    'see'        => array(
                                        'required'       => false,
                                        'allow_multiple' => true,
                                    ),
                    'since'      => array(
                                        'required'       => false,
                                        'allow_multiple' => false,
                                    ),
                    'deprecated' => array(
                                        'required'       => false,
                                        'allow_multiple' => false,
                                    ),
                   );

        $foundTags   = $fp->getTagOrders();
        $orderIndex  = 0;
        $indentation = array();
        $longestTag  = 0;
        $errorPos    = 0;

        foreach ($tags as $tag => $info) {

            // Required tag missing.
            if (($info['required'] === true) && !in_array($tag, $foundTags)) {
                $error    = "Missing $tag tag";
                $errorPos = ($errorPos === 0) ? $commentEnd : $errorPos;
                $this->_phpcsFile->addError($error, $errorPos);
                continue;
            }

             // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if ($info['allow_multiple'] === true) {
                $tagName .= 's';
            }

            $getMethod  = 'get'.$tagName;
            $tagElement = $fp->$getMethod();
            if (is_null($tagElement) || empty($tagElement)) {
                continue;
            }

            $errorPos = $commentStart;
            if (!is_array($tagElement)) {
                $errorPos = $commentStart + $tagElement->getLine();
            }

            // Get the tag order.
            $foundIndexes = array_keys($foundTags, $tag);

            if (count($foundIndexes) > 1) {
                // Multiple occurance not allowed.
                if ($info['allow_multiple'] === false) {
                    $error = "Only one $tag tag is allowed";
                    $this->_phpcsFile->addError($error, $errorPos);
                } else {
                    // Make sure same tags are grouped together.
                    $count = $foundIndexes[0];
                    foreach ($foundIndexes as $index) {
                        if ($index != $count) {
                            $error = ucfirst($tag).' tags must be grouped together';
                            $this->_phpcsFile->addError($error, $errorPos);
                        }
                        $count++;
                    }
                }
            }

            // Check tag order.
            if ($foundIndexes[0] > $orderIndex) {
                $orderIndex = $foundIndexes[0];
            } else {
                $error = "The order of $tag tag is wrong";
                $this->_phpcsFile->addError($error, $errorPos);
            }

            // Store the indentation for checking.
            $len = strlen($tag);
            if ($len > $longestTag) $longestTag = $len;
            if (is_array($tagElement)) {
                foreach ($tagElement as $key => $element) {
                    $indentation[] = array(
                                        'tag'   => $tag,
                                        'space' => $this->_getIndentation($tag, $element),
                                        'line'  => $element->getLine(),
                                     );
                }
            } else {
                $indentation[] = array(
                                    'tag'   => $tag,
                                    'space' => $this->_getIndentation($tag, $tagElement),
                                );
            }

            $method = '_process'.$tagName;
            if (method_exists($this, $method)) {
                $this->$method($errorPos);
            } else {
                if (is_array($tagElement)) {
                    foreach ($tagElement as $key => $element) {
                        $element->process($this->_phpcsFile, $commentStart);
                    }
                } else {
                     $tagElement->process($this->_phpcsFile, $commentStart);
                }
            }

        }

        foreach ($indentation as $indentInfo) {
            if ($indentInfo['space'] !== 0 && $indentInfo['space'] !== $longestTag+1) {
                $expected     = ($longestTag - strlen($indentInfo['tag'])) + 1;
                $space        = $indentInfo['space'] - strlen($indentInfo['tag']);
                $error        = ucfirst($indentInfo['tag'])." tag does not align, expected $expected space(s) but found $space";
                $getTagMethod = 'get'.ucfirst($indentInfo['tag']);
                if ($tags[$indentInfo['tag']]['allow_multiple'] === true) {
                    $line = $indentInfo['line'];
                } else {
                    $tagElem = $this->_fp->$getTagMethod();
                    $line    = $tagElem->getLine();
                }
                $this->_phpcsFile->addError($error, $commentStart + $line);
            }
        }

    }//end processTags()


    /**
     * Get the indentation information of each tag.
     *
     * @param string                                   $tagName    The name of the doc comment element.
     * @param PHP_CodeSniffer_CommentParser_DocElement $tagElement The doc comment element.
     *
     * @return void
     */
    private function _getIndentation($tagName, $tagElement)
    {
        if ($tagElement instanceof PHP_CodeSniffer_CommentParser_SingleElement) {
            if ($tagElement->getContent() !== '') {
                return strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeContent(), ' ');
            }
        } else if ($tagElement instanceof PHP_CodeSniffer_CommentParser_PairElement) {
            if ($tagElement->getValue() !== '') {
                return strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeValue(), ' ');
            }
        }

        return 0;

    }//end _getIndentation()


    /**
     * Process the category tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processCategory($errorPos)
    {
        $category = $this->_fp->getCategory();
        if ($category !== null) {
            $content = $category->getContent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $nameBits  = explode('_', $content);
                    $firstBit  = array_shift($nameBits);
                    $newName   = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }
                    $validName = trim($newName, '_');
                    $error     = "Category name \"$content\" is not valid; consider \"$validName\" instead.";
                    $this->_phpcsFile->addError($error, $errorPos);
                }
            } else {
                $error = 'Category tag must contain a name';
                $this->_phpcsFile->addError($error, $errorPos);
            }
        }

    }//end _processCategory()


    /**
     * Process the package tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processPackage($errorPos)
    {
        $package = $this->_fp->getPackage();
        if ($package !== null) {
            $content = $package->getContent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $nameBits  = explode('_', $content);
                    $firstBit  = array_shift($nameBits);
                    $newName   = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }
                    $validName = trim($newName, '_');
                    $error     = "Category name \"$content\" is not valid; consider \"$validName\" instead.";
                    $this->_phpcsFile->addError($error, $errorPos);
                }
            } else {
                $error = 'Package tag must contain a name';
                $this->_phpcsFile->addError($error, $errorPos);
            }
        }

    }//end _processPackage()


    /**
    * Process the author tag(s) that this header comment has.
    *
    * This function is different from other _process functions
    * as $authors is an array of SingleElements, so we work out
    * the errorPos for each element separately
    *
    * @param int $commentStart The position in the stack where
    *                          the comment started..
    *
    * @return void
    */
    private function _processAuthors($commentStart)
    {
         $authors = $this->_fp->getAuthors();
        // Report missing return.
        if (!empty($authors)) {
            foreach ($authors as $author) {
                $errorPos = $commentStart + $author->getLine();
                $content  = $author->getContent();
                if ($content !== '') {
                    $local = '\da-zA-Z-_+';
                    // Dot character cannot be the first or last character in the local-part.
                    $local_middle = $local.'.\w';
                    if (preg_match('/^[a-zA-Z]+(([\'\,\.\- ][a-zA-Z ])?[a-zA-Z]*)*\s+<(['.$local.']['.$local_middle.']*['.$local.']@[\da-zA-Z][-.\w]*[\da-zA-Z]\.[a-zA-Z]{2,7})>$/', $content) === 0) {
                        $error = 'Content of the author tag must be in the form "Author Name <author@example.com>"';
                        $this->_phpcsFile->addError($error, $errorPos);
                    }
                } else {
                    $error = 'Content missing for author tag';
                    $this->_phpcsFile->addError($error, $errorPos);
                }
            }
        }

    }//end _processAuthor()


    /**
     * Process the copyright tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processCopyright($errorPos)
    {
        $copyright = $this->_fp->getCopyRight();
        if ($copyright !== null) {
            $content = $copyright->getContent();
            if ($content !== '') {
                $matches = Array();
                if (preg_match('/^([0-9]{4})((.{1})([0-9]{4}))? (.+)$/', $content, $matches) !== 0) {
                    // Check earliest-latest year order.
                    if ($matches[3] !== '') {
                        if ($matches[3] !== '-') {
                            $error = 'A hyphen must be used between the earliest and latest year';
                            $this->_phpcsFile->addError($error, $errorPos);
                        }
                        if ($matches[4] !== '' && $matches[4] < $matches[1]) {
                            $error = "Invalid year span \"$matches[1]$matches[3]$matches[4]\" found, use \"$matches[4]-$matches[1]\" instead";
                            $this->_phpcsFile->addWarning($error, $errorPos);
                        }
                    }
                } else {
                    $error = 'Copyright tag must contain a year and the name of the copyright holder';
                    $this->_phpcsFile->addError($error, $errorPos);
                }
            } else {
                $error = 'Copyright tag must contain a year and the name of the copyright holder';
                $this->_phpcsFile->addError($error, $errorPos);
            }
        }

    }//end _processCopyright()


    /**
     * Process the license tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processLicense($errorPos)
    {
        $license = $this->_fp->getLicense();
        if ($license !== null) {
            $value   = $license->getValue();
            $comment = $license->getComment();
            if ($value === '' || $comment === '') {
                $error = 'License tag must contain a URL and a license name';
                $this->_phpcsFile->addError($error, $errorPos);
            }
        }

    }//end _processLicense()


     /**
     * Process the version tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processVersion($errorPos)
    {
        $version = $this->_fp->getVersion();
        if ($version !== null) {
            $content = $version->getContent();
            $matches = Array();
            if (empty($content)) {
                $error = 'Content missing for version tag';
                $this->_phpcsFile->addError($error, $errorPos);
            } else if (preg_match('/^(Release:) (.*)$/', $content, $matches) === 0 && $content !== 'CVS: $Id$') {
                $error = "Invalid version tag \"$content\"";
                $this->_phpcsFile->addWarning($error, $errorPos);
            }
        }

    }//end _processVersion()


}//end class

?>
