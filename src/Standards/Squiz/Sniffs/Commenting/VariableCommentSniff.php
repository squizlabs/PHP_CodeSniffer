<?php
/**
 * Parses and verifies the variable doc comment.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

class VariableCommentSniff extends AbstractVariableSniff
{


    /**
     * Called to process class member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processMemberVar(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $ignore = [
            T_PUBLIC       => T_PUBLIC,
            T_PRIVATE      => T_PRIVATE,
            T_PROTECTED    => T_PROTECTED,
            T_VAR          => T_VAR,
            T_STATIC       => T_STATIC,
            T_WHITESPACE   => T_WHITESPACE,
            T_STRING       => T_STRING,
            T_NS_SEPARATOR => T_NS_SEPARATOR,
            T_NULLABLE     => T_NULLABLE,
        ];

        for ($commentEnd = ($stackPtr - 1); $commentEnd >= 0; $commentEnd--) {
            if (isset($ignore[$tokens[$commentEnd]['code']]) === true) {
                continue;
            }

            if ($tokens[$commentEnd]['code'] === T_ATTRIBUTE_END
                && isset($tokens[$commentEnd]['attribute_opener']) === true
            ) {
                $commentEnd = $tokens[$commentEnd]['attribute_opener'];
                continue;
            }

            break;
        }

        if ($commentEnd === false
            || ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT)
        ) {
            $phpcsFile->addError('Missing member variable doc comment', $stackPtr, 'Missing');
            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a member variable comment', $stackPtr, 'WrongStyle');
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];

        $foundVar = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@var') {
                if ($foundVar !== null) {
                    $error = 'Only one @var tag is allowed in a member variable comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateVar');
                } else {
                    $foundVar = $tag;
                }
            } else if ($tokens[$tag]['content'] === '@see') {
                // Make sure the tag isn't empty.
                $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
                if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                    $error = 'Content missing for @see tag in member variable comment';
                    $phpcsFile->addError($error, $tag, 'EmptySees');
                }
            } else {
                $error = '%s tag is not allowed in member variable comment';
                $data  = [$tokens[$tag]['content']];
                $phpcsFile->addWarning($error, $tag, 'TagNotAllowed', $data);
            }//end if
        }//end foreach

        // The @var tag is the only one we require.
        if ($foundVar === null) {
            $error = 'Missing @var tag in member variable comment';
            $phpcsFile->addError($error, $commentEnd, 'MissingVar');
            return;
        }

        $firstTag = $tokens[$commentStart]['comment_tags'][0];
        if ($foundVar !== null && $tokens[$firstTag]['content'] !== '@var') {
            $error = 'The @var tag must be the first tag in a member variable comment';
            $phpcsFile->addError($error, $foundVar, 'VarOrder');
        }

        // Make sure the tag isn't empty and has the correct padding.
        $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $foundVar, $commentEnd);
        if ($string === false || $tokens[$string]['line'] !== $tokens[$foundVar]['line']) {
            $error = 'Content missing for @var tag in member variable comment';
            $phpcsFile->addError($error, $foundVar, 'EmptyVar');
            return;
        }

         // Support both a var type and a description.
        preg_match('`^((?:\|?(?:array\([^\)]*\)|[\\\\a-z0-9\[\]]+))*)( .*)?`i', $tokens[($foundVar + 2)]['content'], $varParts);
        if (isset($varParts[1]) === false) {
            return;
        }

        $varType = $varParts[1];

        // Check var type (can be multiple, separated by '|').
        $typeNames      = explode('|', $varType);
        $suggestedNames = [];
        foreach ($typeNames as $i => $typeName) {
            $suggestedName = Common::suggestType($typeName);
            if (in_array($suggestedName, $suggestedNames, true) === false) {
                $suggestedNames[] = $suggestedName;
            }
        }

        $suggestedType = implode('|', $suggestedNames);
        if ($varType !== $suggestedType) {
            $error = 'Expected "%s" but found "%s" for @var tag in member variable comment';
            $data  = [
                $suggestedType,
                $varType,
            ];
            $fix   = $phpcsFile->addFixableError($error, $foundVar, 'IncorrectVarType', $data);
            if ($fix === true) {
                $replacement = $suggestedType;
                if (empty($varParts[2]) === false) {
                    $replacement .= $varParts[2];
                }

                $phpcsFile->fixer->replaceToken(($foundVar + 2), $replacement);
                unset($replacement);
            }
        }

    }//end processMemberVar()


    /**
     * Called to process a normal variable.
     *
     * Not required for this sniff.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                         $stackPtr  The position where the double quoted
     *                                               string was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {

    }//end processVariable()


    /**
     * Called to process variables found in double quoted strings.
     *
     * Not required for this sniff.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                         $stackPtr  The position where the double quoted
     *                                               string was found.
     *
     * @return void
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {

    }//end processVariableInString()


}//end class
