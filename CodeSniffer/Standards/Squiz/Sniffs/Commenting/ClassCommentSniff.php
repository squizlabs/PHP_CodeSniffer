<?php
/**
 * Parses and verifies the class doc comment.
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

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found');
}

/**
 * Parses and verifies the class doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A class doc comment exists.</li>
 *  <li>There is exactly one blank line before the class comment.</li>
 *  <li>Short description ends with a full stop.</li>
 *  <li>There is a blank line after the short description.</li>
 *  <li>Each paragraph of the long description ends with a full stop.</li>
 *  <li>There is a blank line between the description and the tags.</li>
 *  <li>Check the format of the since tag (x.x.x).</li>
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
class Squiz_Sniffs_Commenting_ClassCommentSniff implements PHP_CodeSniffer_Sniff
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
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $find   = array (
                   T_ABSTRACT,
                   T_WHITESPACE,
                   T_FINAL,
                  );

        // Extract the class comment docblock.
        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr, 'WrongStyle');
            return;
        } else if ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
            $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
            return;
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $commentNext  = $phpcsFile->findPrevious(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);

        // Distinguish file and class comment.
        $prevClassToken = $phpcsFile->findPrevious(T_CLASS, ($stackPtr - 1));
        if ($prevClassToken === false) {
            // This is the first class token in this file, need extra checks.
            $prevNonComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($commentStart - 1), null, true);
            if ($prevNonComment !== false) {
                $prevComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($prevNonComment - 1));
                if ($prevComment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);
                    if ($newlineToken !== false) {
                        $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($newlineToken + 1), $stackPtr, false, $phpcsFile->eolChar);
                        if ($newlineToken !== false) {
                            // Blank line between the class and the doc block.
                            // The doc block is most likely a file comment.
                            $phpcsFile->addError('Missing class doc comment', ($stackPtr + 1), 'Missing');
                            return;
                        }
                    }//end if
                }//end if

                // Exactly one blank line before the class comment.
                $prevTokenEnd = $phpcsFile->findPrevious(T_WHITESPACE, ($commentStart - 1), null, true);
                if ($prevTokenEnd !== false) {
                    $blankLineBefore = 0;
                    for ($i = ($prevTokenEnd + 1); $i < $commentStart; $i++) {
                        if ($tokens[$i]['code'] === T_WHITESPACE && $tokens[$i]['content'] === $phpcsFile->eolChar) {
                            $blankLineBefore++;
                        }
                    }

                    if ($blankLineBefore !== 2) {
                        $error = 'There must be exactly one blank line before the class comment';
                        $phpcsFile->addError($error, ($commentStart - 1), 'SpacingBefore');
                    }
                }

            }//end if
        }//end if

        $commentString = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

        // Parse the class comment docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($commentString, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($e->getMessage(), $line, 'FailedParse');
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Class doc comment is empty';
            $phpcsFile->addError($error, $commentStart, 'Empty');
            return;
        }

        // The first line of the comment should just be the /** code.
        $eolPos    = strpos($commentString, $phpcsFile->eolChar);
        $firstLine = substr($commentString, 0, $eolPos);
        if ($firstLine !== '/**') {
            $error = 'The open comment tag must be the only content on the line';
            $phpcsFile->addError($error, $commentStart, 'SpacingAfterOpen');
        }

        // Check for a comment description.
        $short = rtrim($comment->getShortComment(), $phpcsFile->eolChar);
        if (trim($short) === '') {
            $error = 'Missing short description in class doc comment';
            $phpcsFile->addError($error, $commentStart, 'MissingShort');
            return;
        }

        // No extra newline before short description.
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $error = 'Extra newline(s) found before class comment short description';
            $phpcsFile->addError($error, ($commentStart + 1), 'SpacingBeforeShort');
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in class comment';
                $phpcsFile->addError($error, ($commentStart + $newlineCount + 1), 'SpacingBetween');
            }

            $newlineCount += $newlineBetween;

            $testLong = trim($long);
            if (preg_match('|[A-Z]|', $testLong[0]) === 0) {
                $error = 'Class comment long description must start with a capital letter';
                $phpcsFile->addError($error, ($commentStart + $newlineCount), 'LongNotCaptial');
            }
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in class comment';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                $phpcsFile->addError($error, ($commentStart + $newlineCount), 'SpacingBeforeTags');
                $short = rtrim($short, $phpcsFile->eolChar.' ');
            }
        }

        // Short description must be single line and end with a full stop.
        $testShort = trim($short);
        $lastChar  = $testShort[(strlen($testShort) - 1)];
        if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
            $error = 'Class comment short description must be on a single line';
            $phpcsFile->addError($error, ($commentStart + 1), 'ShortSingleLine');
        }

        if (preg_match('|[A-Z]|', $testShort[0]) === 0) {
            $error = 'Class comment short description must start with a capital letter';
            $phpcsFile->addError($error, ($commentStart + 1), 'ShortNotCapital');
        }

        if ($lastChar !== '.') {
            $error = 'Class comment short description must end with a full stop';
            $phpcsFile->addError($error, ($commentStart + 1), 'ShortFullStop');
        }

        // Check for unknown/deprecated tags.
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $error = '@%s tag is not allowed in class comment';
            $data  = array($errorTag['tag']);
            $phpcsFile->addWarning($error, ($commentStart + $errorTag['line']), 'TagNotAllowed', $data);
            return;
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
        $foundTags = $this->commentParser->getTagOrders();

        // Other tags found.
        foreach ($foundTags as $tagName) {
            if ($tagName !== 'comment' && $tagName !== 'since') {
                $error = 'Only @since tag is allowed in class comment';
                $this->currentFile->addWarning($error, $commentEnd, 'NotSince');
                break;
            }
        }

        // Since tag missing.
        if (in_array('since', $foundTags) === false) {
            $error = 'Missing @since tag in class comment';
            $this->currentFile->addError($error, $commentEnd, 'MissingSince');
            return;
        }

        // Get the line number for current tag.
        $since = $this->commentParser->getSince();
        if (is_null($since) === true || empty($since) === true) {
            return;
        }

        $errorPos = ($commentStart + $since->getLine());

        // Make sure there is no duplicate tag.
        $foundIndexes = array_keys($foundTags, 'since');
        if (count($foundIndexes) > 1) {
            $error = 'Only 1 @since tag is allowed in class comment';
            $this->currentFile->addError($error, $errorPos, 'MultipleSince');
        }

        // Check spacing.
        if ($since->getContent() !== '') {
            $spacing = substr_count($since->getWhitespaceBeforeContent(), ' ');
            if ($spacing !== 1) {
                $error = 'Expected 1 space but found %s before version number in @since tag';
                $data  = array($spacing);
                $this->currentFile->addError($error, $errorPos, 'SinceSpacing', $data);
            }
        }

        // Check content.
        $this->processSince($errorPos);

    }//end processTags()


    /**
     * Processes the since tag.
     *
     * The since tag must have the exact keyword 'release_version'
     * or is in the form x.x.x
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processSince($errorPos)
    {
        $since = $this->commentParser->getSince();
        if ($since !== null) {
            $content = $since->getContent();
            if (empty($content) === true) {
                $error = 'Content missing for @since tag in class comment';
                $this->currentFile->addError($error, $errorPos, 'EmptySince');

            } else if ($content !== '%release_version%') {
                if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $content) === 0) {
                    $error = 'Expected version number to be in the form x.x.x in @since tag';
                    $this->currentFile->addError($error, $errorPos, 'SinceVersionWrong');
                }
            }
        }

    }//end processSince()


}//end class
?>
