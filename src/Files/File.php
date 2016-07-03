<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Files;

use PHP_CodeSniffer\Files\File as BaseFile;

use Symplify\PHP7_CodeSniffer\Exceptions\TokenizerException;
use Symplify\PHP7_CodeSniffer\Fixer;
use Symplify\PHP7_CodeSniffer\Ruleset;
use Symplify\PHP7_CodeSniffer\Configuration;
use Symplify\PHP7_CodeSniffer\Tokenizers\PHP;
use Symplify\PHP7_CodeSniffer\Util\Common;

final class File extends BaseFile
{
    public function __construct(string $path, Ruleset $ruleset, Fixer $fixer)
    {
        $this->path = $path;
        $this->ruleset = $ruleset;
        $this->fixer = $fixer;

        $this->tokenizerType = 'PHP';
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        if (empty($this->tokens) === false) {
            // File has already been parsed.
            return;
        }

        try {
            $this->tokenizer = new PHP($this->content, $this->eolChar);
            $this->tokens = $this->tokenizer->getTokens();
        } catch (TokenizerException $e) {
            $this->addWarning($e->getMessage(), null, 'Internal.Tokenizer.Exception');
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
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $this->errors = [];
        $this->warnings = [];
        $this->errorCount   = 0;
        $this->warningCount = 0;
        $this->fixableCount = 0;

        $this->parse();

        $this->fixer->startFile($this);

        $foundCode = false;
        $listenerIgnoreTo = [];

        // Foreach of the listeners that have registered to listen for this
        // token, get them to process it.
        foreach ($this->tokens as $stackPtr => $token) {
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

                // If the file path does not match one of our include patterns, skip it.
                // While there is support for a type of each pattern
                // (absolute or relative) we don't actually support it here.
                foreach ($listenerData['include'] as $pattern) {
                    // We assume a / directory separator, as do the exclude rules
                    // most developers write, so we need a special case for any system
                    // that is different.
                    if (DIRECTORY_SEPARATOR === '\\') {
                        $pattern = str_replace('/', '\\\\', $pattern);
                    }

                    $pattern = '`'.$pattern.'`i';
                    if (preg_match($pattern, $this->path) !== 1) {
                        $this->ignoredListeners[$class] = true;
                        continue(2);
                    }
                }

                $this->activeListener = $class;

                $ignoreTo = $this->ruleset->sniffs[$class]->process($this, $stackPtr);
                if ($ignoreTo !== null) {
                    $listenerIgnoreTo[$this->activeListener] = $ignoreTo;
                }

                $this->activeListener = '';
            }//end foreach
        }//end foreach

        // If short open tags are off but the file being checked uses
        // short open tags, the whole content will be inline HTML
        // and nothing will be checked. So try and handle this case.
        if ($foundCode === false) {
            $shortTags = (bool) ini_get('short_open_tag');
            if ($shortTags === false) {
                $error = 'No PHP code was found in this file and short open tags are not allowed by this install of PHP. This file may be using short open tags but PHP does not allow them.';
                $this->addWarning($error, null, 'Internal.NoCodeFound');
            }
        }
    }

    /**
     * Adds an error to the error stack.
     *
     * @param boolean $error    Is this an error message?
     * @param string  $message  The text of the message.
     * @param int     $line     The line on which the message occurred.
     * @param int     $column   The column at which the message occurred.
     * @param string  $code     A violation code unique to the sniff message.
     * @param array   $data     Replacements for the message.
     * @param int     $severity The severity level for this message. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable  Can the problem be fixed by the sniff?
     *
     * @return boolean
     */
    protected function addMessage($error, $message, $line, $column, $code, $data, $severity, $fixable)
    {
        if (isset($this->tokenizer->ignoredLines[$line]) === true) {
            return false;
        }

        $includeAll = true;

        // Work out which sniff generated the message.
        $parts = explode('.', $code);
        if ($parts[0] === 'Internal') {
            // An internal message.
            $listenerCode = Common::getSniffCode($this->activeListener);
            $sniffCode    = $code;
            $checkCodes   = array($sniffCode);
        } else {
            if ($parts[0] !== $code) {
                // The full message code has been passed in.
                $sniffCode    = $code;
                $listenerCode = substr($sniffCode, 0, strrpos($sniffCode, '.'));
            } else {
                $listenerCode = Common::getSniffCode($this->activeListener);
                $sniffCode    = $listenerCode.'.'.$code;
                $parts        = explode('.', $sniffCode);
            }

            $checkCodes = array(
                $sniffCode,
                $parts[0].'.'.$parts[1].'.'.$parts[2],
                $parts[0].'.'.$parts[1],
                $parts[0],
            );
        }//end if

        // Filter out any messages for sniffs that shouldn't have run
        // due to the use of the --sniffs command line argument.
        if ($includeAll === false
            && ((empty($this->configCache['sniffs']) === false
                    && in_array($listenerCode, $this->configCache['sniffs']) === false)
                || (empty($this->configCache['exclude']) === false
                    && in_array($listenerCode, $this->configCache['exclude']) === true))
        ) {
            return false;
        }

        // If we know this sniff code is being ignored for this file, return early.
        foreach ($checkCodes as $checkCode) {
            if (isset($this->ignoredCodes[$checkCode]) === true) {
                return false;
            }
        }

        $oppositeType = 'warning';
        if ($error === false) {
            $oppositeType = 'error';
        }

        foreach ($checkCodes as $checkCode) {
            // Make sure this message type has not been set to the opposite message type.
            if (isset($this->ruleset->ruleset[$checkCode]['type']) === true
                && $this->ruleset->ruleset[$checkCode]['type'] === $oppositeType
            ) {
                $error = !$error;
                break;
            }
        }

        $severity = $configSeverity = 5;
        if ($error === true) {
            $messageCount   = &$this->errorCount;
            $messages       = &$this->errors;
        } else {
            $messageCount   = &$this->warningCount;
            $messages       = &$this->warnings;
        }

        foreach ($checkCodes as $checkCode) {
            // Make sure we are interested in this severity level.
            if (isset($this->ruleset->ruleset[$checkCode]['severity']) === true) {
                $severity = $this->ruleset->ruleset[$checkCode]['severity'];
                break;
            }
        }

        if ($includeAll === false && $configSeverity > $severity) {
            return false;
        }

        // Make sure we are not ignoring this file.
        foreach ($checkCodes as $checkCode) {
            if (isset($this->configCache['ignorePatterns'][$checkCode]) === false) {
                continue;
            }

            foreach ($this->configCache['ignorePatterns'][$checkCode] as $pattern => $type) {
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
                    $this->ignoredCodes[$checkCode] = true;
                    return false;
                }
            }//end foreach
        }//end foreach

        $messageCount++;
        if ($fixable === true) {
            $this->fixableCount++;
        }

        // Work out the error message.
        if (isset($this->ruleset->ruleset[$sniffCode]['message']) === true) {
            $message = $this->ruleset->ruleset[$sniffCode]['message'];
        }

        if (empty($data) === false) {
            $message = vsprintf($message, $data);
        }

        if (isset($messages[$line]) === false) {
            $messages[$line] = array();
        }

        if (isset($messages[$line][$column]) === false) {
            $messages[$line][$column] = array();
        }

        $messages[$line][$column][] = array(
            'message'  => $message,
            'source'   => $sniffCode,
            'listener' => $this->activeListener,
            'severity' => $severity,
            'fixable'  => $fixable,
        );

        return true;
    }
}
