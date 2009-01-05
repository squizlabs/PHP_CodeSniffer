<?php
/**
 * Generic_Sniffs_VersionControl_SubversionPropertiesSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Jack Bates <ms419@freezone.co.uk>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_VersionControl_SubversionPropertiesSniff.
 *
 * Tests that the correct Subversion properties are set.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Jack Bates <ms419@freezone.co.uk>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_VersionControl_SubversionPropertiesSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The Subversion properties that should be set.
     *
     * @var array
     */
    protected $properties = array(
                             'svn:keywords'  => 'Author Id Revision',
                             'svn:eol-style' => 'native',
                            );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_TAG,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure this is the first PHP open tag so we don't process the
        // same file twice.
        $prevOpenTag = $phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1));
        if ($prevOpenTag !== false) {
            return;
        }

        $path       = $phpcsFile->getFileName();
        $properties = $this->properties($path);

        foreach (($properties + $this->properties) as $key => $value) {
            if (isset($properties[$key]) === true
                && isset($this->properties[$key]) === false
            ) {
                $error = 'Unexpected Subversion property "'.$key.'" = "'.$properties[$key].'"';
                $phpcsFile->addError($error, $stackPtr);
                continue;
            }

            if (isset($properties[$key]) === false
                && isset($this->properties[$key]) === true
            ) {
                $error = 'Missing Subversion property "'.$key.'" = "'.$this->properties[$key].'"';
                $phpcsFile->addError($error, $stackPtr);
                continue;
            }

            if ($properties[$key] !== $this->properties[$key]) {
                $error = 'Subversion property "'.$key.'" = "'.$properties[$key].'" does not match "'.$this->properties[$key].'"';
                $phpcsFile->addError($error, $stackPtr);
            }
        }

    }//end process()


    /**
     * Returns the Subversion properties which are actually set on a path.
     *
     * @param string $path The path to return Subversion properties on.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If Subversion properties file could
     *                                   not be opened.
     */
    protected function properties($path)
    {
        $properties = array();

        $paths   = array();
        $paths[] = dirname($path).'/.svn/props/'.basename($path).'.svn-work';
        $paths[] = dirname($path).'/.svn/prop-base/'.basename($path).'.svn-base';

        foreach ($paths as $path) {
            if (true === file_exists($path)) {
                if (false === $handle = fopen($path, 'r')) {
                    $error = 'Error opening file; could not get Subversion properties';
                    throw new PHP_CodeSniffer_Exception($error);
                }

                while (!feof($handle)) {

                    // Read a key length line. Might be END, though.
                    $buffer = fgets($handle);

                    // Check for the end of the hash.
                    if ("END\n" === $buffer) {
                        break;
                    }

                    // Now read that much into a buffer.
                    $key = fread($handle, substr($buffer, 2));

                    // Suck up extra newline after key data.
                    fgetc($handle);

                    // Read a value length line.
                    $buffer = fgets($handle);

                    // Now read that much into a buffer.
                    $value = fread($handle, substr($buffer, 2));

                    // Suck up extra newline after value data.
                    fgetc($handle);

                    $properties[$key] = $value;
                }//end while

                fclose($handle);
            }//end if
        }//end foreach

        return $properties;

    }//end properties()


}//end class

?>
