<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Util;
use Symplify\PHP7_CodeSniffer\Exceptions\RuntimeException;

final class Ruleset
{
    /**
     * The name of the coding standard being used.
     *
     * If a top-level standard includes other standards, or sniffs
     * from other standards, only the name of the top-level standard
     * will be stored in here.
     *
     * If multiple top-level standards are being loaded into
     * a single ruleset object, this will store a comma separated list
     * of the top-level standard names.
     *
     * @var string
     */
    public $name = '';

    /**
     * A list of file paths for the ruleset files being used.
     *
     * @var string[]
     */
    public $paths = '';

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
    public $sniffs = array();

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

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;


        $sniffs = [];
        foreach ($configuration->getStandards() as $name => $rulesetXmlPath) {
//            $installed = Util\Standards::getInstalledStandardPath($standard);
//            if ($installed === null) {
//                $standard = Util\Common::realpath($standard);
//                if (is_dir($standard) === true
//                    && is_file(Util\Common::realpath($standard.DIRECTORY_SEPARATOR.'ruleset.xml')) === true
//                ) {
//                    $standard = Util\Common::realpath($standard.DIRECTORY_SEPARATOR.'ruleset.xml');
//                }
//            } else {
//                $standard = $installed;
//            }

            $ruleset = simplexml_load_file($rulesetXmlPath);
            if ($ruleset !== false) {
                $standardName = (string) $ruleset['name'];
                if ($this->name !== '') {
                    $this->name .= ', ';
                }

                $this->name   .= $standardName;
//                $this->paths[] = $standard;
            }

            $sniffs = array_merge($sniffs, $this->processRuleset($standard));
        }//end foreach

        dump($sniffs);
        die;

        // Ignore sniff restrictions if caching is on.
        $restrictions = [];
        $sniffRestrictions = array();
        foreach ($restrictions as $sniffCode) {
            $parts = explode('.', strtolower($sniffCode));
            $sniffName = 'Symplify\PHP7_CodeSniffer\standards\\'.$parts[0].'\sniffs\\'.$parts[1].'\\'.$parts[2].'sniff';
            $sniffRestrictions[$sniffName] = true;
        }

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
        $rulesetPath = Util\Common::realpath($rulesetPath);

        $ruleset = simplexml_load_file($rulesetPath);
        if ($ruleset === false) {
            throw new RuntimeException("Ruleset $rulesetPath is not valid");
        }

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

        // Process custom ignore pattern rules.
        foreach ($ruleset->{'exclude-pattern'} as $pattern) {
            if ($this->shouldProcessElement($pattern) === false) {
                continue;
            }

            if (isset($pattern['type']) === false) {
                $pattern['type'] = 'absolute';
            }
        }

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
     * Expands a directory into a list of sniff files within.
     *
     * @param string $directory The path to a directory.
     *
     * @return array
     */
    private function expandSniffDirectory($directory)
    {
        $sniffs = array();

        $rdi = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $di  = new \RecursiveIteratorIterator($rdi, 0, \RecursiveIteratorIterator::CATCH_GET_CHILD);

        $dirLen = strlen($directory);

        foreach ($di as $file) {
            $filename = $file->getFilename();

            // Skip hidden files.
            if (substr($filename, 0, 1) === '.') {
                continue;
            }

            // We are only interested in PHP and sniff files.
            $fileParts = explode('.', $filename);
            if (array_pop($fileParts) !== 'php') {
                continue;
            }

            $basename = basename($filename, '.php');
            if (substr($basename, -5) !== 'Sniff') {
                continue;
            }

            $path = $file->getPathname();

            // Skip files in hidden directories within the Sniffs directory of this
            // standard. We use the offset with strpos() to allow hidden directories
            // before, valid example:
            // /home/foo/.composer/vendor/squiz/custom_tool/MyStandard/Sniffs/...
            if (strpos($path, DIRECTORY_SEPARATOR.'.', $dirLen) !== false) {
                continue;
            }

            $sniffs[] = $path;
        }//end foreach

        return $sniffs;
    }

