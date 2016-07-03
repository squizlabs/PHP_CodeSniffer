<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffProvider;
use Symplify\PHP7_CodeSniffer\Util;
use Symplify\PHP7_CodeSniffer\Exceptions\RuntimeException;

final class Ruleset
{
    /**
     * An array of sniff objects that are being used to check files.
     *
     * The key is the fully qualified name of the sniff class
     * and the value is the sniff object.
     *
     * @var array<string, \Symplify\PHP7_CodeSniffer\Sniff>
     */
    private $sniffs = [];

    /**
     * A mapping of sniff codes to fully qualified class names.
     *
     * The key is the sniff code and the value
     * is the fully qualified name of the sniff class.
     *
     * @var array<string, string>
     */
    public $sniffCodes = [];

    /**
     * An array of token types and the sniffs that are listening for them.
     *
     * The key is the token name being listened for and the value
     * is the sniff object.
     *
     * @var array<int, \Symplify\PHP7_CodeSniffer\Sniff>
     */
    public $tokenListeners = [];

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
     * @var SniffProvider
     */
    private $sniffProvider;

    public function __construct(SniffProvider $sniffProvider)
    {
        $this->sniffProvider = $sniffProvider;
    }

    public function createSniffList()
    {
        $this->registerSniffs($this->sniffProvider->getActiveSniffs(), $this->sniffProvider->getSniffRegistrations());
        $this->populateTokenListeners();
    }

    /**
     * Loads and stores sniffs objects used for sniffing files.
     */
    public function registerSniffs(array $files, array $restrictions)
    {
        $listeners = [];

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
