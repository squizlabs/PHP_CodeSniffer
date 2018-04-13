<?php
/**
 * Ensures doc blocks follow basic formatting.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DocCommentSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_DOC_COMMENT_OPEN_TAG];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['comment_closer']) === false
            || ($tokens[$tokens[$stackPtr]['comment_closer']]['content'] === ''
            && $tokens[$stackPtr]['comment_closer'] === ($phpcsFile->numTokens - 1))
        ) {
            // Don't process an unfinished comment during live coding.
            return;
        }

        $commentStart = $stackPtr;
        $commentEnd   = $tokens[$stackPtr]['comment_closer'];

        $empty = [
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_STAR,
        ];

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
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($stackPtr);
                $phpcsFile->fixer->addContentBefore($short, '* ');
                $phpcsFile->fixer->endChangeset();
            }
        }

        // The last line of the comment should just be the */ code.
        $prev = $phpcsFile->findPrevious($empty, ($commentEnd - 1), $stackPtr, true);
        if ($tokens[$prev]['line'] === $tokens[$commentEnd]['line']) {
            $error = 'The close comment tag must be the only content on the line';
            $fix   = $phpcsFile->addFixableError($error, $commentEnd, 'ContentBeforeClose');
            if ($fix === true) {
                $phpcsFile->fixer->addNewlineBefore($commentEnd);
            }
        }

        // Check for additional blank lines at the end of the comment.
        if ($tokens[$prev]['line'] < ($tokens[$commentEnd]['line'] - 1)) {
            $error = 'Additional blank lines found at end of doc comment';
            $fix   = $phpcsFile->addFixableError($error, $commentEnd, 'SpacingAfter');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($prev + 1); $i < $commentEnd; $i++) {
                    if ($tokens[($i + 1)]['line'] === $tokens[$commentEnd]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }

        // Check for a comment description.
        if ($tokens[$short]['code'] !== T_DOC_COMMENT_STRING) {
            $error = 'Missing short description in doc comment';
            $phpcsFile->addError($error, $stackPtr, 'MissingShort');
        } else {
            // No extra newline before short description.
            if ($tokens[$short]['line'] !== ($tokens[$stackPtr]['line'] + 1)) {
                $error = 'Doc comment short description must be on the first line';
                $fix   = $phpcsFile->addFixableError($error, $short, 'SpacingBeforeShort');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $stackPtr; $i < $short; $i++) {
                        if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                            continue;
                        } else if ($tokens[$i]['line'] === $tokens[$short]['line']) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }

            // Account for the fact that a short description might cover
            // multiple lines.
            $shortContent = $tokens[$short]['content'];
            $shortEnd     = $short;
            for ($i = ($short + 1); $i < $commentEnd; $i++) {
                if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING) {
                    if ($tokens[$i]['line'] === ($tokens[$shortEnd]['line'] + 1)) {
                        $shortContent .= $tokens[$i]['content'];
                        $shortEnd      = $i;
                    } else {
                        break;
                    }
                }
            }

            if (preg_match('/^\p{Ll}/u', $shortContent) === 1) {
                $error = 'Doc comment short description must start with a capital letter';
                $phpcsFile->addError($error, $short, 'ShortNotCapital');
            }

            $long = $phpcsFile->findNext($empty, ($shortEnd + 1), ($commentEnd - 1), true);
            if ($long !== false && $tokens[$long]['code'] === T_DOC_COMMENT_STRING) {
                if ($tokens[$long]['line'] !== ($tokens[$shortEnd]['line'] + 2)) {
                    $error = 'There must be exactly one blank line between descriptions in a doc comment';
                    $fix   = $phpcsFile->addFixableError($error, $long, 'SpacingBetween');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($shortEnd + 1); $i < $long; $i++) {
                            if ($tokens[$i]['line'] === $tokens[$shortEnd]['line']) {
                                continue;
                            } else if ($tokens[$i]['line'] === ($tokens[$long]['line'] - 1)) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }

                if (preg_match('/^\p{Ll}/u', $tokens[$long]['content']) === 1) {
                    $error = 'Doc comment long description must start with a capital letter';
                    $phpcsFile->addError($error, $long, 'LongNotCapital');
                }
            }//end if
        }//end if

        if (empty($tokens[$commentStart]['comment_tags']) === true) {
            // No tags in the comment.
            return;
        }

        $firstTag = $tokens[$commentStart]['comment_tags'][0];
        $prev     = $phpcsFile->findPrevious($empty, ($firstTag - 1), $stackPtr, true);
        if ($tokens[$firstTag]['line'] !== ($tokens[$prev]['line'] + 2)) {
            $error = 'There must be exactly one blank line before the tags in a doc comment';
            $fix   = $phpcsFile->addFixableError($error, $firstTag, 'SpacingBeforeTags');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($prev + 1); $i < $firstTag; $i++) {
                    if ($tokens[$i]['line'] === $tokens[$firstTag]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $indent = str_repeat(' ', $tokens[$stackPtr]['column']);
                $phpcsFile->fixer->addContent($prev, $phpcsFile->eolChar.$indent.'*'.$phpcsFile->eolChar);
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Break out the tags into groups and check alignment within each.
        // A tag group is one where there are no blank lines between tags.
        // The param tag group is special as it requires all @param tags to be inside.
        $tagGroups    = [];
        $groupid      = 0;
        $paramGroupid = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $pos => $tag) {
            if ($pos > 0) {
                $prev = $phpcsFile->findPrevious(
                    T_DOC_COMMENT_STRING,
                    ($tag - 1),
                    $tokens[$commentStart]['comment_tags'][($pos - 1)]
                );

                if ($prev === false) {
                    $prev = $tokens[$commentStart]['comment_tags'][($pos - 1)];
                }

                if ($tokens[$prev]['line'] !== ($tokens[$tag]['line'] - 1)) {
                    $groupid++;
                }
            }

            if ($tokens[$tag]['content'] === '@param') {
                if ($paramGroupid !== null
                    && $paramGroupid !== $groupid
                ) {
                    $error = 'Parameter tags must be grouped together in a doc comment';
                    $phpcsFile->addError($error, $tag, 'ParamGroup');
                }

                if ($paramGroupid === null) {
                    $paramGroupid = $groupid;
                }
            }//end if

            $tagGroups[$groupid][] = $tag;
        }//end foreach

        foreach ($tagGroups as $groupid => $group) {
            $maxLength = 0;
            $paddings  = [];
            foreach ($group as $pos => $tag) {
                if ($paramGroupid === $groupid
                    && $tokens[$tag]['content'] !== '@param'
                ) {
                    $error = 'Tag cannot be grouped with parameter tags in a doc comment';
                    $phpcsFile->addError($error, $tag, 'NonParamGroup');
                }

                $tagLength = strlen($tokens[$tag]['content']);
                if ($tagLength > $maxLength) {
                    $maxLength = $tagLength;
                }

                // Check for a value. No value means no padding needed.
                $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
                if ($string !== false && $tokens[$string]['line'] === $tokens[$tag]['line']) {
                    $paddings[$tag] = strlen($tokens[($tag + 1)]['content']);
                }
            }

            // Check that there was single blank line after the tag block
            // but account for a multi-line tag comments.
            $lastTag = $group[$pos];
            $next    = $phpcsFile->findNext(T_DOC_COMMENT_TAG, ($lastTag + 3), $commentEnd);
            if ($next !== false) {
                $prev = $phpcsFile->findPrevious([T_DOC_COMMENT_TAG, T_DOC_COMMENT_STRING], ($next - 1), $commentStart);
                if ($tokens[$next]['line'] !== ($tokens[$prev]['line'] + 2)) {
                    $error = 'There must be a single blank line after a tag group';
                    $fix   = $phpcsFile->addFixableError($error, $lastTag, 'SpacingAfterTagGroup');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prev + 1); $i < $next; $i++) {
                            if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $indent = str_repeat(' ', $tokens[$stackPtr]['column']);
                        $phpcsFile->fixer->addContent($prev, $phpcsFile->eolChar.$indent.'*'.$phpcsFile->eolChar);
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }//end if

            // Now check paddings.
            foreach ($paddings as $tag => $padding) {
                $required = ($maxLength - strlen($tokens[$tag]['content']) + 1);

                if ($padding !== $required) {
                    $error = 'Tag value indented incorrectly; expected %s spaces but found %s';
                    $data  = [
                        $required,
                        $padding,
                    ];

                    $fix = $phpcsFile->addFixableError($error, ($tag + 1), 'TagValueIndent', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($tag + 1), str_repeat(' ', $required));
                    }
                }
            }
        }//end foreach

        // If there is a param group, it needs to be first.
        if ($paramGroupid !== null && $paramGroupid !== 0) {
            $error = 'Parameter tags must be defined first in a doc comment';
            $phpcsFile->addError($error, $tagGroups[$paramGroupid][0], 'ParamNotFirst');
        }

        $foundTags = [];
        foreach ($tokens[$stackPtr]['comment_tags'] as $pos => $tag) {
            $tagName = $tokens[$tag]['content'];
            if (isset($foundTags[$tagName]) === true) {
                $lastTag = $tokens[$stackPtr]['comment_tags'][($pos - 1)];
                if ($tokens[$lastTag]['content'] !== $tagName) {
                    $error = 'Tags must be grouped together in a doc comment';
                    $phpcsFile->addError($error, $tag, 'TagsNotGrouped');
                }

                continue;
            }

            $foundTags[$tagName] = true;
        }

    }//end process()


}//end class
