<?php
/**
 * Tests that the correct Subversion properties are set.
 *
 * @author    Jack Bates <ms419@freezone.co.uk>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class SubversionPropertiesSniff implements Sniff
{

    /**
     * The Subversion properties that should be set.
     *
     * Key of array is the SVN property and the value is the
     * exact value the property should have or NULL if the
     * property should just be set but the value is not fixed.
     *
     * @var array
     */
    protected $properties = [
        'svn:keywords'  => 'Author Id Revision',
        'svn:eol-style' => 'native',
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $path       = $phpcsFile->getFilename();
        $properties = $this->getProperties($path);
        if ($properties === null) {
            // Not under version control.
            return ($phpcsFile->numTokens + 1);
        }

        $allProperties = ($properties + $this->properties);
        foreach ($allProperties as $key => $value) {
            if (isset($properties[$key]) === true
                && isset($this->properties[$key]) === false
            ) {
                $error = 'Unexpected Subversion property "%s" = "%s"';
                $data  = [
                    $key,
                    $properties[$key],
                ];
                $phpcsFile->addError($error, $stackPtr, 'Unexpected', $data);
                continue;
            }

            if (isset($properties[$key]) === false
                && isset($this->properties[$key]) === true
            ) {
                $error = 'Missing Subversion property "%s" = "%s"';
                $data  = [
                    $key,
                    $this->properties[$key],
                ];
                $phpcsFile->addError($error, $stackPtr, 'Missing', $data);
                continue;
            }

            if ($properties[$key] !== null
                && $properties[$key] !== $this->properties[$key]
            ) {
                $error = 'Subversion property "%s" = "%s" does not match "%s"';
                $data  = [
                    $key,
                    $properties[$key],
                    $this->properties[$key],
                ];
                $phpcsFile->addError($error, $stackPtr, 'NoMatch', $data);
            }
        }//end foreach

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


    /**
     * Returns the Subversion properties which are actually set on a path.
     *
     * Returns NULL if the file is not under version control.
     *
     * @param string $path The path to return Subversion properties on.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If Subversion properties file could
     *                                                      not be opened.
     */
    protected function getProperties($path)
    {
        $properties = [];

        $paths   = [];
        $paths[] = dirname($path).'/.svn/props/'.basename($path).'.svn-work';
        $paths[] = dirname($path).'/.svn/prop-base/'.basename($path).'.svn-base';

        $foundPath = false;
        foreach ($paths as $path) {
            if (file_exists($path) === true) {
                $foundPath = true;

                $handle = fopen($path, 'r');
                if ($handle === false) {
                    $error = 'Error opening file; could not get Subversion properties';
                    throw new RuntimeException($error);
                }

                while (feof($handle) === false) {
                    // Read a key length line. Might be END, though.
                    $buffer = trim(fgets($handle));

                    // Check for the end of the hash.
                    if ($buffer === 'END') {
                        break;
                    }

                    // Now read that much into a buffer.
                    $key = fread($handle, substr($buffer, 2));

                    // Suck up extra newline after key data.
                    fgetc($handle);

                    // Read a value length line.
                    $buffer = trim(fgets($handle));

                    // Now read that much into a buffer.
                    $length = substr($buffer, 2);
                    if ($length === '0') {
                        // Length of value is ZERO characters, so
                        // value is actually empty.
                        $value = '';
                    } else {
                        $value = fread($handle, $length);
                    }

                    // Suck up extra newline after value data.
                    fgetc($handle);

                    $properties[$key] = $value;
                }//end while

                fclose($handle);
            }//end if
        }//end foreach

        if ($foundPath === false) {
            return null;
        }

        return $properties;

    }//end getProperties()


}//end class
