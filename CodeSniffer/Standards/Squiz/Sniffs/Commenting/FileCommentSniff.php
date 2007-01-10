<?php
/**
 * Parses and verifies the file doc comment.
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
 * Parses and verifies the file doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A file doc comment exists.</li>
 *  <li>Short description ends with a full stop.</li>
 *  <li>There is a blank line after the short description.</li>
 *  <li>Each paragraph of the long description ends with a full stop.</li>
 *  <li>There is a blank line between the description and the tags.</li>
 *  <li>Check the order, indentation and content of each tag.</li>
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

class Squiz_Sniffs_Commenting_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The header comment parser for the current file.
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser
     */
    protected $commentParser = null;

    /**
    * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentFile = null;


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
        $this->currentFile = $phpcsFile;

        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        // Ignore vim header.
        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            if (strstr($tokens[$commentStart]['content'], 'vim:') !== false) {
                $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($commentStart + 1), null, true);
            }
        }

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a file comment', $stackPtr + 1);
            return;
        } else if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT) {
            $phpcsFile->addError('Missing file doc comment', ($stackPtr + 1));
            return;
        } else {

            // Extract the header comment docblock.
            $commentEnd = ($phpcsFile->findNext(T_DOC_COMMENT, ($commentStart + 1), null, true) - 1);
            $comment    = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

            // Parse the header comment docblock.
            try {
                $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment);
                $this->commentParser->parse();
            } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
                $line = $e->getLineWithinComment() + $commentStart;
                $phpcsFile->addError($e->getMessage(), $line);
                return;
            }

            // No extra newline before short description.
            $comment      = $this->commentParser->getComment();
            $short        = $comment->getShortComment();
            $newlineCount = 0;
            $newlineSpan  = strspn($short, "\n");
            if ($short !== '' && $newlineSpan > 0) {
                $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
                $error = "Extra $line found before file comment short description";
                $phpcsFile->addError($error, ($commentStart + 1));
            }
            $newlineCount = (substr_count($short, "\n") + 1);

            // Exactly one blank line between short and long description.
            $long = $comment->getLongComment();
            if (!empty($long)) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, "\n");
                if ($newlineBetween !== 2) {
                    $error = 'There must be exactly one blank line between descriptions in file comment';
                    $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
                }
                $newlineCount += $newlineBetween;
            }

            // Exactly one blank line before tags.
            $tags = $this->commentParser->getTagOrders();
            if (count($tags) > 1) {
                $newlineSpan = $comment->getNewlineAfter();
                if ($newlineSpan !== 2) {
                    $error = 'There must be exactly one blank line before the tags in file comment';
                    if ($long !== '') {
                        $newlineCount += (substr_count($long, "\n") - $newlineSpan + 1);
                    }
                    $phpcsFile->addError($error, $commentStart + $newlineCount);
                    $short = rtrim($short, "\n ");
                }
            }

            // Short description must be single line and end with a full stop.
            $lastChar = $short[strlen($short)-1];
            if (substr_count($short, "\n") !== 0) {
                $error = "File comment short description must be on a single line";
                $phpcsFile->addError($error, $commentStart + 1);
            }
            if ($lastChar !== '.') {
                $error = "File comment short description must end with a full stop";
                $phpcsFile->addError($error, $commentStart + 1);
            }

            // Check for unknown/deprecated tags.
            $unknownTags = $this->commentParser->getUnknown();
            foreach ($unknownTags as $errorTag) {
                // Unknown tags are not parsed, do not process further.
                $error = ucfirst($errorTag['tag']).' tag is not allowed in file comment';
                $phpcsFile->addWarning($error, $commentStart + $errorTag['line']);
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
        // Required tags in correct order.
        $tags = array(
                 'version',
                 'package',
                 'subpackage',
                 'author',
                 'copyright',
                 'license',
                );

        $foundTags   = $this->commentParser->getTagOrders();
        $errorPos    = 0;
        $orderIndex  = 0;
        $longestTag  = 0;
        $indentation = array();
        foreach ($tags as $tag) {

            // Required tag missing.
            if (!in_array($tag, $foundTags)) {
                $error = "Missing required $tag tag in file comment";
                $this->currentFile->addError($error, $commentEnd);
                continue;
            }

             // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if ($tagName === 'Author') {
                // Author tag is different because it returns an array
                $tagName .= 's';
            }

            // Work out the line number for this tag.
            $getMethod  = 'get'.$tagName;
            $tagElement = $this->commentParser->$getMethod();
            if (is_null($tagElement) || empty($tagElement)) {
                continue;
            } else if (is_array($tagElement) && !empty($tagElement)) {
                $tagElement = $tagElement[0];
            }
            $errorPos = ($commentStart + $tagElement->getLine());

            // Make sure there is no duplicate tag.
            $foundIndexes = array_keys($foundTags, $tag);
            if (count($foundIndexes) > 1) {
                $error = "Only 1 $tag tag is allowed in file comment";
                $this->currentFile->addError($error, $errorPos);
            }

            // Check tag order.
            if ($foundIndexes[0] > $orderIndex) {
                $orderIndex = $foundIndexes[0];
            } else {
                $error = "The order of $tag tag is wrong in file comment";
                $this->currentFile->addError($error, $errorPos);
            }

            // Store the indentation of each tag.
            $len = strlen($tag);
            if ($len > $longestTag) $longestTag = $len;
            $indentation[] = array(
                              'tag'      => $tag,
                              'errorPos' => $errorPos,
                              'space'    => $this->_getIndentation($tag, $tagElement),
                             );

            $method = '_process'.$tagName;
            if (method_exists($this, $method)) {
                // Process each tag if a method is defined.
                call_user_func(array($this, $method), $errorPos);
            } else {
                $tagElement->process($this->currentFile, $commentStart, 'file');
            }

        }

        // Check tag indentation.
        foreach ($indentation as $indentInfo) {
            $tagName = ucfirst($indentInfo['tag']);
            if ($tagName === 'Author') {
                $tagName .= 's';
            }
            if ($indentInfo['space'] !== 0 && $indentInfo['space'] !== $longestTag+1) {
                $expected  = ($longestTag - strlen($indentInfo['tag'])) + 1;
                $space     = $indentInfo['space'] - strlen($indentInfo['tag']);
                $error     = ucfirst($indentInfo['tag']).' tag comment indented incorrectly. ';
                $error    .= "Expected $expected spaces but found $space.";
                $this->currentFile->addError($error, $indentInfo['errorPos']);
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
     * The version tag must have the exact keyword 'release_version'
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processVersion($errorPos)
    {
        $version = $this->commentParser->getVersion();
        if ($version !== null) {
            $content = $version->getContent();
            if (empty($content)) {
                $error = 'Content missing for version tag in file comment';
                $this->currentFile->addError($error, $errorPos);

            } else if ($content !== '%release_version%') {
                if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $content) === 0) {
                    // Separate keyword so it does not get replaced when we commit.
                    $error = 'Expected keyword "%'.'release version%" for version number';
                    $this->currentFile->addError($error, $errorPos);
                }
            }
        }

    }//end _processVersion()


    /**
     * The package name must be 'MySource4'
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processPackage($errorPos)
    {
        $package = $this->commentParser->getPackage();
        if ($package !== null) {
            $content = $package->getContent();
            if (empty($content)) {
                $error = 'Content missing for package tag in file comment';
                $this->currentFile->addError($error, $errorPos);

            } else if ($content !== 'MySource4') {
                $error = "Expected \"MySource4\" for package name";
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end _processPackage()


    /**
     * The subpackage name must be camel-cased
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processSubpackage($errorPos)
    {
        $subpackage = $this->commentParser->getSubpackage();
        if ($subpackage !== null) {
            $content = $subpackage->getContent();
            if (empty($content)) {
                $error = 'Content missing for subpackage tag in file comment';
                $this->currentFile->addError($error, $errorPos);

            } else if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                // Subpackage name must be properly camel-cased.
                $nameBits = explode('_', $content);
                $firstBit = array_shift($nameBits);
                $newName  = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                foreach ($nameBits as $bit) {
                    $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                }
                $validName  = trim($newName, '_');
                $error      = "Subpackage name \"$content\" is not valid; ";
                $error     .= "Consider \"$validName\" instead.";
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end _processSubpackage()


    /**
    * Author tag must be 'Squiz Pty Ltd <mysource4@squiz.net>'
    *
    * @param int $errorPos The line number where the error occurs.
    *
    * @return void
    */
    private function _processAuthors($errorPos)
    {
        $authors = $this->commentParser->getAuthors();
        if (!empty($authors)) {
            $author  = $authors[0];
            $content = $author->getContent();
            if (empty($content)) {
                $error = 'Content missing for author tag in file comment';
                $this->currentFile->addError($error, $errorPos);

            } else if ($content !== 'Squiz Pty Ltd <mysource4@squiz.net>') {
                $error = "Expected \"Squiz Pty Ltd <mysource4@squiz.net>\" for author tag";
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end _processAuthor()


    /**
     * Copyright tag must be in the form '2006-YYYY Squiz Pty Ltd (ABN 77 084 670 600)'
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processCopyright($errorPos)
    {
        $copyright = $this->commentParser->getCopyRight();
        if ($copyright !== null) {
            $content = $copyright->getContent();
            if (empty($content)) {
                $error = 'Content missing for copyright tag in file comment';
                $this->currentFile->addError($error, $errorPos);

            } else if (preg_match('/^([0-9]{4})-([0-9]{4})? (Squiz Pty Ltd \(ABN 77 084 670 600\))$/', $content) === 0) {
                $error = "Expected \"2006-2007 Squiz Pty Ltd (ABN 77 084 670 600)\" for copyright declaration";
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end _processCopyright()


    /**
     * License tag must be 'http://matrix.squiz.net/evaluations/licence Squiz.Net Open Source Licence'
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    private function _processLicense($errorPos)
    {
        $license = $this->commentParser->getLicense();
        if ($license !== null) {
            $url     = $license->getValue();
            $content = $license->getComment();
            if (empty($url) && empty($content)) {
                $error = 'Content missing for license tag in file comment';
                $this->currentFile->addError($error, $errorPos);

            } else {
                // Check for license URL
                if (empty($url)) {
                    $error = 'License URL missing for license tag in file comment';
                    $this->currentFile->addError($error, $errorPos);
                } else if ($url !== 'http://matrix.squiz.net/evaluations/licence') {
                    $error = "Expected \"http://matrix.squiz.net/evaluations/licence\" for license URL";
                    $this->currentFile->addError($error, $errorPos);
                }

                // Check for license name.
                if (empty($content)) {
                    $error = 'License name missing for license tag in file comment';
                    $this->currentFile->addError($error, $errorPos);
                } else if ($content !== 'Squiz.Net Open Source Licence') {
                    $error = "Expected \"Squiz.Net Open Source Licence\" for license name";
                    $this->currentFile->addError($error, $errorPos);
                }
            }
        }//end if

    }//end _processLicense()


}//end class


?>