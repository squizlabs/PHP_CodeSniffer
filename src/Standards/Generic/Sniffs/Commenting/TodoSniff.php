<?php

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Generic_Sniffs_Commenting_TodoSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Commenting_TodoSniff.
 *
 * Warns about TODO comments.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class TodoSniff implements Sniff
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
        return Tokens::$commentTokens;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $content = $tokens[$stackPtr]['content'];
        $matches = array();
        if (preg_match('/(?:\A|[^\p{L}]+)todo([^\p{L}]+(.*)|\Z)/ui', $content, $matches) !== 0) {
            // Clear whitespace and some common characters not required at
            // the end of a to-do message to make the warning more informative.
            $type        = 'CommentFound';
            $todoMessage = trim($matches[1]);
            $todoMessage = trim($todoMessage, '-:[](). ');
            $error       = 'Comment refers to a TODO task';
            $data        = array($todoMessage);
            if ($todoMessage !== '') {
                $type   = 'TaskFound';
                $error .= ' "%s"';
            }

            $phpcsFile->addWarning($error, $stackPtr, $type, $data);
        }

    }//end process()


}//end class
