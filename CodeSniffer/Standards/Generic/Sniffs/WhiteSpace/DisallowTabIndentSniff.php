<?php
/**
 * Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff.
 *
 * Throws errors if tabs are used for indentation.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff implements PHP_CodeSniffer_Sniff
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

            if ($tokens[$i]['content'][0] === "\t") {
                $error = 'Spaces must be used to indent lines; tabs are not allowed';
                $fix   = $phpcsFile->addFixableError($error, $i, 'TabsUsed');
                $phpcsFile->recordMetric($i, 'Line indent', 'tabs');

                if ($fix === true && $phpcsFile->fixer->enabled === true) {
                    // Replace tabs with spaces, usign an indent of 4 spaces.
                    // Other sniffs can then correct the indent if they need to.
                    $newContent = str_replace("\t", '    ', $tokens[$i]['content']);
                    $phpcsFile->fixer->replaceToken($i, $newContent);
                }
            } else if ($tokens[$i]['content'][0] === ' ') {
                $phpcsFile->recordMetric($i, 'Line indent', 'spaces');
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
