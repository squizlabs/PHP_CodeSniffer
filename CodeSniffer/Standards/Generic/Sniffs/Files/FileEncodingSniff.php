<?php
/**
 * Generic_Sniffs_Files_FileEncodingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Klaus Purer <klaus.purer@mail.com>
 * @copyright 2016 Klaus Purer
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Files_FileEncodingSniff.
 *
 * Validates the encoding of a file against a white list of allowed encodings.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Klaus Purer <klaus.purer@mail.com>
 * @copyright 2016 Klaus Purer
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Files_FileEncodingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * List of encodings that files may be encoded with.
     *
     * Any other detected encodings will throw a warning.
     *
     * @var array
     */
    public $allowedEncodings = array('UTF-8');


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_INLINE_HTML,
                T_OPEN_TAG,
               );

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
        // Not all PHP installs have the multi byte extension - nothing we can do.
        if (function_exists('mb_check_encoding') === false) {
            return $phpcsFile->numTokens;
        }

        $fileContent = $phpcsFile->getTokensAsString(0, $phpcsFile->numTokens);

        $validEncodingFound = false;
        foreach ($this->allowedEncodings as $encoding) {
            if (mb_check_encoding($fileContent, $encoding) === true) {
                $validEncodingFound = true;
            }
        }

        if ($validEncodingFound === false) {
            $warning = 'File encoding is invalid, expected %s';
            $data    = array(implode(' or ', $this->allowedEncodings));
            $phpcsFile->addWarning($warning, $stackPtr, 'InvalidEncoding', $data);
        }

        return $phpcsFile->numTokens;

    }//end process()


}//end class
