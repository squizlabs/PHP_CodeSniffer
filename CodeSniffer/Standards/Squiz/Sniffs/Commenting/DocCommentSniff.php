<?php
/**
 * Ensures doc blocks follow basic formatting.
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
 * Ensures doc blocks follow basic formatting.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Commenting_DocCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_DOC_COMMENT_OPEN_TAG);

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
        $tokens       = $phpcsFile->getTokens();
        $commentEnd   = $phpcsFile->findNext(T_DOC_COMMENT_CLOSE_TAG, ($stackPtr + 1));
        $commentStart = $tokens[$commentEnd]['comment_opener'];

        $empty = array(
                  T_DOC_COMMENT_WHITESPACE,
                  T_DOC_COMMENT_STAR,
                 );

        $short = $phpcsFile->findNext($empty, ($stackPtr + 1), $commentEnd, true);
        if ($short === false) {
            // No content at all.
            $error = 'Doc comment is empty';
            $phpcsFile->addError($error, $stackPtr, 'Empty');
            return;
        }

        // The first line of the comment should just be the /** code.
        if ($tokens[$short]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'The open comment tag must be the only content on the line';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ContentAfterOpen');
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->addNewline($stackPtr);
                $phpcsFile->fixer->addContentBefore($short, '* ');
            }
        }

        // Check for additional blank lines at the end of the comment.
        $prev = $phpcsFile->findPrevious($empty, ($commentEnd - 1), $stackPtr, true);
        if ($tokens[$prev]['line'] !== ($tokens[$commentEnd]['line'] - 1)) {
            $error = 'Additional blank lines found at end of doc comment';
            $phpcsFile->addError($error, $commentEnd, 'SpacingAfter');
        }

        // Check for a comment description.
        if ($tokens[$short]['code'] !== T_DOC_COMMENT_STRING) {
            $error = 'Missing short description in doc comment';
            $phpcsFile->addError($error, $stackPtr, 'MissingShort');
            return;
        }

        // No extra newline before short description.
        if ($tokens[$short]['line'] !== ($tokens[$stackPtr]['line'] + 1)) {
            $error = 'Doc comment short description must be on the first line';
            $phpcsFile->addError($error, $short, 'SpacingBeforeShort');
        }

        // Short description must be single line and end with a full stop.
        if (preg_match('|\p{Lu}|u', $tokens[$short]['content'][0]) === 0) {
            $error = 'Doc comment short description must start with a capital letter';
            $phpcsFile->addError($error, $short, 'ShortNotCapital');
        }

        if (substr($tokens[$short]['content'], -1) !== '.') {
            $error = 'Doc comment short description must end with a full stop';
            $fix   = $phpcsFile->addError($error, $short, 'ShortFullStop');
        }

        $long = $phpcsFile->findNext($empty, ($short + 1), ($commentEnd - 1), true);
        if ($long === false) {
            return;
        }

        if ($tokens[$long]['code'] === T_DOC_COMMENT_STRING) {
            if ($tokens[$long]['line'] !== ($tokens[$short]['line'] + 2)) {
                $error = 'There must be exactly one blank line between descriptions in a doc comment';
                $phpcsFile->addError($error, $long, 'SpacingBetween');
            }

            if (preg_match('|\p{Lu}|u', $tokens[$long]['content'][0]) === 0) {
                $error = 'Doc comment long description must start with a capital letter';
                $phpcsFile->addError($error, $long, 'LongNotCapital');
            }
        }

        if (empty($tokens[$commentStart]['comment_tags']) === true) {
            // No tags in the comment.
            return;
        }

        $firstTag = $tokens[$commentStart]['comment_tags'][0];
        $prev     = $phpcsFile->findPrevious($empty, ($firstTag - 1), $stackPtr, true);
        if ($tokens[$firstTag]['line'] !== ($tokens[$prev]['line'] + 2)) {
            $error = 'There must be exactly one blank line before the tags in a doc comment';
            $phpcsFile->addError($error, $firstTag, 'SpacingBeforeTags');
        }

        // Check the alignment of tag values.
        $maxLength = 0;
        $paddings  = array();
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            // Param tags are indented differently.
            if ($tokens[$tag]['content'] !== '@param') {
                $tagLength = strlen($tokens[$tag]['content']);
                if ($tagLength > $maxLength) {
                    $maxLength = $tagLength;
                }
            }

            // Check for a value. No value means no padding needed.
            $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
            if ($string !== false && $tokens[$string]['line'] === $tokens[$tag]['line']) {
                $paddings[$tag] = strlen($tokens[($tag + 1)]['content']);
            }
        }

        foreach ($paddings as $tag => $padding) {
            if ($tokens[$tag]['content'] === '@param') {
                $required = 1;
            } else {
                $required = ($maxLength - strlen($tokens[$tag]['content']) + 1);
            }

            if ($padding !== $required) {
                $error = 'Tag value indented incorrectly; expected %s spaces but found %s';
                $data  = array(
                          $required,
                          $padding,
                         );

                $fix = $phpcsFile->addFixableError($error, ($tag + 1), 'TagValueIndent', $data);
                if ($fix === true && $phpcsFile->fixer->enabled === true) {
                    $phpcsFile->fixer->replaceToken(($tag + 1), str_repeat(' ', $required));
                }
            }
        }

        $firstParam = null;
        $lastParam  = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $pos => $tag) {
            if ($tokens[$tag]['content'] === '@param') {
                if ($lastParam !== null) {
                    $error = 'Paramater tags must be grouped together in a doc commment';
                    $phpcsFile->addError($error, $tag, 'ParamGroup');
                }

                if ($firstParam === null) {
                    if ($pos !== 0) {
                        $error = 'Paramater tags must be defined first in a doc commment';
                        $phpcsFile->addError($error, $tag, 'ParamNotFirst');
                    }

                    $firstParam = $tag;
                }
            } else if ($firstParam !== null) {
                $lastParam = $tokens[$commentStart]['comment_tags'][($pos - 1)];

                // Check that there was a blank line after the param block
                // but account for a multi-line param.
                $prev = $phpcsFile->findPrevious(T_DOC_COMMENT_STRING, $tag, $lastParam);
                if ($prev === false) {
                    $prev = $lastParam;
                }

                if ($tokens[$prev]['line'] !== ($tokens[$tag]['line'] - 2)) {
                    $error = 'There must be a single blank line after the paramater tags';
                    $phpcsFile->addError($error, $lastParam, 'ParamNotFirst');
                }
            }
        }//end foreach

        // also check that if params, they come first and have blank line between them and next tags and are grouped together.

    }//end process()


}//end class
