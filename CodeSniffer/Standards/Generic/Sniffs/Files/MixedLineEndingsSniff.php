<?php
/**
 * Generic_Sniffs_Files_MixedLineEndingsSniff.
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
 * Generic_Sniffs_Files_MixedLineEndingsSniff.
 *
 * Checks that the file does not use multiple EOL characters.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Files_MixedLineEndingsSniff implements PHP_CodeSniffer_Sniff
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
     * Replaces \r and \n with their plain text representation.
     *
     * @param string $string String being processed.
     *
     * @return string
     */
    protected function replaceLineEnds($string)
    {
        $string = str_replace("\n", '\n', $string);
        $string = str_replace("\r", '\r', $string);

        return $string;

    }//end replaceLineEnds()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $expected = $this->replaceLineEnds($phpcsFile->eolChar);

        // Remove backslashes from original file to avoid false matches
        $content = str_replace('\\', '', $phpcsFile->getFileContents());

        // Replace \r and \n with plain text representation
        $content = $this->replaceLineEnds($content);

        // Remove all string representations of expected line ends
        $content = str_replace($expected, '', $content);

        // Check whether content still includes backslashes
        if (preg_match('/(\\\\.)+/', $content, $found)) {
            $error = 'File contains mixed end of line characters; found "%s" and "%s"';
            $data  = array(
                      $expected,
                      $found[0],
                     );
            $phpcsFile->addError($error, 0, 'Found', $data);
        }

    }//end process()


}//end class

?>
