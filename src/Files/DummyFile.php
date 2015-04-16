<?php

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Util;
use PHP_CodeSniffer\Exceptions\TokenizerException;

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
class DummyFile extends File
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
    public function __construct($content, Ruleset $ruleset, Config $config)
    {
        $this->setContent($content);

        // See if a filename was defined in the content.
        // This is done by including: phpcs_input_file: [file path]
        // as the first line of content.
        $path = 'STDIN';
        if ($content !== null) {
            if (substr($content, 0, 17) === 'phpcs_input_file:') {
                $eolPos   = strpos($content, $this->eolChar);
                $filename = trim(substr($content, 17, ($eolPos - 17)));
                $content  = substr($content, ($eolPos + strlen($this->eolChar)));
                $path     = $filename;
            }
        }

        return parent::__construct($path, $ruleset, $config);

    }//end __construct()


}//end class
