<?php

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Util;
use PHP_CodeSniffer\Exceptions\RuntimeException;

/**
 * A class to process command line phpcs scripts.
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
 * A class to process command line phpcs scripts.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Ruleset
{

    private $config = null;
    public $name = '';

    /**
     * An array of sniff objects that are being used to check files.
     *
     * @var array<string, PHP_CodeSniffer\Sniff>
     */
    public $sniffs = array();

    /**
     * The listeners array, indexed by token type.
     *
     * @var array<int, PHP_CodeSniffer\Sniff>
     */
    public $tokenListeners = array();

    /**
     * An array of rules from the ruleset.xml file.
     *
     * It may be empty, indicating that the ruleset does not override
     * any of the default sniff settings.
     *
     * @var array
     */
    protected $ruleset = array();

    /**
     * The directories that the processed rulesets are in.
     *
     * This is declared static because it is also used in the
     * autoloader to look for sniffs outside the PHPCS install.
     * This way, standards designed to be installed inside PHPCS can
     * also be used from outside the PHPCS Standards directory.
     *
     * @var string
     */
    protected static $rulesetDirs = array();


    /**
     * Initialise the standard that the run will use.
     *
     * @param string|array $standards    The set of code sniffs we are testing
     *
     * @return void
     */
    public function __construct(Config $config)
    {
        $restrictions = $config->sniffs;
        $this->config = $config;

        $sniffs = array();
        foreach ($config->standards as $standard) {
            $installed = Util\Standards::getInstalledStandardPath($standard);
            if ($installed === null) {
                $standard = Util\Common::realpath($standard);
                if (is_dir($standard) === true
                    && is_file(Util\Common::realpath($standard.DIRECTORY_SEPARATOR.'ruleset.xml')) === true
                ) {
                    $standard = Util\Common::realpath($standard.DIRECTORY_SEPARATOR.'ruleset.xml');
                }
            } else {
                $standard = $installed;
            }

            $ruleset = simplexml_load_string(file_get_contents($standard));
            if ($ruleset !== false) {
                $standardName = (string) $ruleset['name'];
                if ($this->name !== '') {
                    $this->name .= ', ';
                }

                $this->name .= $standardName;
            }

            if (PHP_CODESNIFFER_VERBOSITY === 1) {
                echo "Registering sniffs in the $standardName standard... ";
                if (count($config->standards) > 1 || PHP_CODESNIFFER_VERBOSITY > 2) {
                    echo PHP_EOL;
                }
            }

            $sniffs = array_merge($sniffs, $this->processRuleset($standard));
        }//end foreach

        $sniffRestrictions = array();
        foreach ($restrictions as $sniffCode) {
            $parts = explode('.', strtolower($sniffCode));
            $sniffName = 'php_codesniffer\standards\\'.$parts[0].'\sniffs\\'.$parts[1].'\\'.$parts[2].'sniff';
            $sniffRestrictions[$sniffName] = true;
        }

        $this->registerSniffs($sniffs, $sniffRestrictions);
        $this->populateTokenListeners();

        if (PHP_CODESNIFFER_VERBOSITY === 1) {
            $numSniffs = count($this->sniffs);
            echo "DONE ($numSniffs sniffs registered)".PHP_EOL;
        }

    }//end __construct()


    /**
     * Prints a report showing the sniffs contained in a standard.
     *
     * @return void
     */
    public function explain()
    {
        $sniffs = array_keys($this->sniffs);
        sort($sniffs);

        ob_start();

        $lastStandard = null;
        $lastCount    = '';
        $sniffCount   = count($sniffs);

        // Add a dummy entry to the end so we loop
        // one last time and clear the output buffer.
        $sniffs[] = '';

        echo PHP_EOL."The $this->name standard contains $sniffCount sniffs".PHP_EOL;

        ob_start();

        foreach ($sniffs as $i => $sniff) {
            if ($i === $sniffCount) {
                $currentStandard = null;
            } else {
                $parts = explode('\\', $sniff);

                $currentStandard = $parts[2];
                if ($lastStandard === null) {
                    $lastStandard = $currentStandard;
                }
            }

            if ($currentStandard !== $lastStandard) {
                $sniffList = ob_get_contents();
                ob_end_clean();

                echo PHP_EOL.$lastStandard.' ('.$lastCount.' sniffs)'.PHP_EOL;
                echo str_repeat('-', (strlen($lastStandard.$lastCount) + 10));
                echo PHP_EOL;
                echo $sniffList;

                $lastStandard = $parts[2];
                $lastCount    = 0;

                if ($currentStandard === null) {
                    break;
                }

                ob_start();
            }

            echo '  '.$parts[2].'.'.$parts[4].'.'.substr($parts[5], 0, -5).PHP_EOL;
            $lastCount++;
        }//end foreach

    }//end explain()



    /**
     * Processes a single ruleset and returns a list of the sniffs it represents.
     *
     * Rules founds within the ruleset are processed immediately, but sniff classes
     * are not registered by this method.
     *
     * @param string $rulesetPath The path to a ruleset XML file.
     * @param int    $depth       How many nested processing steps we are in. This
     *                            is only used for debug output.
     *
     * @return array
     * @throws RuntimeException If the ruleset path is invalid.
     */
    public function processRuleset($rulesetPath, $depth=0)
    {
        $rulesetPath = Util\Common::realpath($rulesetPath);
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo str_repeat("\t", $depth);
            echo "Processing ruleset $rulesetPath".PHP_EOL;
        }

        $ruleset = simplexml_load_string(file_get_contents($rulesetPath));
        if ($ruleset === false) {
            throw new RuntimeException("Ruleset $rulesetPath is not valid");
        }

        $ownSniffs      = array();
        $includedSniffs = array();
        $excludedSniffs = array();

        $rulesetDir          = dirname($rulesetPath);
        self::$rulesetDirs[] = $rulesetDir;

        if (is_dir($rulesetDir.DIRECTORY_SEPARATOR.'Sniffs') === true) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\tAdding sniff files from \"/.../".basename($rulesetDir)."/Sniffs/\" directory".PHP_EOL;
            }

            $ownSniffs = $this->_expandSniffDirectory($rulesetDir.DIRECTORY_SEPARATOR.'Sniffs', $depth);
        }

        foreach ($ruleset->rule as $rule) {
            if (isset($rule['ref']) === false
                || $this->_shouldProcessElement($rule) === false
            ) {
                continue;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\tProcessing rule \"".$rule['ref'].'"'.PHP_EOL;
            }

            $includedSniffs = array_merge(
                $includedSniffs,
                $this->_expandRulesetReference($rule['ref'], $rulesetDir, $depth)
            );

            if (isset($rule->exclude) === true) {
                foreach ($rule->exclude as $exclude) {
                    if ($this->_shouldProcessElement($exclude) === false) {
                        continue;
                    }

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo "\t\tExcluding rule \"".$exclude['name'].'"'.PHP_EOL;
                    }

                    // Check if a single code is being excluded, which is a shortcut
                    // for setting the severity of the message to 0.
                    $parts = explode('.', $exclude['name']);
                    if (count($parts) === 4) {
                        $this->ruleset[(string) $exclude['name']]['severity'] = 0;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo "\t\t=> severity set to 0".PHP_EOL;
                        }
                    } else {
                        $excludedSniffs = array_merge(
                            $excludedSniffs,
                            $this->_expandRulesetReference($exclude['name'], $rulesetDir, ($depth + 1))
                        );
                    }
                }//end foreach
            }//end if

            $this->_processRule($rule, $depth);
        }//end foreach

        // Process custom command line arguments.
        $cliArgs = array();
        foreach ($ruleset->{'arg'} as $arg) {
            if ($this->_shouldProcessElement($arg) === false) {
                continue;
            }

            if (isset($arg['name']) === true) {
                $argString = '--'.(string) $arg['name'];
                if (isset($arg['value']) === true) {
                    $argString .= '='.(string) $arg['value'];
                }
            } else {
                $argString = '-'.(string) $arg['value'];
            }

            $cliArgs[] = $argString;

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t=> set command line value $argString".PHP_EOL;
            }
        }//end foreach

        if (empty($this->config->files) === true) {
            // Process hard-coded file paths.
            foreach ($ruleset->{'file'} as $file) {
                $file      = (string) $file;
                $cliArgs[] = $file;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t=> added \"$file\" to the file list".PHP_EOL;
                }
            }
        }

        if (empty($cliArgs) === false) {
            $this->config->setCommandLineValues($cliArgs);
        }

        // Process custom sniff config settings.
        foreach ($ruleset->{'config'} as $config) {
            if ($this->_shouldProcessElement($config) === false) {
                continue;
            }

            $this->setConfigData((string) $config['name'], (string) $config['value'], true);
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t=> set config value ".(string) $config['name'].': '.(string) $config['value'].PHP_EOL;
            }
        }

        // Process custom ignore pattern rules.
        foreach ($ruleset->{'exclude-pattern'} as $pattern) {
            if ($this->_shouldProcessElement($pattern) === false) {
                continue;
            }

            if (isset($pattern['type']) === false) {
                $pattern['type'] = 'absolute';
            }

            $this->ignorePatterns[(string) $pattern] = (string) $pattern['type'];
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t=> added global ".(string) $pattern['type'].' ignore pattern: '.(string) $pattern.PHP_EOL;
            }
        }

        $includedSniffs = array_unique(array_merge($ownSniffs, $includedSniffs));
        $excludedSniffs = array_unique($excludedSniffs);

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $included = count($includedSniffs);
            $excluded = count($excludedSniffs);
            echo str_repeat("\t", $depth);
            echo "=> Ruleset processing complete; included $included sniffs and excluded $excluded".PHP_EOL;
        }

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
     * @param int    $depth     How many nested processing steps we are in. This
     *                          is only used for debug output.
     *
     * @return array
     */
    private function _expandSniffDirectory($directory, $depth=0)
    {
        $sniffs = array();

        $rdi = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $di = new \RecursiveIteratorIterator($rdi, 0, \RecursiveIteratorIterator::CATCH_GET_CHILD);

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
            // /home/foo/.composer/vendor/drupal/coder/coder_sniffer/Drupal/Sniffs/...
            if (strpos($path, DIRECTORY_SEPARATOR.'.', $dirLen) !== false) {
                continue;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t=> $path".PHP_EOL;
            }

            $sniffs[] = $path;
        }//end foreach

        return $sniffs;

    }//end _expandSniffDirectory()


    /**
     * Expands a ruleset reference into a list of sniff files.
     *
     * @param string $ref        The reference from the ruleset XML file.
     * @param string $rulesetDir The directory of the ruleset XML file, used to
     *                           evaluate relative paths.
     * @param int    $depth      How many nested processing steps we are in. This
     *                           is only used for debug output.
     *
     * @return array
     * @throws RuntimeException If the reference is invalid.
     */
    private function _expandRulesetReference($ref, $rulesetDir, $depth=0)
    {
        // Ignore internal sniffs codes as they are used to only
        // hide and change internal messages.
        if (substr($ref, 0, 9) === 'Internal.') {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t* ignoring internal sniff code *".PHP_EOL;
            }

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
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> $ref".PHP_EOL;
                }
            }
        }

        // As sniffs can't begin with a tilde, assume references in
        // this format at relative to the user's home directory.
        if (substr($ref, 0, 2) === '~/') {
            $realpath = Util\Common::realpath($ref);
            if ($realpath !== false) {
                $ref = $realpath;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> $ref".PHP_EOL;
                }
            }
        }

        if (is_file($ref) === true) {
            if (substr($ref, -9) === 'Sniff.php') {
                // A single external sniff.
                self::$rulesetDirs[] = dirname(dirname(dirname($ref)));
                return array($ref);
            }
        } else {
            // See if this is a whole standard being referenced.
            $path = Util\Standards::getInstalledStandardPath($ref);
            if (Util\Common::isPharFile($path) === true && strpos($path, 'ruleset.xml') === false) {
                // If the ruleset exists inside the phar file, use it.
                if (file_exists($path.DIRECTORY_SEPARATOR.'ruleset.xml') === true) {
                    $path = $path.DIRECTORY_SEPARATOR.'ruleset.xml';
                } else {
                    $path = null;
                }
            }

            if ($path !== null) {
                $ref = $path;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> $ref".PHP_EOL;
                }
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
                    if (Util\Common::isPharFile($stdPath) === true
                        && strpos($stdPath, 'ruleset.xml') === false
                    ) {
                        // Phar files can only return the directory,
                        // since ruleset can be omitted if building one standard.
                        $newRef = Util\Common::realpath($stdPath.$path);
                    } else {
                        $newRef = Util\Common::realpath(dirname($stdPath).$path);
                    }
                }

                if ($newRef === false) {
                    // The sniff is not locally installed, so check if it is being
                    // referenced as a remote sniff outside the install. We do this
                    // by looking through all directories where we have found ruleset
                    // files before, looking for ones for this particular standard,
                    // and seeing if it is in there.
                    foreach (self::$rulesetDirs as $dir) {
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

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> $ref".PHP_EOL;
                }
            }//end if
        }//end if

        if (is_dir($ref) === true) {
            if (is_file($ref.DIRECTORY_SEPARATOR.'ruleset.xml') === true) {
                // We are referencing an external coding standard.
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t* rule is referencing a standard using directory name; processing *".PHP_EOL;
                }

                return $this->processRuleset($ref.DIRECTORY_SEPARATOR.'ruleset.xml', ($depth + 2));
            } else {
                // We are referencing a whole directory of sniffs.
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t* rule is referencing a directory of sniffs *".PHP_EOL;
                    echo str_repeat("\t", $depth);
                    echo "\t\tAdding sniff files from directory".PHP_EOL;
                }

                return $this->_expandSniffDirectory($ref, ($depth + 1));
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
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t* rule is referencing a standard using ruleset path; processing *".PHP_EOL;
                }

                return $this->processRuleset($ref, ($depth + 2));
            }
        }//end if

    }//end _expandRulesetReference()


    /**
     * Processes a rule from a ruleset XML file, overriding built-in defaults.
     *
     * @param SimpleXMLElement $rule  The rule object from a ruleset XML file.
     * @param int              $depth How many nested processing steps we are in.
     *                                This is only used for debug output.
     *
     * @return void
     */
    private function _processRule($rule, $depth=0)
    {
        $code = (string) $rule['ref'];

        // Custom severity.
        if (isset($rule->severity) === true
            && $this->_shouldProcessElement($rule->severity) === true
        ) {
            if (isset($this->ruleset[$code]) === false) {
                $this->ruleset[$code] = array();
            }

            $this->ruleset[$code]['severity'] = (int) $rule->severity;
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t=> severity set to ".(int) $rule->severity.PHP_EOL;
            }
        }

        // Custom message type.
        if (isset($rule->type) === true
            && $this->_shouldProcessElement($rule->type) === true
        ) {
            if (isset($this->ruleset[$code]) === false) {
                $this->ruleset[$code] = array();
            }

            $this->ruleset[$code]['type'] = (string) $rule->type;
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t=> message type set to ".(string) $rule->type.PHP_EOL;
            }
        }

        // Custom message.
        if (isset($rule->message) === true
            && $this->_shouldProcessElement($rule->message) === true
        ) {
            if (isset($this->ruleset[$code]) === false) {
                $this->ruleset[$code] = array();
            }

            $this->ruleset[$code]['message'] = (string) $rule->message;
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t=> message set to ".(string) $rule->message.PHP_EOL;
            }
        }

        // Custom properties.
        if (isset($rule->properties) === true
            && $this->_shouldProcessElement($rule->properties) === true
        ) {
            foreach ($rule->properties->property as $prop) {
                if ($this->_shouldProcessElement($prop) === false) {
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
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo "\t\t=> array property \"$name\" set to \"$value\"".PHP_EOL;
                    }
                } else {
                    $this->ruleset[$code]['properties'][$name] = (string) $prop['value'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo "\t\t=> property \"$name\" set to \"".(string) $prop['value'].'"'.PHP_EOL;
                    }
                }//end if
            }//end foreach
        }//end if

        // Ignore patterns.
        foreach ($rule->{'exclude-pattern'} as $pattern) {
            if ($this->_shouldProcessElement($pattern) === false) {
                continue;
            }

            if (isset($this->ignorePatterns[$code]) === false) {
                $this->ignorePatterns[$code] = array();
            }

            if (isset($pattern['type']) === false) {
                $pattern['type'] = 'absolute';
            }

            $this->ignorePatterns[$code][(string) $pattern] = (string) $pattern['type'];
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t=> added sniff-specific ".(string) $pattern['type'].' ignore pattern: '.(string) $pattern.PHP_EOL;
            }
        }

    }//end _processRule()


    /**
     * Determine if an element should be processed or ignored.
     *
     * @param SimpleXMLElement $element An object from a ruleset XML file.
     * @param int              $depth   How many nested processing steps we are in.
     *                                  This is only used for debug output.
     *
     * @return bool
     */
    private function _shouldProcessElement($element, $depth=0)
    {
        if (isset($element['phpcbf-only']) === false
            && isset($element['phpcs-only']) === false
        ) {
            // No exceptions are being made.
            return true;
        }

        if (PHP_CODESNIFFER_CBF === true
            && isset($element['phpcbf-only']) === true
            && (string) $element['phpcbf-only'] === 'true'
        ) {
            return true;
        }

        if (PHP_CODESNIFFER_CBF === false
            && isset($element['phpcs-only']) === true
            && (string) $element['phpcs-only'] === 'true'
        ) {
            return true;
        }

        return false;

    }//end _shouldProcessElement()


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

            $className = substr($file, ($slashPos + 1));
            $className = substr($className, 0, -4);
            $className = str_replace(DIRECTORY_SEPARATOR, '\\', $className);
            $className = 'PHP_CodeSniffer\Standards\\'.$className;

            // If they have specified a list of sniffs to restrict to, check
            // to see if this sniff is allowed.
            if (empty($restrictions) === false
                && isset($restrictions[strtolower($className)]) === false
            ) {
                continue;
            }

            include_once $file;

            // Skip abstract classes.
            $reflection = new \ReflectionClass($className);
            if ($reflection->isAbstract() === true) {
                continue;
            }

            $listeners[$className] = $className;

            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                echo "Registered $className".PHP_EOL;
            }
        }//end foreach

        $this->sniffs = $listeners;

    }//end registerSniffs()


    /**
     * Populates the array of PHP_CodeSniffer_Sniff's for this file.
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

            // Work out the internal code for this sniff.
            $parts = explode('\\', $sniffClass);
            $code = $parts[2].'.'.$parts[4].'.'.$parts[5];
            $code = substr($code, 0, -5);

            $this->sniffs[$sniffClass] = new $sniffClass();

            // Set custom properties.
            if (isset($this->ruleset[$code]['properties']) === true) {
                foreach ($this->ruleset[$code]['properties'] as $name => $value) {
                    $this->setSniffProperty($sniffClass, $name, $value);
                }
            }

            $tokenizers = array();
            $vars       = get_class_vars($sniffClass);
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

            $parts          = explode('\\', $sniffClass);
            $listenerSource = $parts[2].'.'.$parts[4].'.'.substr($parts[5], 0, -5);
            $ignorePatterns = array();
            $patterns       = $this->getIgnorePatterns($listenerSource);
            foreach ($patterns as $pattern => $type) {
                // While there is support for a type of each pattern
                // (absolute or relative) we don't actually support it here.
                $replacements = array(
                                 '\\,' => ',',
                                 '*'   => '.*',
                                );

                $ignorePatterns[] = strtr($pattern, $replacements);
            }

            foreach ($tokens as $token) {
                if (isset($this->tokenListeners[$token]) === false) {
                    $this->tokenListeners[$token] = array();
                }

                if (isset($this->tokenListeners[$token][$sniffClass]) === false) {
                    $this->tokenListeners[$token][$sniffClass] = array(
                                                                      'class'      => $sniffClass,
                                                                      'source'     => $listenerSource,
                                                                      'tokenizers' => $tokenizers,
                                                                      'ignore'     => $ignorePatterns,
                                                                     );
                }
            }
        }//end foreach

    }//end populateTokenListeners()


    /**
     * Set a single property for a sniff.
     *
     * @param string $sniffClass The class name of the sniff.
     * @param string $name          The name of the property to change.
     * @param string $value         The new value of the property.
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

    /**
     * Gets the array of ignore patterns.
     *
     * Optionally takes a listener to get ignore patterns specified
     * for that sniff only.
     *
     * @param string $listener The listener to get patterns for. If NULL, all
     *                         patterns are returned.
     *
     * @return array
     */
    public function getIgnorePatterns($listener=null)
    {
        if ($listener === null) {
            return $this->ignorePatterns;
        }

        if (isset($this->ignorePatterns[$listener]) === true) {
            return $this->ignorePatterns[$listener];
        }

        return array();

    }//end getIgnorePatterns()

}