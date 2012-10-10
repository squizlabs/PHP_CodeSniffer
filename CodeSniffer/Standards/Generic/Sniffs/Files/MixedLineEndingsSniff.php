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

        $filename = $phpcsFile->getFilename();
        $handle   = fopen($filename, 'r');
        if ($handle === false) {
            $error = 'Error opening file; could not check line endings';
            $phpcsFile->addError($error, 0, 'OpenFailed');
            return;
        }

        $expected = $phpcsFile->eolChar;
        $expected = str_replace("\n", '\n', $expected);
        $expected = str_replace("\r", '\r', $expected);

        $line     = '';
        $nextLine = '';
        while ($nextLine !== false) {
            if ($line === '') {
                $line = fgets($handle);
            }

            // PHP will split the /r and /n of a /r/n over two lines, so we need
            // bring them back together so the EOL character can be correctly
            // determined.
            if ($line[0] === "\n") {
                $line = substr($line, 1);
            }

            $nextLine = fgets($handle);
            if ($nextLine !== false && $nextLine[0] === "\n") {
                $line .= "\n";
            }

            $found = $phpcsFile->detectLineEndings($filename, $line);
            $found = str_replace("\n", '\n', $found);
            $found = str_replace("\r", '\r', $found);

            if ($found !== $expected) {
                $error = 'File contains mixed end of line characters; found "%s" and "%s"';
                $data  = array(
                          $expected,
                          $found,
                         );
                $phpcsFile->addError($error, 0, 'Found', $data);
                break;
            }

            if ($nextLine === false) {
                break;
            }

            $line = $nextLine;
        }//end while

        fclose($handle);

    }//end process()


}//end class

?>
