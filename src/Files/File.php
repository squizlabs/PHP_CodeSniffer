<?php

namespace PHP_CodeSniffer\Files;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Util;
use PHP_CodeSniffer\Exceptions\RuntimeException;
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
class File
{

    /**
     * The absolute path to the file associated with this object.
     *
     * @var string
     */
    protected $path = '';
    protected $content = '';
    public $ignored = false;
    public $config = null;

    /**
     * The EOL character this file uses.
     *
     * @var string
     */
    public $eolChar = '';

    /**
     * The Fixer object to control fixing errors.
     *
     * @var PHP_CodeSniffer_Fixer
     */
    public $fixer = null;

    /**
     * The tokenizer being used for this file.
     *
     * @var object
     */
    public $tokenizer = null;


    /**
     * The number of tokens in this file.
     *
     * Stored here to save calling count() everywhere.
     *
     * @var int
     */
    public $numTokens = 0;

    /**
     * The tokens stack map.
     *
     * Note that the tokens in this array differ in format to the tokens
     * produced by token_get_all(). Tokens are initially produced with
     * token_get_all(), then augmented so that it's easier to process them.
     *
     * @var array()
     * @see Tokens.php
     */
    protected $tokens = array();

    /**
     * The errors raised from PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getErrors()
     */
    protected $errors = array();

    /**
     * The warnings raised from PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getWarnings()
     */
    protected $warnings = array();

    /**
     * The metrics recorded from PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getMetrics()
     */
    protected $metrics = array();

    /**
     * Record the errors and warnings raised.
     *
     * @var bool
     */
    protected $recordErrors = true;


    /**
     * An array of sniffs that are being ignored.
     *
     * @var array()
     */
    protected $ignoredListeners = array();

    /**
     * An array of message codes that are being ignored.
     *
     * @var array()
     */
    protected $ignoredCodes = array();

    /**
     * The total number of errors raised.
     *
     * @var int
     */
    protected $errorCount = 0;

    /**
     * The total number of warnings raised.
     *
     * @var int
     */
    protected $warningCount = 0;

    /**
     * The total number of errors/warnings that can be fixed.
     *
     * @var int
     */
    protected $fixableCount = 0;

    /**
     * An array of sniffs listening to this file's processing.
     *
     * @var array(PHP_CodeSniffer_Sniff)
     */
    protected $listeners = array();

    /**
     * The class name of the sniff currently processing the file.
     *
     * @var string
     */
    protected $activeListener = '';

    /**
     * An array of sniffs being processed and how long they took.
     *
     * @var array()
     */
    protected $listenerTimes = array();

    /**
     * An array of rules from the ruleset.xml file.
     *
     * This value gets set by PHP_CodeSniffer when the object is created.
     * It may be empty, indicating that the ruleset does not override
     * any of the default sniff settings.
     *
     * @var array
     */
    public $ruleset = array();


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
        $this->path          = $path;
        $this->ruleset       = $ruleset;
        $this->config        = $config;
        $this->fixer         = new Fixer();

