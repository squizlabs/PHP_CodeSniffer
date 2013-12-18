<?php
/**
 * Parses and verifies the variable doc comment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

if (class_exists('PHP_CodeSniffer_CommentParser_MemberCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_MemberCommentParser not found');
}

/**
 * Parses and verifies the variable doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A variable doc comment exists.</li>
 *  <li>Short description ends with a full stop.</li>
 *  <li>There is a blank line after the short description.</li>
 *  <li>There is a blank line between the description and the tags.</li>
 *  <li>Check the order, indentation and content of each tag.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class Squiz_Sniffs_Commenting_VariableCommentSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{

    /**
     * Called to process class member vars.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens       = $phpcsFile->getTokens();
        $commentToken = array(
                         T_COMMENT,
                         T_DOC_COMMENT_CLOSE_TAG,
                        );

        $commentEnd = $phpcsFile->findPrevious($commentToken, $stackPtr);
        if ($commentEnd === false) {
            $phpcsFile->addError('Missing variable doc comment', $stackPtr, 'Missing');
            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a variable comment', $stackPtr, 'WrongStyle');
            return;
        } else if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            $phpcsFile->addError('Missing variable doc comment', $stackPtr, 'Missing');
            return;
        } else {
            // Make sure the comment we have found belongs to us.
            $commentFor = $phpcsFile->findNext(array(T_VARIABLE, T_CLASS, T_INTERFACE), ($commentEnd + 1));
            if ($commentFor !== $stackPtr) {
                $phpcsFile->addError('Missing variable doc comment', $stackPtr, 'Missing');
                return;
            }
        }

        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, ($commentEnd - 1));

        $empty = array(
                  T_DOC_COMMENT_WHITESPACE,
                  T_DOC_COMMENT_STAR,
                 );

        $short = $phpcsFile->findNext($empty, ($commentStart + 1), $commentEnd, true);
        if ($short === false) {
            // No content at all.
            $error = 'Variable doc comment is empty';
            $phpcsFile->addError($error, $commentStart, 'Empty');
            return;
        }

        // The first line of the comment should just be the /** code.
        if ($tokens[$short]['line'] === $tokens[$commentStart]['line']) {
            $error = 'The open comment tag must be the only content on the line';
            $phpcsFile->addError($error, $commentStart, 'ContentAfterOpen');
        }

        // Check for additional blank lines at the end of the comment.
        $prev = $phpcsFile->findPrevious($empty, ($commentEnd - 1), $commentStart, true);
        if ($tokens[$prev]['line'] !== ($tokens[$commentEnd]['line'] - 1)) {
            $error = 'Additional blank lines found at end of variable comment';
            $this->currentFile->addError($error, $commentEnd, 'SpacingAfter');
        }

        // Check for a comment description.
        if ($tokens[$short]['code'] !== T_DOC_COMMENT_STRING) {
            $error = 'Missing short description in variable doc comment';
            $phpcsFile->addError($error, $commentStart, 'MissingShort');
            return;
        }

        // No extra newline before short description.
        if ($tokens[$short]['line'] !== ($tokens[$commentStart]['line'] + 1)) {
            $error = 'Comment short description must be on the first line in a variable comment';
            $phpcsFile->addError($error, $short, 'SpacingBeforeShort');
        }

        // Short description must be single line and end with a full stop.
        if (preg_match('|\p{Lu}|u', $tokens[$short]['content'][0]) === 0) {
            $error = 'Variable comment short description must start with a capital letter';
            $phpcsFile->addError($error, $short, 'ShortNotCapital');
        }

        if (substr($tokens[$short]['content'], -1) !== '.') {
            $error = 'Variable comment short description must end with a full stop';
            $phpcsFile->addError($error, $short, 'ShortFullStop');
        }

        $long     = $phpcsFile->findNext($empty, ($short + 1), ($commentEnd - 1), true);
        $foundVar = null;
        if ($long !== false) {
            if ($tokens[$long]['code'] === T_DOC_COMMENT_STRING) {
                if ($tokens[$long]['line'] !== ($tokens[$short]['line'] + 2)) {
                    $error = 'There must be exactly one blank line between descriptions in a variable comment';
                    $phpcsFile->addError($error, $long, 'SpacingBetween');
                }

                if (preg_match('|\p{Lu}|u', $tokens[$long]['content'][0]) === 0) {
                    $error = 'Variable comment long description must start with a capital letter';
                    $phpcsFile->addError($error, $long, 'LongNotCapital');
                }

                $firstTag = $phpcsFile->findNext(T_DOC_COMMENT_TAG, ($long + 1), ($commentEnd - 1)); 
            } else {
                // No long description.
                $firstTag = $long;
            }

            $prev = $phpcsFile->findPrevious($empty, ($firstTag - 1), $commentStart, true);
            if ($tokens[$firstTag]['line'] !== ($tokens[$prev]['line'] + 2)) {
                $error = 'There must be exactly one blank line before the tags in a variable comment';
                $phpcsFile->addError($error, $firstTag, 'SpacingBeforeTags');
            }

            for ($i = $long; $i < $commentEnd; $i++) {
                if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG) {
                    if ($tokens[$i]['content'] === '@var') {
                        if ($foundVar !== null) {
                            $error = 'Only one @var tag is allowed in a variable comment';
                            $phpcsFile->addError($error, $i, 'DuplicateVar');
                        } else {
                            $foundVar = $i;
                        }
                    } else if ($tokens[$i]['content'] === '@see') {
                        // Make sure the tag isn't empty and has the correct padding.
                        $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $i, $commentEnd);
                        if ($string === false || $tokens[$string]['line'] !== $tokens[$i]['line']) {
                            $error = 'Content missing for @see tag in variable comment';
                            $phpcsFile->addError($error, $i, 'EmptySees');
                        } else {
                            $spacing = strlen($tokens[($i + 1)]['content']);
                            if ($spacing !== 1) {
                                $error = '@see tag indented incorrectly; expected 1 space but found %s';
                                $data  = array($spacing);
                                $phpcsFile->addError($error, ($i + 1), 'SeesIndent', $data);
                            }
                        }
                    } else {
                        $error = '%s tag is not allowed in variable comment';
                        $data  = array($tokens[$i]['content']);
                        $phpcsFile->addWarning($error, $i, 'TagNotAllowed', $data);
                    }//end if
                }//end if
            }//end for

            if ($foundVar !== null && $tokens[$firstTag]['content'] !== '@var') {
                $error = 'The @var tag must be the first tag in a variable comment';
                $phpcsFile->addError($error, $foundVar, 'VarOrder');
            }
        }//end if

        // The @var tag is the only one we require.
        if ($foundVar === null) {
            $error = 'Missing @var tag in variable comment';
            $phpcsFile->addError($error, $commentEnd, 'MissingVar');
            return;
        }

        // Make sure the tag isn't empty and has the correct padding.
        $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $foundVar, $commentEnd);
        if ($string === false || $tokens[$string]['line'] !== $tokens[$foundVar]['line']) {
            $error = 'Content missing for @var tag in variable comment';
            $phpcsFile->addError($error, $foundVar, 'EmptyVar');
            return;
        }

        $spacing = strlen($tokens[($foundVar + 1)]['content']);
        if ($spacing !== 1) {
            $error = '@var tag indented incorrectly; expected 1 space but found %s';
            $data  = array($spacing);
            $phpcsFile->addError($error, ($foundVar + 1), 'SeesIndent', $data);
        }

        $varType       = $tokens[($foundVar + 2)]['content'];
        $suggestedType = PHP_CodeSniffer::suggestType($varType);
        if ($varType !== $suggestedType) {
            $error = 'Expected "%s" but found "%s" for @var tag in variable comment';
            $data  = array(
                      $suggestedType,
                      $varType,
                     );
            $phpcsFile->addError($error, ($foundVar + 2), 'IncorrectVarType', $data);
        }

    }//end processMemberVar()


    /**
     * Called to process a normal variable.
     *
     * Not required for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the double quoted
     *                                        string was found.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {

    }//end processVariable()


    /**
     * Called to process variables found in double quoted strings.
     *
     * Not required for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the double quoted
     *                                        string was found.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {

    }//end processVariableInString()


}//end class
