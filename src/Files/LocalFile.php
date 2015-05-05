<?php
/**
 * A local file represents a chunk of text has a file system location.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Util\Cache;

class LocalFile extends File
{


    /**
     * Creates a LocalFile object and sets the content.
     *
     * @param string                   $path    The absolute path to the file.
     * @param \PHP_CodeSniffer\Ruleset $ruleset The ruleset used for the run.
     * @param \PHP_CodeSniffer\Config  $config  The config data for the run.
     *
     * @return void
     */
    public function __construct($path, Ruleset $ruleset, Config $config)
    {
        $path = trim($path);
        if (is_readable($path) === false) {
            parent::__construct($path, $ruleset, $config);
            $error = 'Error opening file; file no longer exists or you do not have access to read the file';
            $this->_addError($error, 1, 1, 'Internal.LocalFile', array(), 5, false);
            $this->ignored = true;
            return;
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

        $this->path = $path;
        $this->reloadContent();

        return parent::__construct($path, $ruleset, $config);

    }//end __construct()


    /**
     * Loads the latest version of the file's content from the file system.
     *
     * @return void
     */
    function reloadContent()
    {
        $this->setContent(file_get_contents($this->path));

    }//end reloadContent()


    /**
     * 
     *
     * @return void
     */
    public function process()
    {
        if ($this->config->cache === false) {
            return parent::process();
        }

        $hash  = $this->path.'.'.md5_file($this->path);
        $cache = Cache::get($hash);
        if ($cache !== false) {
            $this->errors   = $cache['errors'];
            $this->warnings = $cache['warnings'];
            $this->metrics  = $cache['metrics'];
            $this->errorCount   = $cache['errorCount'];
            $this->warningCount = $cache['warningCount'];
            $this->fixableCount  = $cache['fixableCount'];

            if (PHP_CODESNIFFER_VERBOSITY > 0
                || (PHP_CODESNIFFER_CBF === true && empty($this->config->files) === false)
            ) {
                echo "[loaded from cache]... ";
            }

            return;
        }

        parent::process();

        $cache = array(
                  'errors'   => $this->errors,
                  'warnings' => $this->warnings,
                  'metrics'  => $this->metrics,
                  'errorCount'   => $this->errorCount,
                  'warningCount' => $this->warningCount,
                  'fixableCount'  => $this->fixableCount,
                 );

        Cache::set($hash, $cache);

    }//end process()


}//end class
