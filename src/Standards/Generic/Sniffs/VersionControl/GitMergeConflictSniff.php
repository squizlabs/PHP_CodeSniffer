<?php
/**
 * Check for merge conflict artefacts.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class GitMergeConflictSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $error  = 'Merge conflict boundary found; type: %s';

        $checkTokens = [
            T_SL                      => true,
            T_SR                      => true,
            T_IS_IDENTICAL            => true,
            T_COMMENT                 => true,
            T_DOC_COMMENT_STRING      => true,
            T_ENCAPSED_AND_WHITESPACE => true,
            T_INLINE_HTML             => true,
            T_HEREDOC                 => true,
            T_NOWDOC                  => true,
        ];

        for ($i = 0; $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['column'] !== 1 || isset($checkTokens[$tokens[$i]['code']]) === false) {
                continue;
            }

            switch ($tokens[$i]['code']) {
            // Check for first non-comment, non-heredoc/nowdoc, non-inline HTML merge conflict opener.
            case T_SL:
                if (isset($tokens[($i + 1)], $tokens[($i + 2)]) !== false
                    && $tokens[($i + 1)]['code'] === T_SL
                    && $tokens[($i + 2)]['code'] === T_STRING
                    && trim($tokens[($i + 2)]['content']) === '<<< HEAD'
                ) {
                    $phpcsFile->addError($error, $i, 'OpenerFound', ['opener']);
                    $i += 2;
                }
                break;

            // Check for merge conflict closer which was opened in a heredoc/nowdoc.
            case T_SR:
                if (isset($tokens[($i + 1)], $tokens[($i + 2)], $tokens[($i + 3)], $tokens[($i + 4)]) !== false
                    && $tokens[($i + 1)]['code'] === T_SR
                    && $tokens[($i + 2)]['code'] === T_SR
                    && $tokens[($i + 3)]['code'] === T_GREATER_THAN
                    && $tokens[($i + 4)]['code'] === T_WHITESPACE
                    && $tokens[($i + 4)]['content'] === ' '
                ) {
                    $phpcsFile->addError($error, $i, 'CloserFound', ['closer']);
                    $i += 4;
                }
                break;

            // - Check for delimiters and closers.
            // - Inspect heredoc/nowdoc content, comments and inline HTML.
            // - Check for subsequent merge conflict openers after the first broke the tokenizer.
            case T_ENCAPSED_AND_WHITESPACE:
            case T_COMMENT:
            case T_DOC_COMMENT_STRING:
            case T_INLINE_HTML:
            case T_HEREDOC:
            case T_NOWDOC:
                if (substr($tokens[$i]['content'], 0, 12) === '<<<<<<< HEAD') {
                    $phpcsFile->addError($error, $i, 'OpenerFound', ['opener']);
                    break;
                } else if (substr($tokens[$i]['content'], 0, 8) === '>>>>>>> ') {
                    $phpcsFile->addError($error, $i, 'CloserFound', ['closer']);
                    break;
                }

                if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING) {
                    if ($tokens[$i]['content'] === '======='
                        && $tokens[($i + 1)]['code'] === T_DOC_COMMENT_WHITESPACE
                    ) {
                        $phpcsFile->addError($error, $i, 'DelimiterFound', ['delimiter']);
                        break;
                    }
                } else {
                    if ($tokens[$i]['content'] === "=======\n") {
                        $phpcsFile->addError($error, $i, 'DelimiterFound', ['delimiter']);
                    }
                }
                break;
            }//end switch
        }//end for

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
