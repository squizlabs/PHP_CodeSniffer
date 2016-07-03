<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Util;
use Symplify\PHP7_CodeSniffer\Exceptions\RuntimeException;

final class Ruleset
{
    /**
     * A list of regular expressions used to include specific sniffs for files and folders.
     *
     * The key is the sniff code and the value is an array with
     * the key being a regular expression and the value is the type
     * of ignore pattern (absolute or relative).
     *
     * @var array<string, array<string, string>>
     */
    public $includePatterns = array();

    /**
     * An array of sniff objects that are being used to check files.
     *
     * The key is the fully qualified name of the sniff class
     * and the value is the sniff object.
     *
     * @var array<string, \Symplify\PHP7_CodeSniffer\Sniff>
     */
    private $sniffs = array();

    /**
     * A mapping of sniff codes to fully qualified class names.
     *
     * The key is the sniff code and the value
     * is the fully qualified name of the sniff class.
     *
     * @var array<string, string>
     */
    public $sniffCodes = array();

    /**
     * An array of token types and the sniffs that are listening for them.
     *
     * The key is the token name being listened for and the value
     * is the sniff object.
     *
     * @var array<int, \Symplify\PHP7_CodeSniffer\Sniff>
     */
    public $tokenListeners = array();

    /**
     * An array of rules from the ruleset.xml file.
     *
     * It may be empty, indicating that the ruleset does not override
     * any of the default sniff settings.
     *
     * @var array<string, mixed>
     */
    public $ruleset = [];

    /**
     * The directories that the processed rulesets are in.
     *
     * @var string[]
     */
    private $rulesetDirs = [];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    public function __construct(Configuration $configuration, SniffFinder $sniffFinder)
    {
        $this->configuration = $configuration;
        $this->sniffFinder = $sniffFinder;

        $sniffs = [];
        foreach ($configuration->getStandards() as $name => $rulesetXmlPath) {
            $sniffs = $this->sniffFinder->findSniffsInRuleset($rulesetXmlPath);

//            $ruleset = simplexml_load_file($rulesetXmlPath);
//            if ($ruleset !== false) {
//                $standardName = (string) $ruleset['name'];
//                if ($this->name !== '') {
//                    $this->name .= ', ';
//                }
//
//                $this->name   .= $standardName;
//            }
//
            $sniffs = array_merge($sniffs, $this->processRuleset($standard));
        }//end foreach

        // Ignore sniff restrictions if caching is on.
        $restrictions = [];
        $sniffRestrictions = array();
        foreach ($restrictions as $sniffCode) {
            $parts = explode('.', strtolower($sniffCode));
            $sniffName = 'Symplify\PHP7_CodeSniffer\standards\\'.$parts[0].'\sniffs\\'.$parts[1].'\\'.$parts[2].'sniff';
            $sniffRestrictions[$sniffName] = true;
        }

        dump($sniffs);
        die;

        $this->registerSniffs($sniffs, $sniffRestrictions);
        $this->populateTokenListeners();
    }

    /**
     * Processes a single ruleset and returns a list of the sniffs it represents.
     *
     * Rules founds within the ruleset are processed immediately, but sniff classes
     * are not registered by this method.
     *
     * @return string[]
     * @throws RuntimeException If the ruleset path is invalid.
     */
    public function processRuleset(string $rulesetPath) : array
    {
        $ruleset = simplexml_load_file($rulesetPath);

        $ownSniffs      = array();
        $includedSniffs = array();
        $excludedSniffs = array();

        $rulesetDir          = dirname($rulesetPath);
        $this->rulesetDirs[] = $rulesetDir;

        $sniffDir = $rulesetDir.DIRECTORY_SEPARATOR.'Sniffs';
        if (is_dir($sniffDir) === true) {
            $ownSniffs = $this->expandSniffDirectory($sniffDir);
        }

        foreach ($ruleset->rule as $rule) {
            if (isset($rule['ref']) === false
                || $this->shouldProcessElement($rule) === false
            ) {
                continue;
            }

            $expandedSniffs = $this->expandRulesetReference($rule['ref'], $rulesetDir);
            $newSniffs      = array_diff($expandedSniffs, $includedSniffs);
            $includedSniffs = array_merge($includedSniffs, $expandedSniffs);

            if (isset($rule->exclude) === true) {
                foreach ($rule->exclude as $exclude) {
                    if ($this->shouldProcessElement($exclude) === false) {
                        continue;
                    }

                    $excludedSniffs = array_merge(
                        $excludedSniffs,
                        $this->expandRulesetReference($exclude['name'], $rulesetDir)
                    );
                }//end foreach
            }//end if

            $this->processRule($rule, $newSniffs);
        }//end foreach

        $includedSniffs = array_unique(array_merge($ownSniffs, $includedSniffs));
        $excludedSniffs = array_unique($excludedSniffs);

        // Merge our own sniff list with our externally included
        // sniff list, but filter out any excluded sniffs.
        $files = array();
        foreach ($includedSniffs as $sniff) {
            if (in_array($sniff, $excludedSniffs) === true) {
                continue;
            } else {
                $files[] = Util\Common::realpath($sniff);
            }
        }

        return $files;

    }//end processRuleset()


