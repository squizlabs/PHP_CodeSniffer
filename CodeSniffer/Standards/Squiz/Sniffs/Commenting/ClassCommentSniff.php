<?php
/**
 * Parses and verifies the class doc comment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Parses and verifies the class doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A class doc comment exists.</li>
 *  <li>There is exactly one blank line before the class comment.</li>
 *  <li>There are no blank lines after the class comment.</li>
 *  <li>Short and long descriptions end with a full stop and start with capital letter.</li>
 *  <li>There is a blank line between descriptions.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
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
        $find   = array(
                   T_ABSTRACT,
                   T_WHITESPACE,
                   T_FINAL,
                  );

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
            return;
        }

        // Try and determine if this is a file comment instead of a class comment.
        // We assume that if this is the first comment after the open PHP tag, then
        // it is most likely a file comment instead of a class comment.
        $prev = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($commentEnd - 1), null, true);
        if ($tokens[$prev]['code'] === T_OPEN_TAG) {
            $prevOpen = $phpcsFile->findPrevious(T_OPEN_TAG, ($prev - 1));
            if ($prevOpen === false) {
                // This is a comment directly after the first open tag,
                // so probably a file comment.
                $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
                return;
            }
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr, 'WrongStyle');
            return;
        }

        if ($tokens[$commentEnd]['line'] !== ($tokens[$stackPtr]['line'] - 1)) {
            $error = 'There must be no blank lines after the class comment';
            $phpcsFile->addError($error, $commentEnd, 'SpacingAfter');
        }

        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, ($commentEnd - 1));
        if ($tokens[$prev]['line'] !== ($tokens[$commentStart]['line'] - 2)) {
            $error = 'There must be exactly one blank line before the class comment';
            $phpcsFile->addError($error, $commentStart, 'SpacingBefore');
        }

        $empty = array(
                  T_DOC_COMMENT_WHITESPACE,
                  T_DOC_COMMENT_STAR,
                 );

        $short = $phpcsFile->findNext($empty, ($commentStart + 1), $commentEnd, true);
        if ($short === false) {
            // No content at all.
            $error = 'Class doc comment is empty';
            $phpcsFile->addError($error, $commentStart, 'Empty');
            return;
        }

        // The first line of the comment should just be the /** code.
        if ($tokens[$short]['line'] === $tokens[$commentStart]['line']) {
            $error = 'The open comment tag must be the only content on the line';
            $phpcsFile->addError($error, $commentStart, 'SpacingAfterOpen');
        }

        // Check for additional blank lines at the end of the comment.
        $prev = $phpcsFile->findPrevious($empty, ($commentEnd - 1), $commentStart, true);
        if ($tokens[$prev]['line'] !== ($tokens[$commentEnd]['line'] - 1)) {
            $error = 'Additional blank lines found at end of class comment';
            $this->currentFile->addError($error, $commentEnd, 'SpacingAfter');
        }

        // Check for a comment description.
        if ($tokens[$short]['code'] !== T_DOC_COMMENT_STRING) {
            $error = 'Missing short description in class doc comment';
            $phpcsFile->addError($error, $commentStart, 'MissingShort');
            return;
        }

        // No extra newline before short description.
        if ($tokens[$short]['line'] !== ($tokens[$commentStart]['line'] + 1)) {
            $error = 'Comment short description must be on the first line in a class comment';
            $phpcsFile->addError($error, $short, 'SpacingBeforeShort');
        }

        // Short description must be single line and end with a full stop.
        if (preg_match('|\p{Lu}|u', $tokens[$short]['content'][0]) === 0) {
            $error = 'Class comment short description must start with a capital letter';
            $phpcsFile->addError($error, $short, 'ShortNotCapital');
        }

        if (substr($tokens[$short]['content'], -1) !== '.') {
            $error = 'Class comment short description must end with a full stop';
            $phpcsFile->addError($error, $short, 'ShortFullStop');
        }

        $long = $phpcsFile->findNext($empty, ($short + 1), ($commentEnd - 1), true);
        if ($long === false) {
            return;
        }

        if ($tokens[$long]['code'] === T_DOC_COMMENT_STRING) {
            if ($tokens[$long]['line'] !== ($tokens[$short]['line'] + 2)) {
                $error = 'There must be exactly one blank line between descriptions in a class comment';
                $phpcsFile->addError($error, $long, 'SpacingBetween');
            }

            if (preg_match('|\p{Lu}|u', $tokens[$long]['content'][0]) === 0) {
                $error = 'Class comment long description must start with a capital letter';
                $phpcsFile->addError($error, $long, 'LongNotCapital');
            }
        }

        for ($i = $long; $i < $commentEnd; $i++) {
            if ($tokens[$i]['code'] !== T_DOC_COMMENT_TAG) {
                continue;
            }

            $error = '%s tag is not allowed in class comment';
            $data  = array($tokens[$i]['content']);
            $phpcsFile->addWarning($error, $i, 'TagNotAllowed', $data);
        }

    }//end process()


}//end class