    /**
     * Expands a ruleset reference into a list of sniff files.
     *
     * @param string $ref        The reference from the ruleset XML file.
     * @param string $rulesetDir The directory of the ruleset XML file, used to
     *                           evaluate relative paths.
     *
     * @return array
     * @throws RuntimeException If the reference is invalid.
     */
    private function expandRulesetReference($ref, $rulesetDir)
    {
        // Ignore internal sniffs codes as they are used to only
        // hide and change internal messages.
        if (substr($ref, 0, 9) === 'Internal.') {
            return array();
        }

        // As sniffs can't begin with a full stop, assume references in
        // this format are relative paths and attempt to convert them
        // to absolute paths. If this fails, let the reference run through
        // the normal checks and have it fail as normal.
        if (substr($ref, 0, 1) === '.') {
            $realpath = Util\Common::realpath($rulesetDir.'/'.$ref);
            if ($realpath !== false) {
                $ref = $realpath;
            }
        }

        // As sniffs can't begin with a tilde, assume references in
        // this format are relative to the user's home directory.
        if (substr($ref, 0, 2) === '~/') {
            $realpath = Util\Common::realpath($ref);
            if ($realpath !== false) {
                $ref = $realpath;
            }
        }

        if (is_file($ref) === true) {
            if (substr($ref, -9) === 'Sniff.php') {
                // A single external sniff.
                $this->rulesetDirs[] = dirname(dirname(dirname($ref)));
                return array($ref);
            }
        } else {
            // See if this is a whole standard being referenced.
            $path = Util\Standards::getInstalledStandardPath($ref);

            if ($path !== null) {
                $ref = $path;
            } else if (is_dir($ref) === false) {
                // Work out the sniff path.
                $sepPos = strpos($ref, DIRECTORY_SEPARATOR);
                if ($sepPos !== false) {
                    $stdName = substr($ref, 0, $sepPos);
                    $path    = substr($ref, $sepPos);
                } else {
                    $parts   = explode('.', $ref);
                    $stdName = $parts[0];
                    if (count($parts) === 1) {
                        // A whole standard?
                        $path = '';
                    } else if (count($parts) === 2) {
                        // A directory of sniffs?
                        $path = DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR.$parts[1];
                    } else {
                        // A single sniff?
                        $path = DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR.$parts[1].DIRECTORY_SEPARATOR.$parts[2].'Sniff.php';
                    }
                }

                $newRef  = false;
                $stdPath = Util\Standards::getInstalledStandardPath($stdName);
                if ($stdPath !== null && $path !== '') {
                    $newRef = Util\Common::realpath(dirname($stdPath).$path);
                }

                if ($newRef === false) {
                    // The sniff is not locally installed, so check if it is being
                    // referenced as a remote sniff outside the install. We do this
                    // by looking through all directories where we have found ruleset
                    // files before, looking for ones for this particular standard,
                    // and seeing if it is in there.
                    foreach ($this->rulesetDirs as $dir) {
                        if (strtolower(basename($dir)) !== strtolower($stdName)) {
                            continue;
                        }

                        $newRef = Util\Common::realpath($dir.$path);

                        if ($newRef !== false) {
                            $ref = $newRef;
                        }
                    }
                } else {
                    $ref = $newRef;
                }
            }//end if
        }//end if

        if (is_dir($ref) === true) {
            if (is_file($ref.DIRECTORY_SEPARATOR.'ruleset.xml') === true) {
                // We are referencing an external coding standard.
                return $this->processRuleset($ref.DIRECTORY_SEPARATOR.'ruleset.xml');
            } else {
                // We are referencing a whole directory of sniffs.
                return $this->expandSniffDirectory($ref);
            }
        } else {
            if (is_file($ref) === false) {
                $error = "Referenced sniff \"$ref\" does not exist";
                throw new RuntimeException($error);
            }

            if (substr($ref, -9) === 'Sniff.php') {
                // A single sniff.
                return array($ref);
            } else {
                // Assume an external ruleset.xml file.
                return $this->processRuleset($ref);
            }
        }//end if

    }//end expandRulesetReference()


