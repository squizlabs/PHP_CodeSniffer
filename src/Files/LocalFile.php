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
        $this->path = trim($path);
        if (is_readable($this->path) === false) {
            parent::__construct($this->path, $ruleset, $config);
            $error = 'Error opening file; file no longer exists or you do not have access to read the file';
            $this->addMessage(true, $error, 1, 1, 'Internal.LocalFile', [], 5, false);
            $this->ignored = true;
            return;
        }

        // Before we go and spend time tokenizing this file, just check
        // to see if there is a tag up top to indicate that the whole
        // file should be ignored. It must be on one of the first two lines.
        if ($config->annotations === true) {
            $handle = fopen($this->path, 'r');
            if ($handle !== false) {
                $firstContent  = fgets($handle);
                $firstContent .= fgets($handle);
                fclose($handle);

                if (strpos($firstContent, '@codingStandardsIgnoreFile') !== false
                    || stripos($firstContent, 'phpcs:ignorefile') !== false
                ) {
                    // We are ignoring the whole file.
                    $this->ignored = true;
                    return;
                }
            }
        }

        $this->reloadContent();

        parent::__construct($this->path, $ruleset, $config);

    }//end __construct()


    /**
     * Loads the latest version of the file's content from the file system.
     *
     * @return void
     */
    public function reloadContent()
    {
        $this->setContent(file_get_contents($this->path));

    }//end reloadContent()


    /**
     * Processes the file.
     *
     * @return void
     */
    public function process()
    {
        if ($this->ignored === true) {
            return;
        }

        if ($this->configCache['cache'] === false) {
            parent::process();
            return;
        }

        $hash  = md5_file($this->path);
        $cache = Cache::get($this->path);
        if ($cache !== false && $cache['hash'] === $hash) {
            // We can't filter metrics, so just load all of them.
            $this->metrics = $cache['metrics'];

            if ($this->configCache['recordErrors'] === true) {
                // Replay the cached errors and warnings to filter out the ones
                // we don't need for this specific run.
                $this->configCache['cache'] = false;
                $this->replayErrors($cache['errors'], $cache['warnings']);
                $this->configCache['cache'] = true;
            } else {
                $this->errorCount   = $cache['errorCount'];
                $this->warningCount = $cache['warningCount'];
                $this->fixableCount = $cache['fixableCount'];
            }

            if (PHP_CODESNIFFER_VERBOSITY > 0
                || (PHP_CODESNIFFER_CBF === true && empty($this->config->files) === false)
            ) {
                echo "[loaded from cache]... ";
            }

            $this->numTokens = $cache['numTokens'];
            $this->fromCache = true;
            return;
        }//end if

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo PHP_EOL;
        }

        parent::process();

        $cache = [
            'hash'         => $hash,
            'errors'       => $this->errors,
            'warnings'     => $this->warnings,
            'metrics'      => $this->metrics,
            'errorCount'   => $this->errorCount,
            'warningCount' => $this->warningCount,
            'fixableCount' => $this->fixableCount,
            'numTokens'    => $this->numTokens,
        ];

        Cache::set($this->path, $cache);

        // During caching, we don't filter out errors in any way, so
        // we need to do that manually now by replaying them.
        if ($this->configCache['recordErrors'] === true) {
            $this->configCache['cache'] = false;
            $this->replayErrors($this->errors, $this->warnings);
            $this->configCache['cache'] = true;
        }

    }//end process()


    /**
     * Clears and replays error and warnings for the file.
     *
     * Replaying errors and warnings allows for filtering rules to be changed
     * and then errors and warnings to be reapplied with the new rules. This is
     * particularly useful while caching.
     *
     * @param array $errors   The list of errors to replay.
     * @param array $warnings The list of warnings to replay.
     *
     * @return void
     */
    private function replayErrors($errors, $warnings)
    {
        $this->errors       = [];
        $this->warnings     = [];
        $this->errorCount   = 0;
        $this->warningCount = 0;
        $this->fixableCount = 0;

        $this->replayingErrors = true;

        foreach ($errors as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $this->activeListener = $error['listener'];
                    $this->addMessage(
                        true,
                        $error['message'],
                        $line,
                        $column,
                        $error['source'],
                        [],
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
                    $this->addMessage(
                        false,
                        $error['message'],
                        $line,
                        $column,
                        $error['source'],
                        [],
                        $error['severity'],
                        $error['fixable']
                    );
                }
            }
        }

        $this->replayingErrors = false;

    }//end replayErrors()


}//end class
