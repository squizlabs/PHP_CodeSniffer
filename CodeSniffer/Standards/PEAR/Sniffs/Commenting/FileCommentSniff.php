<?php
/**
 * Parses and verifies the doc comments for files.
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

require_once 'PHP/CodeSniffer/Sniff.php';
require_once 'PHP/CodeSniffer/CommentParser/ClassCommentParser.php';

/**
 * Parses and verifies the doc comments for files.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>A PHP version is specified.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
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
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_phpcsFile = $phpcsFile;

        // We are only interested if this is the first open tag.
        $openTags = array(
                     T_OPEN_TAG,
                     T_CLOSE_TAG,
                    );
        if ($stackPtr !== 0) {
            if ($this->_phpcsFile->findPrevious($openTags, 0, $stackPtr) !== false) {
                return;
            }
        }

        $tokens = $this->_phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart = $this->_phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        // Ignore vim header.
        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            if (strstr($tokens[$commentStart]['content'], 'vim:') !== false) {
                $commentStart = $this->_phpcsFile->findNext(T_WHITESPACE, $commentStart + 1, null, true);
            }
        }

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $this->_phpcsFile->addError('Consider using "/**" style comment for file comment', $stackPtr + 1);
            return;
        } else if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT) {
            $this->_phpcsFile->addError('Missing file doc comment', $stackPtr + 1);
            return;
        } else {

            // Extract the header comment docblock.
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

            // No extra newline before short description.
            $comment      = $this->_fp->getComment();
            $short        = $comment->getShortComment();
            $newlineCount = 0;
            $newlineSpan  = strspn($short, "\n");
            if ($short !== '' && $newlineSpan > 0) {
                $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
                $error = "Extra $line found before file comment short description";
                $phpcsFile->addError($error, $commentStart + 1);
            }
            $newlineCount = substr_count($short, "\n") + 1;

            // Exactly one blank line between short and long description.
            $between        = $comment->getWhiteSpaceBetween();
            $long           = $comment->getLongComment();
            $newlineBetween = substr_count($between, "\n");
            if ($newlineBetween !== 2 && $long !== '') {
                $error = 'There must be exactly one blank line between descriptions in file comment';
                $phpcsFile->addError($error, $commentStart + $newlineCount + 1);
            }
            $newlineCount += $newlineBetween;

            // Exactly one blank line before tags.
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in file comment';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, "\n") - $newlineSpan + 1);
                }
                $phpcsFile->addError($error, $commentStart + $newlineCount);
            }

            // Check for unknown/deprecated tags.
            $unknownTags = $this->_fp->getUnknown();
            foreach ($unknownTags as $errorTag) {
                $error = ucfirst($errorTag['tag']).' tag is not allowed in file comment';
                $phpcsFile->addWarning($error, $commentStart + $errorTag['line']);
            }

            // Check the PHP Version.
            if (strstr(strtolower($long), 'php version') === false) {
                $error = 'PHP version not specified';
                $phpcsFile->addWarning($error, $commentEnd);
            }

            // Check each tag.
            $this->processTags($commentStart, $commentEnd);
        }

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

        $docBlock    = (get_class($this) === 'PEAR_Sniffs_Commenting_FileCommentSniff') ? 'file' : 'class';
        $foundTags   = $fp->getTagOrders();
        $orderIndex  = 0;
        $indentation = array();
        $longestTag  = 0;
        $errorPos    = 0;

        foreach ($tags as $tag => $info) {

            // Required tag missing.
            if (($info['required'] === true) && !in_array($tag, $foundTags)) {
                $error = "Missing $tag tag in $docBlock comment";
                $this->_phpcsFile->addError($error, $commentEnd);
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
                    $error = "Only one $tag tag is allowed in $docBlock comment";
                    $this->_phpcsFile->addError($error, $errorPos);
                } else {
                    // Make sure same tags are grouped together.
                    $i     = 0;
                    $count = $foundIndexes[0];
                    foreach ($foundIndexes as $index) {
                        if ($index != $count) {
                            $errorPosIndex = $errorPos + $tagElement[$i]->getLine();
                            $error         = ucfirst($tag).' tags must be grouped together';
                            $this->_phpcsFile->addError($error, $errorPosIndex);
                        }
                        $i++;
                        $count++;
                    }
                }
            }

            // Check tag order.
            if ($foundIndexes[0] > $orderIndex) {
                $orderIndex = $foundIndexes[0];
            } else {
                if (is_array($tagElement) && !empty($tagElement)) {
                    $errorPos += $tagElement[0]->getLine();
                }
                $error = "The order of $tag tag is wrong in $docBlock comment";
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
                // Process each tag if a method is defined.
                call_user_func(array($this, $method), $errorPos);
            } else {
                if (is_array($tagElement)) {
                    foreach ($tagElement as $key => $element) {
                        $element->process($this->_phpcsFile, $commentStart, $docBlock);
                    }
                } else {
                     $tagElement->process($this->_phpcsFile, $commentStart, $docBlock);
                }
            }

        }

        foreach ($indentation as $indentInfo) {
            if ($indentInfo['space'] !== 0 && $indentInfo['space'] !== $longestTag+1) {
                $expected      = ($longestTag - strlen($indentInfo['tag'])) + 1;
                $space         = $indentInfo['space'] - strlen($indentInfo['tag']);
                $error         = ucfirst($indentInfo['tag']).' tag comment indented incorrectly. ';
                $error        .= "Expected $expected spaces but found $space.";
                $getTagMethod  = 'get'.ucfirst($indentInfo['tag']);
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
                    $nameBits = explode('_', $content);
                    $firstBit = array_shift($nameBits);
                    $newName  = ucfirst($firstBit).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= ucfirst($bit).'_';
                    }
                    $validName = trim($newName, '_');
                    $error     = "Category name \"$content\" is not valid; Consider \"$validName\" instead.";
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
                    $nameBits = explode('_', $content);
                    $firstBit = array_shift($nameBits);
                    $newName  = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }
                    $validName = trim($newName, '_');
                    $error     = "Package name \"$content\" is not valid; Consider \"$validName\" instead.";
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
                    if (preg_match('/^([^<]*)\s+<(['.$local.']['.$local_middle.']*['.$local.']@[\da-zA-Z][-.\w]*[\da-zA-Z]\.[a-zA-Z]{2,7})>$/', $content) === 0) {
                        $error = 'Content of the author tag must be in the form "Display Name <username@example.com>"';
                        $this->_phpcsFile->addError($error, $errorPos);
                    }
                } else {
                    $docBlock = (get_class($this) === 'PEAR_Sniffs_Commenting_FileCommentSniff') ? 'file' : 'class';
                    $error    = "Content missing for author tag in $docBlock comment";
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
                            $error = "Invalid year span \"$matches[1]$matches[3]$matches[4]\" found; Consider \"$matches[4]-$matches[1]\" instead.";
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
                $error = 'Content missing for version tag in file comment';
                $this->_phpcsFile->addError($error, $errorPos);

            } else if ((strstr($content, 'CVS:')) === false) {
                $error = "Invalid version \"$content\" in file comment; Consider \"CVS: <cvs_id>\" instead.";
                $this->_phpcsFile->addWarning($error, $errorPos);
            }
        }

    }//end _processVersion()

}//end class

?>
