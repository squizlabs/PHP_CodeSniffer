<?php
/**
 * Verifies that constants have their visibility declared.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * All constants must have their visibility declared.
 *
 * Mainly this sniff is copied from slevomat/coding-standard
 *
 * @see https://github.com/slevomat/coding-standard/blob/master/SlevomatCodingStandard/Sniffs/Classes/ClassConstantVisibilitySniff.php
 */
class ConstantsVisibilitySniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_CONST];

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
        if (PHP_VERSION_ID < 70000) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if (count($tokens[$stackPtr]['conditions']) === 0) {
            return;
        }

        $classPointer = array_keys($tokens[$stackPtr]['conditions'])[(count($tokens[$stackPtr]['conditions']) - 1)];
        if (in_array($tokens[$classPointer]['code'], [T_CLASS, T_INTERFACE, T_ANON_CLASS], true) === false) {
            return;
        }

        $ineffectiveTokens = [
            T_WHITESPACE,
            T_COMMENT,
            T_DOC_COMMENT,
            T_DOC_COMMENT_OPEN_TAG,
            T_DOC_COMMENT_CLOSE_TAG,
            T_DOC_COMMENT_STAR,
            T_DOC_COMMENT_STRING,
            T_DOC_COMMENT_TAG,
            T_DOC_COMMENT_WHITESPACE,
            T_PHPCS_DISABLE,
            T_PHPCS_ENABLE,
            T_PHPCS_IGNORE,
            T_PHPCS_IGNORE_FILE,
            T_PHPCS_SET,
        ];
        $visibilityPointer = $phpcsFile->findPrevious($ineffectiveTokens, ($stackPtr - 1), null, true);
        if ($visibilityPointer === false) {
            return;
        }

        if (in_array($tokens[$visibilityPointer]['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true) === true) {
            return;
        }

        $token = $phpcsFile->findNext($ineffectiveTokens, ($stackPtr + 1), null, true);
        if ($token === false) {
            return;
        }

        $message = sprintf('Constant %s visibility missing.', $tokens[$token]['content']);
        $phpcsFile->addError($message, $stackPtr, 'MissingConstantVisibility');

    }//end process()


}//end class
