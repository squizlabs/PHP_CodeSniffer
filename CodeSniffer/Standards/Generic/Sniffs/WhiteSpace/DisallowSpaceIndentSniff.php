<?php
/**
 * Generic_Sniffs_WhiteSpace_DisallowSpaceIndentSniff.
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
 * Generic_Sniffs_WhiteSpace_DisallowSpaceIndentSniff.
 *
 * Throws errors if spaces are used for indentation.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_WhiteSpace_DisallowSpaceIndentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                   'CSS',
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['column'] !== 1 || $tokens[$i]['code'] !== T_WHITESPACE) {
                continue;
            }

            // If tabs are being converted to spaces, the original content
            // should be used instead of the converted content.
            if (isset($tokens[$i]['orig_content']) === true) {
                $content = $tokens[$i]['orig_content'];
            } else {
                $content = $tokens[$i]['content'];
            }

            if ($content[0] === ' ') {
                // Space are considered ok if they are proceeded by tabs and not followed
                // by tabs, as is the case with standard docblock comments.
                $error = 'Tabs must be used to indent lines; spaces are not allowed';
                $phpcsFile->addError($error, $i, 'SpacesUsed');
                $phpcsFile->recordMetric($i, 'Line indent', 'spaces');
            } else if ($content[0] === "\t") {
                $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
            }
        }//end for

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
