<?php

/**
 * A local file represents a chunk of text has a file system location.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer\Files;

use Symplify\PHP7_CodeSniffer\Ruleset;
use Symplify\PHP7_CodeSniffer\Configuration;
use Symplify\PHP7_CodeSniffer\Util\Cache;

class LocalFile extends File
{


    public function __construct(string $path, Ruleset $ruleset, Configuration $config)
    {
        $path = trim($path);
        if (is_readable($path) === false) {
            parent::__construct($path, $ruleset, $config);
            $error = 'Error opening file; file no longer exists or you do not have access to read the file';
            $this->addMessage(true, $error, 1, 1, 'Internal.LocalFile', array(), 5, false);
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
            return parent::process();
        }

        $hash  = md5_file($this->path);
        $cache = Cache::get($this->path);
        if ($cache !== false && $cache['hash'] === $hash) {
            // We can't filter metrics, so just load all of them.
            $this->metrics = $cache['metrics'];

            // Replay the cached errors and warnings to filter out the ones
            // we don't need for this specific run.
            $this->configCache['cache'] = false;
            $this->replayErrors($cache['errors'], $cache['warnings']);
            $this->configCache['cache'] = true;

            $this->numTokens = $cache['numTokens'];
            $this->fromCache = true;
            return;
        }//end if

        parent::process();

        $cache = array(
                  'hash'         => $hash,
                  'errors'       => $this->errors,
                  'warnings'     => $this->warnings,
                  'metrics'      => $this->metrics,
                  'errorCount'   => $this->errorCount,
                  'warningCount' => $this->warningCount,
                  'fixableCount' => $this->fixableCount,
                  'numTokens'    => $this->numTokens,
                 );

        Cache::set($this->path, $cache);

        // During caching, we don't filter out errors in any way, so
        // we need to do that manually now by replaying them.
        $this->configCache['cache'] = false;
        $this->replayErrors($this->errors, $this->warnings);
        $this->configCache['cache'] = true;

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
        $this->errors       = array();
        $this->warnings     = array();
        $this->errorCount   = 0;
        $this->warningCount = 0;
        $this->fixableCount = 0;

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
                        array(),
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
                        array(),
                        $error['fixable']
                    );
                }
            }
        }

    }//end replayErrors()


}//end class
