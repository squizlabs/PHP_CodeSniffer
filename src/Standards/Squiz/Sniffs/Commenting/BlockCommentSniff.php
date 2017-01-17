<?php
/**
 * Verifies that block comments are used appropriately.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class BlockCommentSniff implements Sniff
{

    /**
     * The --tab-width CLI value that is being used.
     *
     * @var integer
     */
    private $tabWidth = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_COMMENT,
                T_DOC_COMMENT_OPEN_TAG,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->tabWidth === null) {
            if (isset($phpcsFile->config->tabWidth) === false || $phpcsFile->config->tabWidth === 0) {
                // We have no idea how wide tabs are, so assume 4 spaces for fixing.
                $this->tabWidth = 4;
            } else {
                $this->tabWidth = $phpcsFile->config->tabWidth;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // If it's an inline comment, return.
        if (substr($tokens[$stackPtr]['content'], 0, 2) !== '/*') {
            return;
        }

        // If this is a function/class/interface doc block comment, skip it.
        // We are only interested in inline doc block comments.
        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            $ignore    = array(
                          T_CLASS     => true,
                          T_INTERFACE => true,
                          T_TRAIT     => true,
                          T_FUNCTION  => true,
                          T_PUBLIC    => true,
                          T_PRIVATE   => true,
                          T_FINAL     => true,
                          T_PROTECTED => true,
                          T_STATIC    => true,
                          T_ABSTRACT  => true,
                          T_CONST     => true,
                          T_VAR       => true,
                         );
            if (isset($ignore[$tokens[$nextToken]['code']]) === true) {
                return;
            }

            $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prevToken]['code'] === T_OPEN_TAG) {
                return;
            }

            $error = 'Block comments must be started with /*';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'WrongStart');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, '/*');
            }

            $end = $tokens[$stackPtr]['comment_closer'];
            if ($tokens[$end]['content'] !== '*/') {
                $error = 'Block comments must be ended with */';
                $fix   = $phpcsFile->addFixableError($error, $end, 'WrongEnd');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($end, '*/');
                }
            }

            return;
        }//end if

        $commentLines  = array($stackPtr);
        $nextComment   = $stackPtr;
        $lastLine      = $tokens[$stackPtr]['line'];
        $commentString = $tokens[$stackPtr]['content'];

        // Construct the comment into an array.
        while (($nextComment = $phpcsFile->findNext(T_WHITESPACE, ($nextComment + 1), null, true)) !== false) {
            if ($tokens[$nextComment]['code'] !== $tokens[$stackPtr]['code']) {
                // Found the next bit of code.
                break;
            }

            if (($tokens[$nextComment]['line'] - 1) !== $lastLine) {
                // Not part of the block.
                break;
            }

            $lastLine       = $tokens[$nextComment]['line'];
            $commentLines[] = $nextComment;
            $commentString .= $tokens[$nextComment]['content'];
            if ($tokens[$nextComment]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                break;
            }
        }

        $commentText = str_replace($phpcsFile->eolChar, '', $commentString);
        $commentText = trim($commentText, '/* ');
        if ($commentText === '') {
            $error = 'Empty block comment not allowed';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Empty');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, '');
                $lastToken = array_pop($commentLines);
                for ($i = ($stackPtr + 1); $i <= $lastToken; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        if (count($commentLines) === 1) {
            $error = 'Single line block comment not allowed; use inline ("// text") comment instead';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SingleLine');
            if ($fix === true) {
                $comment = '// '.$commentText.$phpcsFile->eolChar;
                $phpcsFile->fixer->replaceToken($stackPtr, $comment);
            }

            return;
        }

        $content = trim($tokens[$stackPtr]['content']);
        if ($content !== '/*' && $content !== '/**') {
            $error = 'Block comment text must start on a new line';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoNewLine');
            if ($fix === true) {
                $indent = '';
                if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
                    if (isset($tokens[($stackPtr - 1)]['orig_content']) === true) {
                        $indent = $tokens[($stackPtr - 1)]['orig_content'];
                    } else {
                        $indent = $tokens[($stackPtr - 1)]['content'];
                    }
                }

                $comment = preg_replace(
                    '/^(\s*\/\*\*?)/',
                    '$1'.$phpcsFile->eolChar.$indent,
                    $tokens[$stackPtr]['content'],
                    1
                );
                $phpcsFile->fixer->replaceToken($stackPtr, $comment);
            }

            return;
        }//end if

        $starColumn = ($tokens[$stackPtr]['column'] + 3);

        // Make sure first line isn't blank.
        if (trim($tokens[$commentLines[1]]['content']) === '') {
            $error = 'Empty line not allowed at start of comment';
            $fix   = $phpcsFile->addFixableError($error, $commentLines[1], 'HasEmptyLine');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($commentLines[1], '');
            }
        } else {
            // Check indentation of first line.
            $content      = $tokens[$commentLines[1]]['content'];
            $commentText  = ltrim($content);
            $leadingSpace = (strlen($content) - strlen($commentText));
            if ($leadingSpace !== $starColumn) {
                $expected = $starColumn.' space';
                if ($starColumn !== 1) {
                    $expected .= 's';
                }

                $data = array(
                         $expected,
                         $leadingSpace,
                        );

                $error = 'First line of comment not aligned correctly; expected %s but found %s';
                $fix   = $phpcsFile->addFixableError($error, $commentLines[1], 'FirstLineIndent', $data);
                if ($fix === true) {
                    if (isset($tokens[$commentLines[1]]['orig_content']) === true
                        && $tokens[$commentLines[1]]['orig_content'][0] === "\t"
                    ) {
                        // Line is indented using tabs.
                        $padding = str_repeat("\t", floor($starColumn / $this->tabWidth));
                    } else {
                        $padding = str_repeat(' ', $starColumn);
                    }

                    $phpcsFile->fixer->replaceToken($commentLines[1], $padding.ltrim($content));
                }
            }//end if

            if (preg_match('/^\p{Ll}/u', $commentText) === 1) {
                $error = 'Block comments must start with a capital letter';
                $phpcsFile->addError($error, $commentLines[1], 'NoCapital');
            }
        }//end if

        // Check that each line of the comment is indented past the star.
        foreach ($commentLines as $line) {
            $leadingSpace = (strlen($tokens[$line]['content']) - strlen(ltrim($tokens[$line]['content'])));
            // First and last lines (comment opener and closer) are handled separately.
            if ($line === $commentLines[(count($commentLines) - 1)] || $line === $commentLines[0]) {
                continue;
            }

            // First comment line was handled above.
            if ($line === $commentLines[1]) {
                continue;
            }

            // If it's empty, continue.
            if (trim($tokens[$line]['content']) === '') {
                continue;
            }

            if ($leadingSpace < $starColumn) {
                $expected = $starColumn.' space';
                if ($starColumn !== 1) {
                    $expected .= 's';
                }

                $data = array(
                         $expected,
                         $leadingSpace,
                        );

                $error = 'Comment line indented incorrectly; expected at least %s but found %s';
                $fix   = $phpcsFile->addFixableError($error, $line, 'LineIndent', $data);
                if ($fix === true) {
                    if (isset($tokens[$line]['orig_content']) === true
                        && $tokens[$line]['orig_content'][0] === "\t"
                    ) {
                        // Line is indented using tabs.
                        $padding = str_repeat("\t", floor($starColumn / $this->tabWidth));
                    } else {
                        $padding = str_repeat(' ', $starColumn);
                    }

                    $phpcsFile->fixer->replaceToken($line, $padding.ltrim($tokens[$line]['content']));
                }
            }//end if
        }//end foreach

        // Finally, test the last line is correct.
        $lastIndex = (count($commentLines) - 1);
        $content   = trim($tokens[$commentLines[$lastIndex]]['content']);
        if ($content !== '*/' && $content !== '**/') {
            $error = 'Comment closer must be on a new line';
            $phpcsFile->addError($error, $commentLines[$lastIndex], 'CloserSameLine');
        } else {
            $content      = $tokens[$commentLines[$lastIndex]]['content'];
            $commentText  = ltrim($content);
            $leadingSpace = (strlen($content) - strlen($commentText));
            if ($leadingSpace !== ($tokens[$stackPtr]['column'] - 1)) {
                $expected = ($tokens[$stackPtr]['column'] - 1);
                if ($expected === 1) {
                    $expected .= ' space';
                } else {
                    $expected .= ' spaces';
                }

                $data = array(
                         $expected,
                         $leadingSpace,
                        );

                $error = 'Last line of comment aligned incorrectly; expected %s but found %s';
                $phpcsFile->addError($error, $commentLines[$lastIndex], 'LastLineIndent', $data);
            }
        }//end if

        // Check that the lines before and after this comment are blank.
        $contentBefore = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if (isset($tokens[$contentBefore]['scope_closer']) === true
            && $tokens[$contentBefore]['scope_opener'] === $contentBefore
        ) {
            if (($tokens[$stackPtr]['line'] - $tokens[$contentBefore]['line']) !== 1) {
                $error = 'Empty line not required before block comment';
                $phpcsFile->addError($error, $stackPtr, 'HasEmptyLineBefore');
            }
        } else {
            if (($tokens[$stackPtr]['line'] - $tokens[$contentBefore]['line']) < 2) {
                $error = 'Empty line required before block comment';
                $phpcsFile->addError($error, $stackPtr, 'NoEmptyLineBefore');
            }
        }

        $commentCloser = $commentLines[$lastIndex];
        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, ($commentCloser + 1), null, true);
        if ($contentAfter !== false && ($tokens[$contentAfter]['line'] - $tokens[$commentCloser]['line']) < 2) {
            $error = 'Empty line required after block comment';
            $phpcsFile->addError($error, $commentCloser, 'NoEmptyLineAfter');
        }

    }//end process()


}//end class
