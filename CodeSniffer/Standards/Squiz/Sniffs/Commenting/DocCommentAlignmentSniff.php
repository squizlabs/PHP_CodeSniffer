<?php
/**
 * Squiz_Sniffs_Commenting_EmptyCatchCommentSniff.
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

/**
 * Squiz_Sniffs_Commenting_DocCommentAlignmentSniff.
 *
 * Tests that the stars in a doc comment align correctly.
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
class Squiz_Sniffs_Commenting_DocCommentAlignmentSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_DOC_COMMENT,
               );

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
        $tokens = $phpcsFile->getTokens();

        // We only want to get the first comment in a block. If there is
        // a comment on the line before this one, return.
        $docComment = $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr - 1);
        if ($docComment !== false) {
            if ($tokens[$docComment]['line'] === ($tokens[$stackPtr]['line'] - 1)) {
                return;
            }
        }

        $comments       = array($stackPtr);
        $currentComment = $stackPtr;
        $lastComment    = $stackPtr;
        while (($currentComment = $phpcsFile->findNext(T_DOC_COMMENT, $currentComment + 1)) !== false) {
            if ($tokens[$lastComment]['line'] === ($tokens[$currentComment]['line'] - 1)) {
                $comments[]  = $currentComment;
                $lastComment = $currentComment;
            } else {
                break;
            }
        }

        // The $comments array now contains pointers to each token in the
        // comment block.
        $requiredColumn  = strpos($tokens[$stackPtr]['content'], '*');
        $requiredColumn += $tokens[$stackPtr]['column'];

        foreach ($comments as $commentPointer) {
            $currentColumn  = strpos($tokens[$commentPointer]['content'], '*');
            $currentColumn += $tokens[$commentPointer]['column'];

            if ($currentColumn === $requiredColumn) {
                // Star is aligned correctly.
                continue;
            }

            $expected  = ($requiredColumn - 1);
            $expected .= ($expected === 1) ? ' space' : ' spaces';
            $found     = ($currentColumn - 1);
            $error     = "Expected $expected before asterisk; $found found";
            $phpcsFile->addError($error, $commentPointer);
        }

    }//end process()


}//end class

?>