    /**
     * Processes a rule from a ruleset XML file, overriding built-in defaults.
     *
     * @param SimpleXMLElement $rule      The rule object from a ruleset XML file.
     * @param string[]         $newSniffs An array of sniffs that got included by this rule.
     */
    private function processRule(SimpleXMLElement $rule, array $newSniffs)
    {
        $ref  = (string) $rule['ref'];
        $todo = array($ref);

        $parts = explode('.', $ref);
        if (count($parts) <= 2) {
            // We are processing a standard or a category of sniffs.
            foreach ($newSniffs as $sniffFile) {
                $parts         = explode(DIRECTORY_SEPARATOR, $sniffFile);
                $sniffName     = array_pop($parts);
                $sniffCategory = array_pop($parts);
                array_pop($parts);
                $sniffStandard = array_pop($parts);
                $todo[]        = $sniffStandard.'.'.$sniffCategory.'.'.substr($sniffName, 0, -9);
            }
        }

        foreach ($todo as $code) {
            // Custom message type.
            if (isset($rule->type) === true
                && $this->shouldProcessElement($rule->type) === true
            ) {
                if (isset($this->ruleset[$code]) === false) {
                    $this->ruleset[$code] = array();
                }

                $type = strtolower((string) $rule->type);
                if ($type !== 'error' && $type !== 'warning') {
                    throw new RuntimeException("Message type \"$type\" is invalid; must be \"error\" or \"warning\"");
                }

                $this->ruleset[$code]['type'] = $type;
            }//end if

            // Custom message.
            if (isset($rule->message) === true
                && $this->shouldProcessElement($rule->message) === true
            ) {
                if (isset($this->ruleset[$code]) === false) {
                    $this->ruleset[$code] = array();
                }

                $this->ruleset[$code]['message'] = (string) $rule->message;
            }

            // Custom properties.
            if (isset($rule->properties) === true
                && $this->shouldProcessElement($rule->properties) === true
            ) {
                foreach ($rule->properties->property as $prop) {
                    if ($this->shouldProcessElement($prop) === false) {
                        continue;
                    }

                    if (isset($this->ruleset[$code]) === false) {
                        $this->ruleset[$code] = array(
                                                 'properties' => array(),
                                                );
                    } else if (isset($this->ruleset[$code]['properties']) === false) {
                        $this->ruleset[$code]['properties'] = array();
                    }

                    $name = (string) $prop['name'];
                    if (isset($prop['type']) === true
                        && (string) $prop['type'] === 'array'
                    ) {
                        $value  = (string) $prop['value'];
                        $values = array();
                        foreach (explode(',', $value) as $val) {
                            $v = '';

                            list($k,$v) = explode('=>', $val.'=>');
                            if ($v !== '') {
                                $values[$k] = $v;
                            } else {
                                $values[] = $k;
                            }
                        }

                        $this->ruleset[$code]['properties'][$name] = $values;
                    } else {
                        $this->ruleset[$code]['properties'][$name] = (string) $prop['value'];
                    }//end if
                }//end foreach
            }//end foreach
        }//end foreach

    }//end processRule()


    /**
     * Determine if an element should be processed or ignored.
     *
     * @param SimpleXMLElement $element An object from a ruleset XML file.
     *
     * @return bool
     */
    private function shouldProcessElement($element)
    {
        if (isset($element['phpcbf-only']) === false
            && isset($element['phpcs-only']) === false
        ) {
            // No exceptions are being made.
            return true;
        }

        if (PHP_CodeSniffer_CBF === true
            && isset($element['phpcbf-only']) === true
            && (string) $element['phpcbf-only'] === 'true'
        ) {
            return true;
        }

        if (PHP_CodeSniffer_CBF === false
            && isset($element['phpcs-only']) === true
            && (string) $element['phpcs-only'] === 'true'
        ) {
            return true;
        }

        return false;

    }//end shouldProcessElement()


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

    }//end setSniffProperty()


}//end class