        $parts = explode('.', $path);
        $extension = array_pop($parts);
        if (isset($config->extensions[$extension]) === true) {
            $this->tokenizerType = $config->extensions[$extension];
        } else {
            // Revert to default.
            $this->tokenizerType = 'PHP';
        }
/*
        if ($this->config->interactive === false) {
            $cliValues = $phpcs->config->getCommandLineValues();
            if (isset($cliValues['showSources']) === true
                && $cliValues['showSources'] !== true
            ) {
                $recordErrors = false;
                foreach ($cliValues['reports'] as $report => $output) {
                    $reportClass = $phpcs->reporting->factory($report);
                    if (property_exists($reportClass, 'recordErrors') === false
                        || $reportClass->recordErrors === true
                    ) {
                        $recordErrors = true;
                        break;
                    }
                }

                $this->recordErrors = $recordErrors;
            }
        }
        */

    }//end __construct()


    function setContent($content)
    {
        $this->content = $content;
        $this->tokens = array();

        try {
            $this->eolChar = Util\Common::detectLineEndings($content);
        } catch (RuntimeException $e) {
            $this->addWarningOnLine($e->getMessage(), 1, 'Internal.DetectLineEndings');
            return;
        }
    }

    /**
     * Starts the stack traversal and tells listeners when tokens are found.
     *
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     */
    public function process()
    {
        $this->errors       = array();
        $this->warnings     = array();
        $this->errorCount   = 0;
        $this->warningCount = 0;
        $this->fixableCount = 0;

        // Reset the ignored lines because lines numbers may have changed
        // if we are fixing this file.
        #self::$ignoredLines = array();

/*
        // If this is standard input, see if a filename was passed in as well.
        // This is done by including: phpcs_input_file: [file path]
        // as the first line of content.
        if ($this->path === 'STDIN' && $contents !== null) {
            if (substr($contents, 0, 17) === 'phpcs_input_file:') {
                $eolPos      = strpos($contents, $this->eolChar);
                $filename    = trim(substr($contents, 17, ($eolPos - 17)));
                $contents    = substr($contents, ($eolPos + strlen($this->eolChar)));
                $this->path = $filename;
            }
        }
*/
        $this->parse();

        $this->fixer->startFile($this);

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** START TOKEN PROCESSING ***".PHP_EOL;
        }

        $foundCode        = false;
        #$listeners        = $this->ruleset->getSniffs();
        $listenerIgnoreTo = array();
        $inTests          = defined('PHP_CODESNIFFER_IN_TESTS');

        // Foreach of the listeners that have registered to listen for this
        // token, get them to process it.
        foreach ($this->tokens as $stackPtr => $token) {
            // Check for ignored lines.
            if ($token['code'] === T_COMMENT
                || $token['code'] === T_DOC_COMMENT
                || ($inTests === true && $token['code'] === T_INLINE_HTML)
            ) {
                if (strpos($token['content'], '@codingStandards') !== false) {
                    if (strpos($token['content'], '@codingStandardsIgnoreFile') !== false) {
                        // Ignoring the whole file, just a little late.
                        $this->errors       = array();
                        $this->warnings     = array();
                        $this->errorCount   = 0;
                        $this->warningCount = 0;
                        $this->fixableCount = 0;
                        return;
                    } else if (strpos($token['content'], '@codingStandardsChangeSetting') !== false) {
                        $start         = strpos($token['content'], '@codingStandardsChangeSetting');
                        $comment       = substr($token['content'], ($start + 30));
                        $parts         = explode(' ', $comment);
                        $sniffParts    = explode('.', $parts[0]);
                        $listenerClass = 'PHP_CodeSniffer\Standards\\'.$sniffParts[0].'\Sniffs\\'.$sniffParts[1].'\\'.$sniffParts[2].'Sniff';
                        $this->ruleset->setSniffProperty($listenerClass, $parts[1], $parts[2]);
                    }//end if
                }//end if
            }//end if

            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                $type    = $token['type'];
                $content = Util\Common::prepareForOutput($token['content']);
                echo "\t\tProcess token $stackPtr: $type => $content".PHP_EOL;
            }

            if ($token['code'] !== T_INLINE_HTML) {
                $foundCode = true;
            }

            if (isset($this->ruleset->tokenListeners[$token['code']]) === false) {
                continue;
            }

            foreach ($this->ruleset->tokenListeners[$token['code']] as $listenerData) {
                if (isset($this->ignoredListeners[$listenerData['class']]) === true
                    || (isset($listenerIgnoreTo[$listenerData['class']]) === true
                    && $listenerIgnoreTo[$listenerData['class']] > $stackPtr)
                ) {
                    // This sniff is ignoring past this token, or the whole file.
                    continue;
                }

                // Make sure this sniff supports the tokenizer
                // we are currently using.
                $class = $listenerData['class'];

                if (isset($listenerData['tokenizers'][$this->tokenizerType]) === false) {
                    continue;
                }

                // If the file path matches one of our ignore patterns, skip it.
                // While there is support for a type of each pattern
                // (absolute or relative) we don't actually support it here.
                foreach ($listenerData['ignore'] as $pattern) {
                    // We assume a / directory separator, as do the exclude rules
                    // most developers write, so we need a special case for any system
                    // that is different.
                    if (DIRECTORY_SEPARATOR === '\\') {
                        $pattern = str_replace('/', '\\\\', $pattern);
                    }

                    $pattern = '`'.$pattern.'`i';
                    if (preg_match($pattern, $this->path) === 1) {
                        $this->ignoredListeners[$class] = true;
                        continue(2);
                    }
                }

                $this->activeListener = $class;

                if (PHP_CODESNIFFER_VERBOSITY > 2) {
                    $startTime = microtime(true);
                    echo "\t\t\tProcessing ".$this->activeListener.'... ';
                }

                $ignoreTo = $this->ruleset->sniffs[$class]->process($this, $stackPtr);
                if ($ignoreTo !== null) {
                    $listenerIgnoreTo[$this->activeListener] = $ignoreTo;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 2) {
                    $timeTaken = (microtime(true) - $startTime);
                    if (isset($this->listenerTimes[$this->activeListener]) === false) {
                        $this->listenerTimes[$this->activeListener] = 0;
                    }

                    $this->listenerTimes[$this->activeListener] += $timeTaken;

                    $timeTaken = round(($timeTaken), 4);
                    echo "DONE in $timeTaken seconds".PHP_EOL;
                }

                $this->activeListener = '';
            }//end foreach
        }//end foreach

        if ($this->recordErrors === false) {
            $this->errors   = array();
            $this->warnings = array();
        }

        // If short open tags are off but the file being checked uses
        // short open tags, the whole content will be inline HTML
        // and nothing will be checked. So try and handle this case.
        if ($foundCode === false && $this->tokenizerType === 'PHP') {
            $shortTags = (bool) ini_get('short_open_tag');
            if ($shortTags === false) {
                $error = 'No PHP code was found in this file and short open tags are not allowed by this install of PHP. This file may be using short open tags but PHP does not allow them.';
                $this->addWarning($error, null, 'Internal.NoCodeFound');
            }
        }

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** END TOKEN PROCESSING ***".PHP_EOL;
            echo "\t*** START SNIFF PROCESSING REPORT ***".PHP_EOL;

            asort($this->listenerTimes, SORT_NUMERIC);
            $this->listenerTimes = array_reverse($this->listenerTimes, true);
            foreach ($this->listenerTimes as $listener => $timeTaken) {
                echo "\t$listener: ".round(($timeTaken), 4).' secs'.PHP_EOL;
            }

            echo "\t*** END SNIFF PROCESSING REPORT ***".PHP_EOL;
        }

        // We don't need these any more.
        #$this->listenerTimes = null;
        #$this->content = null;
        #$this->tokens = null;
        #$this->tokenizer = null;

    }//end process()


    /**
     * Tokenizes the file and prepares it for the test run.
     *
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     */
    protected function parse()
    {
        if (empty($this->tokens) === false) {
            // File has already been parsed.
            return;
        }

        try {
            $tokenizerClass = 'PHP_CodeSniffer\Tokenizers\\'.$this->tokenizerType;
            $this->tokenizer = new $tokenizerClass($this->content, $this->config, $this->eolChar);
            $this->tokens = $this->tokenizer->getTokens();
        } catch (TokenizerException $e) {
            $this->addWarning($e->getMessage(), null, 'Internal.Tokenizer.Exception');
            if (PHP_CODESNIFFER_VERBOSITY > 0 || (PHP_CODESNIFFER_CBF === true && empty($this->config->files) === false)) {
                echo "[$this->tokenizerType => tokenizer error]... ";
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo PHP_EOL;
                }
            }

            return;
        }

        $this->numTokens = count($this->tokens);

        // Check for mixed line endings as these can cause tokenizer errors and we
        // should let the user know that the results they get may be incorrect.
        // This is done by removing all backslashes, removing the newline char we
        // detected, then converting newlines chars into text. If any backslashes
        // are left at the end, we have additional newline chars in use.
        $contents = str_replace('\\', '', $this->content);
        $contents = str_replace($this->eolChar, '', $contents);
        $contents = str_replace("\n", '\n', $contents);
        $contents = str_replace("\r", '\r', $contents);
        if (strpos($contents, '\\') !== false) {
            $error = 'File has mixed line endings; this may cause incorrect results';
            $this->addWarningOnLine($error, 1, 'Internal.LineEndings.Mixed');
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0 || (PHP_CODESNIFFER_CBF === true && empty($this->config->files) === false)) {
            if ($this->numTokens === 0) {
                $numLines = 0;
            } else {
                $numLines = $this->tokens[($this->numTokens - 1)]['line'];
            }

            echo "[$this->tokenizerType => $this->numTokens tokens in $numLines lines]... ";
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

    }//end parse()


    /**
     * Returns the token stack for this file.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;

    }//end getTokens()


    /**
     * Remove vars stored in this file that are no longer required.
     *
     * @return void
     */
    public function cleanUp()
    {
        $this->listenerTimes = null;
        $this->content = null;
        $this->tokens = null;
        $this->tokenizer = null;
        $this->fixer = null;

    }//end cleanUp()


    /**
     * Records an error against a specific token in the file.
     *
     * @param string  $error    The error message.
     * @param int     $stackPtr The stack position where the error occurred.
     * @param string  $code     A violation code unique to the sniff message.
     * @param array   $data     Replacements for the error message.
     * @param int     $severity The severity level for this error. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable  Can the error be fixed by the sniff?
     *
     * @return boolean
     */
    public function addError(
        $error,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0,
        $fixable=false
    ) {
        if ($stackPtr === null) {
            $line   = 1;
            $column = 1;
        } else {
            $line   = $this->tokens[$stackPtr]['line'];
            $column = $this->tokens[$stackPtr]['column'];
        }

        return $this->_addError($error, $line, $column, $code, $data, $severity, $fixable);

    }//end addError()


    /**
     * Records a warning against a specific token in the file.
     *
     * @param string  $warning  The error message.
     * @param int     $stackPtr The stack position where the error occurred.
     * @param string  $code     A violation code unique to the sniff message.
     * @param array   $data     Replacements for the warning message.
     * @param int     $severity The severity level for this warning. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable  Can the warning be fixed by the sniff?
     *
     * @return boolean
     */
    public function addWarning(
        $warning,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0,
        $fixable=false
    ) {
        if ($stackPtr === null) {
            $line   = 1;
            $column = 1;
        } else {
            $line   = $this->tokens[$stackPtr]['line'];
            $column = $this->tokens[$stackPtr]['column'];
        }

        return $this->_addWarning($warning, $line, $column, $code, $data, $severity, $fixable);

    }//end addWarning()


    /**
     * Records an error against a specific line in the file.
     *
     * @param string $error    The error message.
     * @param int    $line     The line on which the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the error message.
     * @param int    $severity The severity level for this error. A value of 0 will be converted into the default severity level.
     *                          will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addErrorOnLine(
        $error,
        $line,
        $code='',
        $data=array(),
        $severity=0
    ) {
        return $this->addError($error, $line, 1, $code, $data, $severity, false);

    }//end addErrorOnLine()


    /**
     * Records a warning against a specific token in the file.
     *
     * @param string $warning  The error message.
     * @param int    $line     The line on which the warning occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the warning message.
     * @param int    $severity The severity level for this warning. A value of 0 will be converted into the default severity level.
     *                          will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addWarningOnLine(
        $warning,
        $line,
        $code='',
        $data=array(),
        $severity=0
    ) {
        return $this->addWarning($warning, $line, 1, $code, $data, $severity, false);

    }//end addWarningOnLine()


    /**
     * Records a fixable error against a specific token in the file.
     *
     * Returns true if the error was recorded and should be fixed.
     *
     * @param string $error    The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the error message.
     * @param int    $severity The severity level for this error. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addFixableError(
        $error,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0
    ) {
        $recorded = $this->addError($error, $stackPtr, $code, $data, $severity, true);
        if ($recorded === true && $this->fixer->enabled === true) {
            return true;
        }

        return false;

    }//end addFixableError()


    /**
     * Records a fixable warning against a specific token in the file.
     *
     * Returns true if the warning was recorded and should be fixed.
     *
     * @param string $warning  The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the warning message.
     * @param int    $severity The severity level for this warning. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addFixableWarning(
        $warning,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0
    ) {
        $recorded = $this->addWarning($warning, $stackPtr, $code, $data, $severity, true);
        if ($recorded === true && $this->fixer->enabled === true) {
            return true;
        }

        return false;

    }//end addFixableWarning()


    /**
     * Adds an error to the error stack.
     *
     * @param string  $error    The error message.
     * @param int     $line     The line on which the error occurred.
     * @param int     $column   The column at which the error occurred.
     * @param string  $code     A violation code unique to the sniff message.
     * @param array   $data     Replacements for the error message.
     * @param int     $severity The severity level for this error. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable  Can the error be fixed by the sniff?
     *
     * @return boolean
     */
    protected function _addError($error, $line, $column, $code, $data, $severity, $fixable)
    {
        if (isset($this->tokenizer->ignoredLines[$line]) === true) {
            return false;
        }

        // Work out which sniff generated the error.
        if (substr($code, 0, 9) === 'Internal.') {
            // Any internal message.
            $sniff     = $code;
            $sniffCode = $code;
        } else {
            $parts = explode('\\', $this->activeListener);
            if (isset($parts[5]) === true) {
                $sniff = $parts[2].'.'.$parts[4].'.'.$parts[5];

                // Remove "Sniff" from the end.
                $sniff = substr($sniff, 0, -5);
            } else {
                $sniff = 'unknownSniff';
            }

            $sniffCode = $sniff;
            if ($code !== '') {
                $sniffCode .= '.'.$code;
            }
        }//end if

        // If we know this sniff code is being ignored for this file, return early.
        if (isset($this->ignoredCodes[$sniffCode]) === true) {
            return false;
        }

        // Make sure this message type has not been set to "warning".
        if (isset($this->ruleset->ruleset[$sniffCode]['type']) === true
            && $this->ruleset->ruleset[$sniffCode]['type'] === 'warning'
        ) {
            // Pass this off to the warning handler.
            return $this->addWarning($error, $line, $column, $code, $data, $severity, $fixable);
        } else if ($this->config->errorSeverity === 0) {
            // Don't bother doing any processing as errors are just going to
            // be hidden in the reports anyway.
            return false;
        }

        // Make sure we are interested in this severity level.
        if (isset($this->ruleset->ruleset[$sniffCode]['severity']) === true) {
            $severity = $this->ruleset->ruleset[$sniffCode]['severity'];
        } else if ($severity !== 0 && $this->config->errorSeverity > $severity) {
            return false;
        }

        // Make sure we are not ignoring this file.
        $patterns = $this->ruleset->getIgnorePatterns($sniffCode);
        foreach ($patterns as $pattern => $type) {
            // While there is support for a type of each pattern
            // (absolute or relative) we don't actually support it here.
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            // We assume a / directory separator, as do the exclude rules
            // most developers write, so we need a special case for any system
            // that is different.
            if (DIRECTORY_SEPARATOR === '\\') {
                $replacements['/'] = '\\\\';
            }

            $pattern = '`'.strtr($pattern, $replacements).'`i';
            if (preg_match($pattern, $this->path) === 1) {
                $this->ignoredCodes[$sniffCode] = true;
                return false;
            }
        }//end foreach

        $this->errorCount++;
        if ($fixable === true) {
            $this->fixableCount++;
        }

        if ($this->recordErrors === false) {
            if (isset($this->errors[$line]) === false) {
                $this->errors[$line] = 0;
            }

            $this->errors[$line]++;
            return true;
        }

        // Work out the error message.
        if (isset($this->ruleset->ruleset[$sniffCode]['message']) === true) {
            $error = $this->ruleset->ruleset[$sniffCode]['message'];
        }

        if (empty($data) === true) {
            $message = $error;
        } else {
            $message = vsprintf($error, $data);
        }

        if (isset($this->errors[$line]) === false) {
            $this->errors[$line] = array();
        }

        if (isset($this->errors[$line][$column]) === false) {
            $this->errors[$line][$column] = array();
        }

        $this->errors[$line][$column][] = array(
                                            'message'  => $message,
                                            'source'   => $sniffCode,
                                            'severity' => $severity,
                                            'fixable'  => $fixable,
                                           );

        if (PHP_CODESNIFFER_VERBOSITY > 1
            && $this->fixer->enabled === true
            && $fixable === true
        ) {
            @ob_end_clean();
            echo "\tE: [Line $line] $message ($sniffCode)".PHP_EOL;
            ob_start();
        }

        return true;

    }//end _addError()


    /**
     * Adds an warning to the warning stack.
     *
     * @param string  $warning  The error message.
     * @param int     $line     The line on which the warning occurred.
     * @param int     $column   The column at which the warning occurred.
     * @param string  $code     A violation code unique to the sniff message.
     * @param array   $data     Replacements for the warning message.
     * @param int     $severity The severity level for this warning. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable  Can the warning be fixed by the sniff?
     *
     * @return boolean
     */
    protected function _addWarning($warning, $line, $column, $code, $data, $severity, $fixable)
    {
        if (isset($this->tokenizer->ignoredLines[$line]) === true) {
            return false;
        }

        // Work out which sniff generated the warning.
        if (substr($code, 0, 9) === 'Internal.') {
            // Any internal message.
            $sniff     = $code;
            $sniffCode = $code;
        } else {
            $parts = explode('\\', $this->activeListener);
            if (isset($parts[5]) === true) {
                $sniff = $parts[2].'.'.$parts[4].'.'.$parts[5];

                // Remove "Sniff" from the end.
                $sniff = substr($sniff, 0, -5);
            } else {
                $sniff = 'unknownSniff';
            }

            $sniffCode = $sniff;
            if ($code !== '') {
                $sniffCode .= '.'.$code;
            }
        }//end if

        // If we know this sniff code is being ignored for this file, return early.
        if (isset($this->ignoredCodes[$sniffCode]) === true) {
            return false;
        }

        // Make sure this message type has not been set to "error".
        if (isset($this->ruleset->ruleset[$sniffCode]['type']) === true
            && $this->ruleset->ruleset[$sniffCode]['type'] === 'error'
        ) {
            // Pass this off to the error handler.
            return $this->addError($warning, $line, $column, $code, $data, $severity, $fixable);
        } else if ($this->config->warningSeverity === 0) {
            // Don't bother doing any processing as warnings are just going to
            // be hidden in the reports anyway.
            return false;
        }

        // Make sure we are interested in this severity level.
        if (isset($this->ruleset->ruleset[$sniffCode]['severity']) === true) {
            $severity = $this->ruleset->ruleset[$sniffCode]['severity'];
        } else if ($severity !== 0 && $this->config->warningSeverity > $severity) {
            return false;
        }

        // Make sure we are not ignoring this file.
        $patterns = $this->ruleset->getIgnorePatterns($sniffCode);
        foreach ($patterns as $pattern => $type) {
            // While there is support for a type of each pattern
            // (absolute or relative) we don't actually support it here.
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            // We assume a / directory separator, as do the exclude rules
            // most developers write, so we need a special case for any system
            // that is different.
            if (DIRECTORY_SEPARATOR === '\\') {
                $replacements['/'] = '\\\\';
            }

            $pattern = '`'.strtr($pattern, $replacements).'`i';
            if (preg_match($pattern, $this->path) === 1) {
                $this->ignoredCodes[$sniffCode] = true;
                return false;
            }
        }//end foreach

        $this->warningCount++;
        if ($fixable === true) {
            $this->fixableCount++;
        }

        if ($this->recordErrors === false) {
            if (isset($this->warnings[$line]) === false) {
                $this->warnings[$line] = 0;
            }

            $this->warnings[$line]++;
            return true;
        }

        // Work out the warning message.
        if (isset($this->ruleset->ruleset[$sniffCode]['message']) === true) {
            $warning = $this->ruleset->ruleset[$sniffCode]['message'];
        }

        if (empty($data) === true) {
            $message = $warning;
        } else {
            $message = vsprintf($warning, $data);
        }

        if (isset($this->warnings[$line]) === false) {
            $this->warnings[$line] = array();
        }

        if (isset($this->warnings[$line][$column]) === false) {
            $this->warnings[$line][$column] = array();
        }

        $this->warnings[$line][$column][] = array(
                                              'message'  => $message,
                                              'source'   => $sniffCode,
                                              'severity' => $severity,
                                              'fixable'  => $fixable,
                                             );

        if (PHP_CODESNIFFER_VERBOSITY > 1
            && $this->fixer->enabled === true
            && $fixable === true
        ) {
            @ob_end_clean();
            echo "\tW: $message ($sniffCode)".PHP_EOL;
            ob_start();
        }

        return true;

    }//end _addWarning()


    /**
     * Adds an warning to the warning stack.
     *
     * @param int    $stackPtr The stack position where the metric was recorded.
     * @param string $metric   The name of the metric being recorded.
     * @param string $value    The value of the metric being recorded.
     *
     * @return boolean
     */
    public function recordMetric($stackPtr, $metric, $value)
    {
        if (isset($this->metrics[$metric]) === false) {
            $this->metrics[$metric] = array(
                                        'values' => array(
                                                     $value => array($stackPtr),
                                                    ),
                                       );
        } else {
            if (isset($this->metrics[$metric]['values'][$value]) === false) {
                $this->metrics[$metric]['values'][$value] = array($stackPtr);
            } else {
                $this->metrics[$metric]['values'][$value][] = $stackPtr;
            }
        }

        return true;

    }//end recordMetric()


    /**
     * Returns the number of errors raised.
     *
     * @return int
     */
    public function getErrorCount()
    {
        return $this->errorCount;

    }//end getErrorCount()


    /**
     * Returns the number of warnings raised.
     *
     * @return int
     */
    public function getWarningCount()
    {
        return $this->warningCount;

    }//end getWarningCount()


    /**
     * Returns the number of successes recorded.
     *
     * @return int
     */
    public function getSuccessCount()
    {
        return $this->successCount;

    }//end getSuccessCount()


    /**
     * Returns the number of fixable errors/warnings raised.
     *
     * @return int
     */
    public function getFixableCount()
    {
        return $this->fixableCount;

    }//end getFixableCount()


    /**
     * Returns the list of ignored lines.
     *
     * @return array
     */
    public function getIgnoredLines()
    {
        exit('not done');
        return self::$ignoredLines;

    }//end getIgnoredLines()


    /**
     * Returns the errors raised from processing this file.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;

    }//end getErrors()


    /**
     * Returns the warnings raised from processing this file.
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;

    }//end getWarnings()


    /**
     * Returns the metrics found while processing this file.
     *
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;

    }//end getMetrics()


    /**
     * Returns the absolute filename of this file.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->path;

    }//end getFilename()



    /**
     * Returns the declaration names for T_CLASS, T_INTERFACE and T_FUNCTION tokens.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface or function.
     *
     * @return string|null The name of the class, interface or function.
     *                     or NULL if the function is a closure.
     * @throws PHP_CodeSniffer_Exception If the specified token is not of type
     *                                   T_FUNCTION, T_CLASS or T_INTERFACE.
     */
    public function getDeclarationName($stackPtr)
    {
        $tokenCode = $this->tokens[$stackPtr]['code'];
        if ($tokenCode !== T_FUNCTION
            && $tokenCode !== T_CLASS
            && $tokenCode !== T_INTERFACE
            && $tokenCode !== T_TRAIT
        ) {
            throw new RuntimeException('Token type "'.$this->tokens[$stackPtr]['type'].'" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');
        }

        if ($tokenCode === T_FUNCTION
            && $this->isAnonymousFunction($stackPtr) === true
        ) {
            return null;
        }

        for ($i = $stackPtr; $i < $this->numTokens; $i++) {
            if ($this->tokens[$i]['code'] === T_STRING) {
                break;
            }
        }

        return $this->tokens[$i]['content'];

    }//end getDeclarationName()


    /**
     * Check if the token at the specified position is a anonymous function.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface or function.
     *
     * @return boolean
     * @throws PHP_CodeSniffer_Exception If the specified token is not of type
     *                                   T_FUNCTION
     */
    public function isAnonymousFunction($stackPtr)
    {
        $tokenCode = $this->tokens[$stackPtr]['code'];
        if ($tokenCode !== T_FUNCTION) {
            throw new TokenizerException('Token type is not T_FUNCTION');
        }

        if (isset($this->tokens[$stackPtr]['parenthesis_opener']) === false) {
            // Something is not right with this function.
            return false;
        }

        $name = false;
        for ($i = ($stackPtr + 1); $i < $this->numTokens; $i++) {
            if ($this->tokens[$i]['code'] === T_STRING) {
                $name = $i;
                break;
            }
        }

        if ($name === false) {
            // No name found.
            return true;
        }

        $open = $this->tokens[$stackPtr]['parenthesis_opener'];
        if ($name > $open) {
            return true;
        }

        return false;

    }//end isAnonymousFunction()


    /**
     * Returns the method parameters for the specified T_FUNCTION token.
     *
     * Each parameter is in the following format:
     *
     * <code>
     *   0 => array(
     *         'name'              => '$var',  // The variable name.
     *         'pass_by_reference' => false,   // Passed by reference.
     *         'type_hint'         => string,  // Type hint for array or custom type
     *        )
     * </code>
     *
     * Parameters with default values have an additional array index of
     * 'default' with the value of the default as a string.
     *
     * @param int $stackPtr The position in the stack of the T_FUNCTION token
     *                      to acquire the parameters for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified $stackPtr is not of
     *                                   type T_FUNCTION.
     */
    public function getMethodParameters($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_FUNCTION) {
            throw new TokenizerException('$stackPtr must be of type T_FUNCTION');
        }

        $opener = $this->tokens[$stackPtr]['parenthesis_opener'];
        $closer = $this->tokens[$stackPtr]['parenthesis_closer'];

        $vars            = array();
        $currVar         = null;
        $defaultStart    = null;
        $paramCount      = 0;
        $passByReference = false;
        $typeHint        = '';

        for ($i = ($opener + 1); $i <= $closer; $i++) {
            // Check to see if this token has a parenthesis opener. If it does
            // its likely to be an array, which might have arguments in it, which
            // we cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($this->tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $this->tokens[$i]['parenthesis_closer']) {
                    $i = ($this->tokens[$i]['parenthesis_closer'] + 1);
                }
            }

            switch ($this->tokens[$i]['code']) {
            case T_BITWISE_AND:
                $passByReference = true;
                break;
            case T_VARIABLE:
                $currVar = $i;
                break;
            case T_ARRAY_HINT:
            case T_CALLABLE:
                $typeHint = $this->tokens[$i]['content'];
                break;
            case T_STRING:
                // This is a string, so it may be a type hint, but it could
                // also be a constant used as a default value.
                $prevComma = false;
                for ($t = $i; $t >= $opener; $t--) {
                    if ($this->tokens[$t]['code'] === T_COMMA) {
                        $prevComma = $t;
                        break;
                    }
                }

                if ($prevComma !== false) {
                    $nextEquals = false;
                    for ($t = $prevComma; $t < $i; $t++) {
                        if ($this->tokens[$t]['code'] === T_EQUAL) {
                            $nextEquals = $t;
                            break;
                        }
                    }

                    if ($nextEquals !== false) {
                        break;
                    }
                }

                if ($defaultStart === null) {
                    $typeHint .= $this->tokens[$i]['content'];
                }
                break;
            case T_NS_SEPARATOR:
                // Part of a type hint or default value.
                if ($defaultStart === null) {
                    $typeHint .= $this->tokens[$i]['content'];
                }
                break;
            case T_CLOSE_PARENTHESIS:
            case T_COMMA:
                // If it's null, then there must be no parameters for this
                // method.
                if ($currVar === null) {
                    continue;
                }

                $vars[$paramCount]         = array();
                $vars[$paramCount]['name'] = $this->tokens[$currVar]['content'];

                if ($defaultStart !== null) {
                    $vars[$paramCount]['default']
                        = $this->getTokensAsString(
                            $defaultStart,
                            ($i - $defaultStart)
                        );
                }

                $vars[$paramCount]['pass_by_reference'] = $passByReference;
                $vars[$paramCount]['type_hint']         = $typeHint;

                // Reset the vars, as we are about to process the next parameter.
                $defaultStart    = null;
                $passByReference = false;
                $typeHint        = '';

                $paramCount++;
                break;
            case T_EQUAL:
                $defaultStart = ($i + 1);
                break;
            }//end switch
        }//end for

        return $vars;

    }//end getMethodParameters()


    /**
     * Returns the visibility and implementation properties of a method.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'scope'           => 'public', // public protected or protected
     *    'scope_specified' => true,     // true is scope keyword was found.
     *    'is_abstract'     => false,    // true if the abstract keyword was found.
     *    'is_final'        => false,    // true if the final keyword was found.
     *    'is_static'       => false,    // true if the static keyword was found.
     *    'is_closure'      => false,    // true if no name is found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_FUNCTION token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_FUNCTION token.
     */
    public function getMethodProperties($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_FUNCTION) {
            throw new TokenizerException('$stackPtr must be of type T_FUNCTION');
        }

        $valid = array(
                  T_PUBLIC      => T_PUBLIC,
                  T_PRIVATE     => T_PRIVATE,
                  T_PROTECTED   => T_PROTECTED,
                  T_STATIC      => T_STATIC,
                  T_FINAL       => T_FINAL,
                  T_ABSTRACT    => T_ABSTRACT,
                  T_WHITESPACE  => T_WHITESPACE,
                  T_COMMENT     => T_COMMENT,
                  T_DOC_COMMENT => T_DOC_COMMENT,
                 );

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;
        $isClosure      = $this->isAnonymousFunction($stackPtr);

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->tokens[$i]['code']]) === false) {
                break;
            }

            switch ($this->tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_ABSTRACT:
                $isAbstract = true;
                break;
            case T_FINAL:
                $isFinal = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }//end switch
        }//end for

        return array(
                'scope'           => $scope,
                'scope_specified' => $scopeSpecified,
                'is_abstract'     => $isAbstract,
                'is_final'        => $isFinal,
                'is_static'       => $isStatic,
                'is_closure'      => $isClosure,
               );

    }//end getMethodProperties()


    /**
     * Returns the visibility and implementation properties of the class member
     * variable found at the specified position in the stack.
     *
     * The format of the array is:
     *
     * <code>
     *   array(
     *    'scope'       => 'public', // public protected or protected
     *    'is_static'   => false,    // true if the static keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_VARIABLE token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_VARIABLE token, or if the position is not
     *                                   a class member variable.
     */
    public function getMemberProperties($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_VARIABLE) {
            throw new TokenizerException('$stackPtr must be of type T_VARIABLE');
        }

        $conditions = array_keys($this->tokens[$stackPtr]['conditions']);
        $ptr        = array_pop($conditions);
        if (isset($this->tokens[$ptr]) === false
            || ($this->tokens[$ptr]['code'] !== T_CLASS
            && $this->tokens[$ptr]['code'] !== T_TRAIT)
        ) {
            if (isset($this->tokens[$ptr]) === true
                && $this->tokens[$ptr]['code'] === T_INTERFACE
            ) {
                // T_VARIABLEs in interfaces can actually be method arguments
                // but they wont be seen as being inside the method because there
                // are no scope openers and closers for abstract methods. If it is in
                // parentheses, we can be pretty sure it is a method argument.
                if (isset($this->tokens[$stackPtr]['nested_parenthesis']) === false
                    || empty($this->tokens[$stackPtr]['nested_parenthesis']) === true
                ) {
                    $error = 'Possible parse error: interfaces may not include member vars';
                    $this->addWarning($error, $stackPtr, 'Internal.ParseError.InterfaceHasMemberVar');
                    return array();
                }
            } else {
                throw new TokenizerException('$stackPtr is not a class member var');
            }
        }

        $valid = array(
                  T_PUBLIC      => T_PUBLIC,
                  T_PRIVATE     => T_PRIVATE,
                  T_PROTECTED   => T_PROTECTED,
                  T_STATIC      => T_STATIC,
                  T_WHITESPACE  => T_WHITESPACE,
                  T_COMMENT     => T_COMMENT,
                  T_DOC_COMMENT => T_DOC_COMMENT,
                  T_VARIABLE    => T_VARIABLE,
                  T_COMMA       => T_COMMA,
                 );

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->tokens[$i]['code']]) === false) {
                break;
            }

            switch ($this->tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }
        }//end for

        return array(
                'scope'           => $scope,
                'scope_specified' => $scopeSpecified,
                'is_static'       => $isStatic,
               );

    }//end getMemberProperties()


    /**
     * Returns the visibility and implementation properties of a class.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'is_abstract' => false, // true if the abstract keyword was found.
     *    'is_final'    => false, // true if the final keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_CLASS token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_CLASS token.
     */
    public function getClassProperties($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_CLASS) {
            throw new TokenizerException('$stackPtr must be of type T_CLASS');
        }

        $valid = array(
                  T_FINAL       => T_FINAL,
                  T_ABSTRACT    => T_ABSTRACT,
                  T_WHITESPACE  => T_WHITESPACE,
                  T_COMMENT     => T_COMMENT,
                  T_DOC_COMMENT => T_DOC_COMMENT,
                 );

        $isAbstract = false;
        $isFinal    = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->tokens[$i]['code']]) === false) {
                break;
            }

            switch ($this->tokens[$i]['code']) {
            case T_ABSTRACT:
                $isAbstract = true;
                break;

            case T_FINAL:
                $isFinal = true;
                break;
            }
        }//end for

        return array(
                'is_abstract' => $isAbstract,
                'is_final'    => $isFinal,
               );

    }//end getClassProperties()


    /**
     * Determine if the passed token is a reference operator.
     *
     * Returns true if the specified token position represents a reference.
     * Returns false if the token represents a bitwise operator.
     *
     * @param int $stackPtr The position of the T_BITWISE_AND token.
     *
     * @return boolean
     */
    public function isReference($stackPtr)
    {
        if ($this->tokens[$stackPtr]['code'] !== T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $this->findPrevious(
            Util\Tokens::$emptyTokens,
            ($stackPtr - 1),
            null,
            true
        );

        if ($this->tokens[$tokenBefore]['code'] === T_FUNCTION) {
            // Function returns a reference.
            return true;
        }

        if ($this->tokens[$tokenBefore]['code'] === T_DOUBLE_ARROW) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if ($this->tokens[$tokenBefore]['code'] === T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (isset(Util\Tokens::$assignmentTokens[$this->tokens[$tokenBefore]['code']]) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        if (isset($this->tokens[$stackPtr]['nested_parenthesis']) === true) {
            $brackets    = $this->tokens[$stackPtr]['nested_parenthesis'];
            $lastBracket = array_pop($brackets);
            if (isset($this->tokens[$lastBracket]['parenthesis_owner']) === true) {
                $owner = $this->tokens[$this->tokens[$lastBracket]['parenthesis_owner']];
                if ($owner['code'] === T_FUNCTION
                    || $owner['code'] === T_CLOSURE
                    || $owner['code'] === T_ARRAY
                ) {
                    // Inside a function or array declaration, this is a reference.
                    return true;
                }
            } else {
                $prev = false;
                for ($t = ($this->tokens[$lastBracket]['parenthesis_opener'] - 1); $t >= 0; $t--) {
                    if ($this->tokens[$t]['code'] !== T_WHITESPACE) {
                        $prev = $t;
                        break;
                    }
                }

                if ($prev !== false && $this->tokens[$prev]['code'] === T_USE) {
                    return true;
                }
            }//end if
        }//end if

        $tokenAfter = $this->findNext(
            Util\Tokens::$emptyTokens,
            ($stackPtr + 1),
            null,
            true
        );

        if ($this->tokens[$tokenAfter]['code'] === T_VARIABLE
            && ($this->tokens[$tokenBefore]['code'] === T_OPEN_PARENTHESIS
            || $this->tokens[$tokenBefore]['code'] === T_COMMA)
        ) {
            return true;
        }

        return false;

    }//end isReference()


    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified length.
     *
     * @param int $start  The position to start from in the token stack.
     * @param int $length The length of tokens to traverse from the start pos.
     *
     * @return string The token contents.
     */
    public function getTokensAsString($start, $length)
    {
        $str = '';
        $end = ($start + $length);
        if ($end > $this->numTokens) {
            $end = $this->numTokens;
        }

        for ($i = $start; $i < $end; $i++) {
            $str .= $this->tokens[$i]['content'];
        }

        return $str;

    }//end getTokensAsString()


    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack.
     * @param int       $end     The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the start of the token stack.
     * @param bool      $exclude If true, find the next token that are NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be checked. IE. checking will stop
     *                           at the next semi-colon found.
     *
     * @return int|bool
     * @see    findNext()
     */
    public function findPrevious(
        $types,
        $start,
        $end=null,
        $exclude=false,
        $value=null,
        $local=false
    ) {
        $types = (array) $types;

        if ($end === null) {
            $end = 0;
        }

        for ($i = $start; $i >= $end; $i--) {
            $found = (bool) $exclude;
            foreach ($types as $type) {
                if ($this->tokens[$i]['code'] === $type) {
                    $found = !$exclude;
                    break;
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->tokens[$i]['content'] === $value) {
                    return $i;
                }
            }

            if ($local === true) {
                if (isset($this->tokens[$i]['scope_opener']) === true
                    && $i === $this->tokens[$i]['scope_closer']
                ) {
                    $i = $this->tokens[$i]['scope_opener'];
                } else if (isset($this->tokens[$i]['bracket_opener']) === true
                    && $i === $this->tokens[$i]['bracket_closer']
                ) {
                    $i = $this->tokens[$i]['bracket_opener'];
                } else if (isset($this->tokens[$i]['parenthesis_opener']) === true
                    && $i === $this->tokens[$i]['parenthesis_closer']
                ) {
                    $i = $this->tokens[$i]['parenthesis_opener'];
                } else if ($this->tokens[$i]['code'] === T_SEMICOLON) {
                    break;
                }
            }
        }//end for

        return false;

    }//end findPrevious()


    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack.
     * @param int       $end     The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the end of the token stack.
     * @param bool      $exclude If true, find the next token that is NOT of
     *                           a type specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be checked. i.e., checking will stop
     *                           at the next semi-colon found.
     *
     * @return int|bool
     * @see    findPrevious()
     */
    public function findNext(
        $types,
        $start,
        $end=null,
        $exclude=false,
        $value=null,
        $local=false
    ) {
        $types = (array) $types;

        if ($end === null || $end > $this->numTokens) {
            $end = $this->numTokens;
        }

        for ($i = $start; $i < $end; $i++) {
            $found = (bool) $exclude;
            foreach ($types as $type) {
                if ($this->tokens[$i]['code'] === $type) {
                    $found = !$exclude;
                    break;
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->tokens[$i]['content'] === $value) {
                    return $i;
                }
            }

            if ($local === true && $this->tokens[$i]['code'] === T_SEMICOLON) {
                break;
            }
        }//end for

        return false;

    }//end findNext()


    /**
     * Returns the position of the first non-whitespace token in a statement.
     *
     * @param int $start The position to start searching from in the token stack.
     *
     * @return int
     */
    public function findStartOfStatement($start)
    {
        $endTokens = Util\Tokens::$blockOpeners;

        $endTokens[T_COLON]            = true;
        $endTokens[T_COMMA]            = true;
        $endTokens[T_DOUBLE_ARROW]     = true;
        $endTokens[T_SEMICOLON]        = true;
        $endTokens[T_OPEN_TAG]         = true;
        $endTokens[T_CLOSE_TAG]        = true;
        $endTokens[T_OPEN_SHORT_ARRAY] = true;

        $lastNotEmpty = $start;

        for ($i = $start; $i >= 0; $i--) {
            if (isset($endTokens[$this->tokens[$i]['code']]) === true) {
                // Found the end of the previous statement.
                return $lastNotEmpty;
            }

            // Skip nested statements.
            if (isset($this->tokens[$i]['scope_opener']) === true
                && $i === $this->tokens[$i]['scope_closer']
            ) {
                $i = $this->tokens[$i]['scope_opener'];
            } else if (isset($this->tokens[$i]['bracket_opener']) === true
                && $i === $this->tokens[$i]['bracket_closer']
            ) {
                $i = $this->tokens[$i]['bracket_opener'];
            } else if (isset($this->tokens[$i]['parenthesis_opener']) === true
                && $i === $this->tokens[$i]['parenthesis_closer']
            ) {
                $i = $this->tokens[$i]['parenthesis_opener'];
            }

            if (isset(Util\Tokens::$emptyTokens[$this->tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }//end for

        return 0;

    }//end findStartOfStatement()


    /**
     * Returns the position of the last non-whitespace token in a statement.
     *
     * @param int $start The position to start searching from in the token stack.
     *
     * @return int
     */
    public function findEndOfStatement($start)
    {
        $endTokens = array(
                      T_COLON                => true,
                      T_COMMA                => true,
                      T_DOUBLE_ARROW         => true,
                      T_SEMICOLON            => true,
                      T_CLOSE_PARENTHESIS    => true,
                      T_CLOSE_SQUARE_BRACKET => true,
                      T_CLOSE_CURLY_BRACKET  => true,
                      T_CLOSE_SHORT_ARRAY    => true,
                      T_OPEN_TAG             => true,
                      T_CLOSE_TAG            => true,
                     );

        $lastNotEmpty = $start;

        for ($i = $start; $i < $this->numTokens; $i++) {
            if ($i !== $start && isset($endTokens[$this->tokens[$i]['code']]) === true) {
                // Found the end of the statement.
                if ($this->tokens[$i]['code'] === T_CLOSE_PARENTHESIS
                    || $this->tokens[$i]['code'] === T_CLOSE_SQUARE_BRACKET
                    || $this->tokens[$i]['code'] === T_CLOSE_CURLY_BRACKET
                    || $this->tokens[$i]['code'] === T_OPEN_TAG
                    || $this->tokens[$i]['code'] === T_CLOSE_TAG
                ) {
                    return $lastNotEmpty;
                }

                return $i;
            }

            // Skip nested statements.
            if (isset($this->tokens[$i]['scope_closer']) === true
                && $i === $this->tokens[$i]['scope_opener']
            ) {
                $i = $this->tokens[$i]['scope_closer'];
            } else if (isset($this->tokens[$i]['bracket_closer']) === true
                && $i === $this->tokens[$i]['bracket_opener']
            ) {
                $i = $this->tokens[$i]['bracket_closer'];
            } else if (isset($this->tokens[$i]['parenthesis_closer']) === true
                && $i === $this->tokens[$i]['parenthesis_opener']
            ) {
                $i = $this->tokens[$i]['parenthesis_closer'];
            }

            if (isset(Util\Tokens::$emptyTokens[$this->tokens[$i]['code']]) === false) {
                $lastNotEmpty = $i;
            }
        }//end for

        return ($this->numTokens - 1);

    }//end findEndOfStatement()


    /**
     * Returns the position of the first token on a line, matching given type.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack. The first token matching on
     *                           this line before this token will be returned.
     * @param bool      $exclude If true, find the token that is NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token must be equal to.
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     *
     * @return int | bool
     */
    public function findFirstOnLine($types, $start, $exclude=false, $value=null)
    {
        if (is_array($types) === false) {
            $types = array($types);
        }

        $foundToken = false;

        for ($i = $start; $i >= 0; $i--) {
            if ($this->tokens[$i]['line'] < $this->tokens[$start]['line']) {
                break;
            }

            $found = $exclude;
            foreach ($types as $type) {
                if ($exclude === false) {
                    if ($this->tokens[$i]['code'] === $type) {
                        $found = true;
                        break;
                    }
                } else {
                    if ($this->tokens[$i]['code'] === $type) {
                        $found = false;
                        break;
                    }
                }
            }

            if ($found === true) {
                if ($value === null) {
                    $foundToken = $i;
                } else if ($this->tokens[$i]['content'] === $value) {
                    $foundToken = $i;
                }
            }
        }//end for

        return $foundToken;

    }//end findFirstOnLine()


    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * @param int       $stackPtr The position of the token we are checking.
     * @param int|array $types    The type(s) of tokens to search for.
     *
     * @return boolean
     */
    public function hasCondition($stackPtr, $types)
    {
        // Check for the existence of the token.
        if (isset($this->tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $types      = (array) $types;
        $conditions = $this->tokens[$stackPtr]['conditions'];

        foreach ($types as $type) {
            if (in_array($type, $conditions) === true) {
                // We found a token with the required type.
                return true;
            }
        }

        return false;

    }//end hasCondition()


    /**
     * Return the position of the condition for the passed token.
     *
     * Returns FALSE if the token does not have the condition.
     *
     * @param int $stackPtr The position of the token we are checking.
     * @param int $type     The type of token to search for.
     *
     * @return int
     */
    public function getCondition($stackPtr, $type)
    {
        // Check for the existence of the token.
        if (isset($this->tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $conditions = $this->tokens[$stackPtr]['conditions'];
        foreach ($conditions as $token => $condition) {
            if ($condition === $type) {
                return $token;
            }
        }

        return false;

    }//end getCondition()


    /**
     * Returns the name of the class that the specified class extends.
     *
     * Returns FALSE on error or if there is no extended class name.
     *
     * @param int $stackPtr The stack position of the class.
     *
     * @return string
     */
    public function findExtendedClassName($stackPtr)
    {
        // Check for the existence of the token.
        if (isset($this->tokens[$stackPtr]) === false) {
            return false;
        }

        if ($this->tokens[$stackPtr]['code'] !== T_CLASS) {
            return false;
        }

        if (isset($this->tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classCloserIndex = $this->tokens[$stackPtr]['scope_closer'];
        $extendsIndex     = $this->findNext(T_EXTENDS, $stackPtr, $classCloserIndex);
        if (false === $extendsIndex) {
            return false;
        }

        $find = array(
                 T_NS_SEPARATOR,
                 T_STRING,
                 T_WHITESPACE,
                );

        $end  = $this->findNext($find, ($extendsIndex + 1), $classCloserIndex, true);
        $name = $this->getTokensAsString(($extendsIndex + 1), ($end - $extendsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        return $name;

    }//end findExtendedClassName()


}//end class
