<?php

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;

/**
 * A PHP_CodeSniffer_File object represents a PHP source file and the tokens
 * associated with it.
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
 * A PHP_CodeSniffer_File object represents a PHP source file and the tokens
 * associated with it.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class LocalFile extends File
{


    /**
     * Constructs a PHP_CodeSniffer_File.
     *
     * @param string          $file      The absolute path to the file to process.
     * @param array(string)   $listeners The initial listeners listening to processing of this file.
     *                                   to processing of this file.
     * @param array           $ruleset   An array of rules from the ruleset.xml file.
     *                                   ruleset.xml file.
     * @param PHP_CodeSniffer $phpcs     The PHP_CodeSniffer object controlling this run.
     *                                   this run.
     *
     * @throws PHP_CodeSniffer_Exception If the register() method does
     *                                   not return an array.
     */
    public function __construct($path, Ruleset $ruleset, Config $config)
    {
        $path = trim($path);
        if (is_readable($path) === false) {
            exit('file not reable');
        }

        // Before we go and spend time tokenizing this file, just check
        // to see if there is a tag up top to indicate that the whole
        // file should be ignored. It must be on one of the first two lines.
        $handle = fopen($path, 'r');
        if ($handle !== false) {
            $firstContent  = fgets($handle);
            $firstContent .= fgets($handle);
            fclose($handle);

            if (strpos($firstContent, '@codingStandardsIgnoreFile') !== false) {
                // We are ignoring the whole file.
                if (PHP_CODESNIFFER_VERBOSITY > 0) {
                    echo 'Ignoring '.basename($path).PHP_EOL;
                }

                $this->ignored = true;
                return;
            }
        }

        $this->setContent(file_get_contents($path));

        return parent::__construct($path, $ruleset, $config);

    }//end __construct()

    function reloadContent()
    {
        $this->setContent(file_get_contents($this->path));
    }


}//end class
