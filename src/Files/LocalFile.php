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
        if ($this->ignored === true) {
            return;
        }

        if ($this->config->cache === false) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }

            return parent::process();
        }

        $hash  = md5_file($this->path);
        $cache = Cache::get($this->path);
        if ($cache !== false && $cache['hash'] === $hash) {
            // We can't filter metrics, so just load all of them.
            $this->metrics = $cache['metrics'];

            // Replay the cached errors and warnings to filter out the ones
            // we don't need for this specific run.
            $this->config->cache = false;
            $this->replayErrors($cache['errors'], $cache['warnings']);
            $this->config->cache = true;

            if (PHP_CODESNIFFER_VERBOSITY > 0
                || (PHP_CODESNIFFER_CBF === true && empty($this->config->files) === false)
            ) {
                echo "[loaded from cache]... ";
            }

            return;
        }

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo PHP_EOL;
        }

        parent::process();

        $cache = array(
                  'hash'         => $hash,
                  'errors'       => $this->errors,
                  'warnings'     => $this->warnings,
                  'metrics'      => $this->metrics,
                  'errorCount'   => $this->errorCount,
                  'warningCount' => $this->warningCount,
                  'fixableCount' => $this->fixableCount,
                 );

        Cache::set($this->path, $cache);

        // During caching, we don't filter out errors in any way, so
        // we need to do that manually now by replaying them.
        $this->config->cache = false;
        $this->replayErrors($this->errors, $this->warnings);
        $this->config->cache = true;

    }//end process()


    private function replayErrors($errors, $warnings)
    {
        $this->errors       = array();
        $this->warnigns     = array();
        $this->errorCount   = 0;
        $this->warningCount = 0;
        $this->fixableCount = 0;

        foreach ($errors as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $this->activeListener = $error['listener'];
                    $this->_addError(
                        $error['message'],
                        $line,
                        $column,
                        $error['source'],
                        array(),
                        $error['severity'],
                        $error['fixable']
                    );
                }
            }
        }

        foreach ($warnings as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $this->activeListener = $error['listener'];
                    $this->_addWarning(
                        $error['message'],
                        $line,
                        $column,
                        $error['source'],
                        array(),
                        $error['severity'],
                        $error['fixable']
                    );
                }
            }
        }

    }//end replayErrors()


}//end class