    /**
     * Loads and stores sniffs objects used for sniffing files.
     *
     * @param array $files        Paths to the sniff files to register.
     * @param array $restrictions The sniff class names to restrict the allowed
     *                            listeners to.
     *
     * @return void
     */
    public function registerSniffs($files, $restrictions)
    {
        $listeners = array();

        foreach ($files as $file) {
            // Work out where the position of /StandardName/Sniffs/... is
            // so we can determine what the class will be called.
            $sniffPos = strrpos($file, DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR);
            if ($sniffPos === false) {
                continue;
            }

            $slashPos = strrpos(substr($file, 0, $sniffPos), DIRECTORY_SEPARATOR);
            if ($slashPos === false) {
                continue;
            }

            $className = Autoload::loadFile($file);

            // If they have specified a list of sniffs to restrict to, check
            // to see if this sniff is allowed.
            if (empty($restrictions) === false
                && isset($restrictions[strtolower($className)]) === false
            ) {
                continue;
            }

            // Skip abstract classes.
            $reflection = new \ReflectionClass($className);
            if ($reflection->isAbstract() === true) {
                continue;
            }

            $listeners[$className] = $className;
        }//end foreach

        $this->sniffs = $listeners;

    }//end registerSniffs()


    /**
     * Populates the array of Symplify\PHP7_CodeSniffer_Sniff's for this file.
     *
     * @return void
     * @throws RuntimeException If sniff registration fails.
     */
    public function populateTokenListeners()
    {
        // Construct a list of listeners indexed by token being listened for.
        $this->tokenListeners = array();

        foreach ($this->sniffs as $sniffClass => $sniffObject) {
            $this->sniffs[$sniffClass] = null;
            $this->sniffs[$sniffClass] = new $sniffClass();

            $sniffCode = Util\Common::getSniffCode($sniffClass);
            $this->sniffCodes[$sniffCode] = $sniffClass;

            // Set custom properties.
            if (isset($this->ruleset[$sniffCode]['properties']) === true) {
                foreach ($this->ruleset[$sniffCode]['properties'] as $name => $value) {
                    $this->setSniffProperty($sniffClass, $name, $value);
                }
            }

            $tokenizers = array();
            $vars = get_class_vars($sniffClass);
            if (isset($vars['supportedTokenizers']) === true) {
                foreach ($vars['supportedTokenizers'] as $tokenizer) {
                    $tokenizers[$tokenizer] = $tokenizer;
                }
            } else {
                $tokenizers = array('PHP' => 'PHP');
            }

            $tokens = $this->sniffs[$sniffClass]->register();
            if (is_array($tokens) === false) {
                $msg = "Sniff $sniffClass register() method must return an array";
                throw new RuntimeException($msg);
            }

            foreach ($tokens as $token) {
                if (isset($this->tokenListeners[$token]) === false) {
                    $this->tokenListeners[$token] = array();
                }

                if (isset($this->tokenListeners[$token][$sniffClass]) === false) {
                    $this->tokenListeners[$token][$sniffClass] = array(
                                                                  'class'      => $sniffClass,
                                                                  'source'     => $sniffCode,
                                                                  'tokenizers' => $tokenizers,
                                                                 );
                }
            }
        }//end foreach

    }//end populateTokenListeners()


    /**
     * Set a single property for a sniff.
     *
     * @param string $sniffClass The class name of the sniff.
     * @param string $name       The name of the property to change.
     * @param string $value      The new value of the property.
     *
     * @return void
     */
    public function setSniffProperty($sniffClass, $name, $value)
    {
        // Setting a property for a sniff we are not using.
        if (isset($this->sniffs[$sniffClass]) === false) {
            return;
        }

        $name = trim($name);
        if (is_string($value) === true) {
            $value = trim($value);
        }

        // Special case for booleans.
        if ($value === 'true') {
            $value = true;
        } else if ($value === 'false') {
            $value = false;
        }

        $this->sniffs[$sniffClass]->$name = $value;

    }

    /**
     * @return string[]
     */
    public function getSniffs() : array
    {
        return $this->sniffs;
    }
}
