<?php
/**
 * Parses and verifies the doc comments for classes.
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
require_once 'PHP/CodeSniffer/Standards/PEAR/Sniffs/Commenting/FileCommentSniff.php';

/**
 * Parses and verifies the doc comments for classes.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
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

class PEAR_Sniffs_Commenting_ClassCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);

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
        $tokens = $this->_phpcsFile->getTokens();
        $find   = array (
                   T_ABSTRACT,
                   T_WHITESPACE,
                  );

        // Extract the class comment docblock.
        $commentEnd = $phpcsFile->findPrevious($find, $stackPtr - 1, null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $this->_phpcsFile->addError('Consider using "/**" style comment for class comment', $stackPtr);
            return;
        } else if ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
            $this->_phpcsFile->addError('Missing class doc comment', $stackPtr);
            return;
        }
        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT, $commentEnd - 1, null, true) + 1;

        if ($commentStart !== false && $tokens[$commentStart]['code'] === T_COMMENT) {
            $this->_phpcsFile->addError('Consider using "/**" instead of "//" for class doc comment', $stackPtr);
            return;
        } else if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT) {
            $this->_phpcsFile->addError('Missing class doc comment', $stackPtr);
            return;
        } else {

            $comment = $this->_phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);

            // Parse the class comment.docblock.
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
                $error = "Extra $line found before class comment short description";
                $phpcsFile->addError($error, $commentStart + 1);
            }
            $newlineCount = substr_count($short, "\n") + 1;

            // Exactly one blank line between short and long description.
            $between        = $comment->getWhiteSpaceBetween();
            $long           = $comment->getLongComment();
            $newlineBetween = substr_count($between, "\n");
            if ($newlineBetween !== 2 && $long !== '') {
                $error = 'There must be exactly one blank line between descriptions in class comment';
                $phpcsFile->addError($error, $commentStart + $newlineCount + 1);
            }
            $newlineCount += $newlineBetween;

            // Exactly one blank line before tags.
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in class comment';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, "\n") - $newlineSpan + 1);
                }
                $phpcsFile->addError($error, $commentStart + $newlineCount);
            }

            // Check each tag.
            $this->processTags($commentStart, $commentEnd);
        }


    }//end process()


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
                $error = 'Content missing for version tag in class comment';
                $this->_phpcsFile->addError($error, $errorPos);
            } else if ((strstr($content, 'Release:') === false)) {
                $error = "Invalid version \"$content\" in class comment; Consider \"Release: @package_version@\" instead.";
                $this->_phpcsFile->addWarning($error, $errorPos);
            }
        }

    }//end _processVersion()


}//end class

?>
