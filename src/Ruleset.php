<?php
/**
 * Stores the rules used to check and fix files.
 *
 * A ruleset object directly maps to a ruleset XML file.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Util;
use PHP_CodeSniffer\Exceptions\RuntimeException;

class Ruleset
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
    public $paths = [];

    /**
     * A list of regular expressions used to ignore specific sniffs for files and folders.
     *
     * Is also used to set global exclude patterns.
     * The key is the regular expression and the value is the type
     * of ignore pattern (absolute or relative).
     *
     * @var array<string, string>
     */
    public $ignorePatterns = [];

    /**
     * A list of regular expressions used to include specific sniffs for files and folders.
     *
     * The key is the sniff code and the value is an array with
     * the key being a regular expression and the value is the type
     * of ignore pattern (absolute or relative).
     *
     * @var array<string, array<string, string>>
     */
    public $includePatterns = [];

    /**
     * An array of sniff objects that are being used to check files.
     *
     * The key is the fully qualified name of the sniff class
     * and the value is the sniff object.
     *
     * @var array<string, \PHP_CodeSniffer\Sniffs\Sniff>
     */
    public $sniffs = [];

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
     * @var array<int, \PHP_CodeSniffer\Sniffs\Sniff>
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
     * The directories that the processed rulesets are in.
     *
     * @var string[]
     */
    protected $rulesetDirs = [];

    /**
     * The config data for the run.
     *
     * @var \PHP_CodeSniffer\Config
     */
    private $config = null;


    /**
     * Initialise the ruleset that the run will use.
     *
     * @param \PHP_CodeSniffer\Config $config The config data for the run.
     *
     * @return void
     */
    public function __construct(Config $config)
    {
        // Ignore sniff restrictions if caching is on.
        $restrictions = [];
        $exclusions   = [];
        if ($config->cache === false) {
            $restrictions = $config->sniffs;
            $exclusions   = $config->exclude;
        }

        $this->config = $config;
        $sniffs       = [];

        $standardPaths = [];
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

            $standardPaths[] = $standard;
        }

        foreach ($standardPaths as $standard) {
            $ruleset = @simplexml_load_string(file_get_contents($standard));
            if ($ruleset !== false) {
                $standardName = (string) $ruleset['name'];
                if ($this->name !== '') {
                    $this->name .= ', ';
                }

                $this->name .= $standardName;

                // Allow autoloading of custom files inside this standard.
                if (isset($ruleset['namespace']) === true) {
                    $namespace = (string) $ruleset['namespace'];
                } else {
                    $namespace = basename(dirname($standard));
                }

                Autoload::addSearchPath(dirname($standard), $namespace);
            }

            if (defined('PHP_CODESNIFFER_IN_TESTS') === true && empty($restrictions) === false) {
                // In unit tests, only register the sniffs that the test wants and not the entire standard.
                try {
                    foreach ($restrictions as $restriction) {
                        $sniffs = array_merge($sniffs, $this->expandRulesetReference($restriction, dirname($standard)));
                    }
                } catch (RuntimeException $e) {
                    // Sniff reference could not be expanded, which probably means this
                    // is an installed standard. Let the unit test system take care of
                    // setting the correct sniff for testing.
                    return;
                }

                break;
            }

            if (PHP_CODESNIFFER_VERBOSITY === 1) {
                echo "Registering sniffs in the $standardName standard... ";
                if (count($config->standards) > 1 || PHP_CODESNIFFER_VERBOSITY > 2) {
                    echo PHP_EOL;
                }
            }

            $sniffs = array_merge($sniffs, $this->processRuleset($standard));
        }//end foreach

        $sniffRestrictions = [];
        foreach ($restrictions as $sniffCode) {
            $parts     = explode('.', strtolower($sniffCode));
            $sniffName = $parts[0].'\sniffs\\'.$parts[1].'\\'.$parts[2].'sniff';
            $sniffRestrictions[$sniffName] = true;
        }

        $sniffExclusions = [];
        foreach ($exclusions as $sniffCode) {
            $parts     = explode('.', strtolower($sniffCode));
            $sniffName = $parts[0].'\sniffs\\'.$parts[1].'\\'.$parts[2].'sniff';
            $sniffExclusions[$sniffName] = true;
        }

        $this->registerSniffs($sniffs, $sniffRestrictions, $sniffExclusions);
        $this->populateTokenListeners();

        $numSniffs = count($this->sniffs);
        if (PHP_CODESNIFFER_VERBOSITY === 1) {
            echo "DONE ($numSniffs sniffs registered)".PHP_EOL;
        }

        if ($numSniffs === 0) {
            throw new RuntimeException('No sniffs were registered');
        }

    }//end __construct()


    /**
     * Prints a report showing the sniffs contained in a standard.
     *
     * @return void
     */
    public function explain()
    {
        $sniffs = array_keys($this->sniffCodes);
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
                $currentStandard = substr($sniff, 0, strpos($sniff, '.'));
                if ($lastStandard === null) {
                    $lastStandard = $currentStandard;
                }
            }

            if ($currentStandard !== $lastStandard) {
                $sniffList = ob_get_contents();
                ob_end_clean();

                echo PHP_EOL.$lastStandard.' ('.$lastCount.' sniff';
                if ($lastCount > 1) {
                    echo 's';
                }

                echo ')'.PHP_EOL;
                echo str_repeat('-', (strlen($lastStandard.$lastCount) + 10));
                echo PHP_EOL;
                echo $sniffList;

                $lastStandard = $currentStandard;
                $lastCount    = 0;

                if ($currentStandard === null) {
                    break;
                }

                ob_start();
            }//end if

            echo '  '.$sniff.PHP_EOL;
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
     * @return string[]
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the ruleset path is invalid.
     */
    public function processRuleset($rulesetPath, $depth=0)
    {
        $rulesetPath = Util\Common::realpath($rulesetPath);
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo str_repeat("\t", $depth);
            echo 'Processing ruleset '.Util\Common::stripBasepath($rulesetPath, $this->config->basepath).PHP_EOL;
        }

        libxml_use_internal_errors(true);
        $ruleset = simplexml_load_string(file_get_contents($rulesetPath));
        if ($ruleset === false) {
            $errorMsg = "Ruleset $rulesetPath is not valid".PHP_EOL;
            $errors   = libxml_get_errors();
            foreach ($errors as $error) {
                $errorMsg .= '- On line '.$error->line.', column '.$error->column.': '.$error->message;
            }

            libxml_clear_errors();
            throw new RuntimeException($errorMsg);
        }

        libxml_use_internal_errors(false);

        $ownSniffs      = [];
        $includedSniffs = [];
        $excludedSniffs = [];

        $this->paths[]       = $rulesetPath;
        $rulesetDir          = dirname($rulesetPath);
        $this->rulesetDirs[] = $rulesetDir;

        $sniffDir = $rulesetDir.DIRECTORY_SEPARATOR.'Sniffs';
        if (is_dir($sniffDir) === true) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\tAdding sniff files from ".Util\Common::stripBasepath($sniffDir, $this->config->basepath).' directory'.PHP_EOL;
            }

            $ownSniffs = $this->expandSniffDirectory($sniffDir, $depth);
        }

        // Included custom autoloaders.
        foreach ($ruleset->{'autoload'} as $autoload) {
            if ($this->shouldProcessElement($autoload) === false) {
                continue;
            }

            $autoloadPath = (string) $autoload;
            if (is_file($autoloadPath) === false) {
                $autoloadPath = Util\Common::realPath(dirname($rulesetPath).DIRECTORY_SEPARATOR.$autoloadPath);
            }

            if ($autoloadPath === false) {
                throw new RuntimeException('The specified autoload file "'.$autoload.'" does not exist');
            }

            include_once $autoloadPath;

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t=> included autoloader $autoloadPath".PHP_EOL;
            }
        }//end foreach

        // Process custom sniff config settings.
        foreach ($ruleset->{'config'} as $config) {
            if ($this->shouldProcessElement($config) === false) {
                continue;
            }

            Config::setConfigData((string) $config['name'], (string) $config['value'], true);
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t=> set config value ".(string) $config['name'].': '.(string) $config['value'].PHP_EOL;
            }
        }

        foreach ($ruleset->rule as $rule) {
            if (isset($rule['ref']) === false
                || $this->shouldProcessElement($rule) === false
            ) {
                continue;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\tProcessing rule \"".$rule['ref'].'"'.PHP_EOL;
            }

            $expandedSniffs = $this->expandRulesetReference((string) $rule['ref'], $rulesetDir, $depth);
            $newSniffs      = array_diff($expandedSniffs, $includedSniffs);
            $includedSniffs = array_merge($includedSniffs, $expandedSniffs);

            $parts = explode('.', $rule['ref']);
            if (count($parts) === 4
                && $parts[0] !== ''
                && $parts[1] !== ''
                && $parts[2] !== ''
            ) {
                $sniffCode = $parts[0].'.'.$parts[1].'.'.$parts[2];
                if (isset($this->ruleset[$sniffCode]['severity']) === true
                    && $this->ruleset[$sniffCode]['severity'] === 0
                ) {
                    // This sniff code has already been turned off, but now
                    // it is being explicitly included again, so turn it back on.
                    $this->ruleset[(string) $rule['ref']]['severity'] = 5;
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo "\t\t* disabling sniff exclusion for specific message code *".PHP_EOL;
                        echo str_repeat("\t", $depth);
                        echo "\t\t=> severity set to 5".PHP_EOL;
                    }
                } else if (empty($newSniffs) === false) {
                    $newSniff = $newSniffs[0];
                    if (in_array($newSniff, $ownSniffs, true) === false) {
                        // Including a sniff that hasn't been included higher up, but
                        // only including a single message from it. So turn off all messages in
                        // the sniff, except this one.
                        $this->ruleset[$sniffCode]['severity']            = 0;
                        $this->ruleset[(string) $rule['ref']]['severity'] = 5;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo "\t\tExcluding sniff \"".$sniffCode.'" except for "'.$parts[3].'"'.PHP_EOL;
                        }
                    }
                }//end if
            }//end if

            if (isset($rule->exclude) === true) {
                foreach ($rule->exclude as $exclude) {
                    if (isset($exclude['name']) === false) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo "\t\t* ignoring empty exclude rule *".PHP_EOL;
                            echo "\t\t\t=> ".$exclude->asXML().PHP_EOL;
                        }

                        continue;
                    }

                    if ($this->shouldProcessElement($exclude) === false) {
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
                            $this->expandRulesetReference($exclude['name'], $rulesetDir, ($depth + 1))
                        );
                    }
                }//end foreach
            }//end if

            $this->processRule($rule, $newSniffs, $depth);
        }//end foreach

        // Process custom command line arguments.
        $cliArgs = [];
        foreach ($ruleset->{'arg'} as $arg) {
            if ($this->shouldProcessElement($arg) === false) {
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

        // Set custom php ini values as CLI args.
        foreach ($ruleset->{'ini'} as $arg) {
            if ($this->shouldProcessElement($arg) === false) {
                continue;
            }

            if (isset($arg['name']) === false) {
                continue;
            }

            $name      = (string) $arg['name'];
            $argString = $name;
            if (isset($arg['value']) === true) {
                $value      = (string) $arg['value'];
                $argString .= "=$value";
            } else {
                $value = 'true';
            }

            $cliArgs[] = '-d';
            $cliArgs[] = $argString;

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t=> set PHP ini value $name to $value".PHP_EOL;
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
            // Change the directory so all relative paths are worked
            // out based on the location of the ruleset instead of
            // the location of the user.
            $inPhar = Util\Common::isPharFile($rulesetDir);
            if ($inPhar === false) {
                $currentDir = getcwd();
                chdir($rulesetDir);
            }

            $this->config->setCommandLineValues($cliArgs);

            if ($inPhar === false) {
                chdir($currentDir);
            }
        }

        // Process custom ignore pattern rules.
        foreach ($ruleset->{'exclude-pattern'} as $pattern) {
            if ($this->shouldProcessElement($pattern) === false) {
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
        $files = [];
        foreach ($includedSniffs as $sniff) {
            if (in_array($sniff, $excludedSniffs, true) === true) {
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
    private function expandSniffDirectory($directory, $depth=0)
    {
        $sniffs = [];

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

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t=> ".Util\Common::stripBasepath($path, $this->config->basepath).PHP_EOL;
            }

            $sniffs[] = $path;
        }//end foreach

        return $sniffs;

    }//end expandSniffDirectory()


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
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the reference is invalid.
     */
    private function expandRulesetReference($ref, $rulesetDir, $depth=0)
    {
        // Ignore internal sniffs codes as they are used to only
        // hide and change internal messages.
        if (substr($ref, 0, 9) === 'Internal.') {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo str_repeat("\t", $depth);
                echo "\t\t* ignoring internal sniff code *".PHP_EOL;
            }

            return [];
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
                    echo "\t\t=> ".Util\Common::stripBasepath($ref, $this->config->basepath).PHP_EOL;
                }
            }
        }

        // As sniffs can't begin with a tilde, assume references in
        // this format are relative to the user's home directory.
        if (substr($ref, 0, 2) === '~/') {
            $realpath = Util\Common::realpath($ref);
            if ($realpath !== false) {
                $ref = $realpath;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> ".Util\Common::stripBasepath($ref, $this->config->basepath).PHP_EOL;
                }
            }
        }

        if (is_file($ref) === true) {
            if (substr($ref, -9) === 'Sniff.php') {
                // A single external sniff.
                $this->rulesetDirs[] = dirname(dirname(dirname($ref)));
                return [$ref];
            }
        } else {
            // See if this is a whole standard being referenced.
            $path = Util\Standards::getInstalledStandardPath($ref);
            if (Util\Common::isPharFile($path) === true && strpos($path, 'ruleset.xml') === false) {
                // If the ruleset exists inside the phar file, use it.
                if (file_exists($path.DIRECTORY_SEPARATOR.'ruleset.xml') === true) {
                    $path .= DIRECTORY_SEPARATOR.'ruleset.xml';
                } else {
                    $path = null;
                }
            }

            if ($path !== null) {
                $ref = $path;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> ".Util\Common::stripBasepath($ref, $this->config->basepath).PHP_EOL;
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

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> ".Util\Common::stripBasepath($ref, $this->config->basepath).PHP_EOL;
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

                return $this->expandSniffDirectory($ref, ($depth + 1));
            }
        } else {
            if (is_file($ref) === false) {
                $error = "Referenced sniff \"$ref\" does not exist";
                throw new RuntimeException($error);
            }

            if (substr($ref, -9) === 'Sniff.php') {
                // A single sniff.
                return [$ref];
            } else {
                // Assume an external ruleset.xml file.
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t* rule is referencing a standard using ruleset path; processing *".PHP_EOL;
                }

                return $this->processRuleset($ref, ($depth + 2));
            }
        }//end if

    }//end expandRulesetReference()


    /**
     * Processes a rule from a ruleset XML file, overriding built-in defaults.
     *
     * @param \SimpleXMLElement $rule      The rule object from a ruleset XML file.
     * @param string[]          $newSniffs An array of sniffs that got included by this rule.
     * @param int               $depth     How many nested processing steps we are in.
     *                                     This is only used for debug output.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If rule settings are invalid.
     */
    private function processRule($rule, $newSniffs, $depth=0)
    {
        $ref  = (string) $rule['ref'];
        $todo = [$ref];

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
            // Custom severity.
            if (isset($rule->severity) === true
                && $this->shouldProcessElement($rule->severity) === true
            ) {
                if (isset($this->ruleset[$code]) === false) {
                    $this->ruleset[$code] = [];
                }

                $this->ruleset[$code]['severity'] = (int) $rule->severity;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> severity set to ".(int) $rule->severity;
                    if ($code !== $ref) {
                        echo " for $code";
                    }

                    echo PHP_EOL;
                }
            }

            // Custom message type.
            if (isset($rule->type) === true
                && $this->shouldProcessElement($rule->type) === true
            ) {
                if (isset($this->ruleset[$code]) === false) {
                    $this->ruleset[$code] = [];
                }

                $type = strtolower((string) $rule->type);
                if ($type !== 'error' && $type !== 'warning') {
                    throw new RuntimeException("Message type \"$type\" is invalid; must be \"error\" or \"warning\"");
                }

                $this->ruleset[$code]['type'] = $type;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> message type set to ".(string) $rule->type;
                    if ($code !== $ref) {
                        echo " for $code";
                    }

                    echo PHP_EOL;
                }
            }//end if

            // Custom message.
            if (isset($rule->message) === true
                && $this->shouldProcessElement($rule->message) === true
            ) {
                if (isset($this->ruleset[$code]) === false) {
                    $this->ruleset[$code] = [];
                }

                $this->ruleset[$code]['message'] = (string) $rule->message;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> message set to ".(string) $rule->message;
                    if ($code !== $ref) {
                        echo " for $code";
                    }

                    echo PHP_EOL;
                }
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
                        $this->ruleset[$code] = [
                            'properties' => [],
                        ];
                    } else if (isset($this->ruleset[$code]['properties']) === false) {
                        $this->ruleset[$code]['properties'] = [];
                    }

                    $name = (string) $prop['name'];
                    if (isset($prop['type']) === true
                        && (string) $prop['type'] === 'array'
                    ) {
                        $values = [];
                        if (isset($prop['extend']) === true
                            && (string) $prop['extend'] === 'true'
                            && isset($this->ruleset[$code]['properties'][$name]) === true
                        ) {
                            $values = $this->ruleset[$code]['properties'][$name];
                        }

                        if (isset($prop->element) === true) {
                            $printValue = '';
                            foreach ($prop->element as $element) {
                                if ($this->shouldProcessElement($element) === false) {
                                    continue;
                                }

                                $value = (string) $element['value'];
                                if (isset($element['key']) === true) {
                                    $key          = (string) $element['key'];
                                    $values[$key] = $value;
                                    $printValue  .= $key.'=>'.$value.',';
                                } else {
                                    $values[]    = $value;
                                    $printValue .= $value.',';
                                }
                            }

                            $printValue = rtrim($printValue, ',');
                        } else {
                            $value      = (string) $prop['value'];
                            $printValue = $value;
                            foreach (explode(',', $value) as $val) {
                                list($k, $v) = explode('=>', $val.'=>');
                                if ($v !== '') {
                                    $values[trim($k)] = trim($v);
                                } else {
                                    $values[] = trim($k);
                                }
                            }
                        }//end if

                        $this->ruleset[$code]['properties'][$name] = $values;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo "\t\t=> array property \"$name\" set to \"$printValue\"";
                            if ($code !== $ref) {
                                echo " for $code";
                            }

                            echo PHP_EOL;
                        }
                    } else {
                        $this->ruleset[$code]['properties'][$name] = (string) $prop['value'];
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo "\t\t=> property \"$name\" set to \"".(string) $prop['value'].'"';
                            if ($code !== $ref) {
                                echo " for $code";
                            }

                            echo PHP_EOL;
                        }
                    }//end if
                }//end foreach
            }//end if

            // Ignore patterns.
            foreach ($rule->{'exclude-pattern'} as $pattern) {
                if ($this->shouldProcessElement($pattern) === false) {
                    continue;
                }

                if (isset($this->ignorePatterns[$code]) === false) {
                    $this->ignorePatterns[$code] = [];
                }

                if (isset($pattern['type']) === false) {
                    $pattern['type'] = 'absolute';
                }

                $this->ignorePatterns[$code][(string) $pattern] = (string) $pattern['type'];
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> added rule-specific ".(string) $pattern['type'].' ignore pattern';
                    if ($code !== $ref) {
                        echo " for $code";
                    }

                    echo ': '.(string) $pattern.PHP_EOL;
                }
            }//end foreach

            // Include patterns.
            foreach ($rule->{'include-pattern'} as $pattern) {
                if ($this->shouldProcessElement($pattern) === false) {
                    continue;
                }

                if (isset($this->includePatterns[$code]) === false) {
                    $this->includePatterns[$code] = [];
                }

                if (isset($pattern['type']) === false) {
                    $pattern['type'] = 'absolute';
                }

                $this->includePatterns[$code][(string) $pattern] = (string) $pattern['type'];
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo "\t\t=> added rule-specific ".(string) $pattern['type'].' include pattern';
                    if ($code !== $ref) {
                        echo " for $code";
                    }

                    echo ': '.(string) $pattern.PHP_EOL;
                }
            }//end foreach
        }//end foreach

    }//end processRule()


    /**
     * Determine if an element should be processed or ignored.
     *
     * @param \SimpleXMLElement $element An object from a ruleset XML file.
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

    }//end shouldProcessElement()


    /**
     * Loads and stores sniffs objects used for sniffing files.
     *
     * @param array $files        Paths to the sniff files to register.
     * @param array $restrictions The sniff class names to restrict the allowed
     *                            listeners to.
     * @param array $exclusions   The sniff class names to exclude from the
     *                            listeners list.
     *
     * @return void
     */
    public function registerSniffs($files, $restrictions, $exclusions)
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

            $className   = Autoload::loadFile($file);
            $compareName = Util\Common::cleanSniffClass($className);

            // If they have specified a list of sniffs to restrict to, check
            // to see if this sniff is allowed.
            if (empty($restrictions) === false
                && isset($restrictions[$compareName]) === false
            ) {
                continue;
            }

            // If they have specified a list of sniffs to exclude, check
            // to see if this sniff is allowed.
            if (empty($exclusions) === false
                && isset($exclusions[$compareName]) === true
            ) {
                continue;
            }

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
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If sniff registration fails.
     */
    public function populateTokenListeners()
    {
        // Construct a list of listeners indexed by token being listened for.
        $this->tokenListeners = [];

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

            $tokenizers = [];
            $vars       = get_class_vars($sniffClass);
            if (isset($vars['supportedTokenizers']) === true) {
                foreach ($vars['supportedTokenizers'] as $tokenizer) {
                    $tokenizers[$tokenizer] = $tokenizer;
                }
            } else {
                $tokenizers = ['PHP' => 'PHP'];
            }

            $tokens = $this->sniffs[$sniffClass]->register();
            if (is_array($tokens) === false) {
                $msg = "Sniff $sniffClass register() method must return an array";
                throw new RuntimeException($msg);
            }

            $ignorePatterns = [];
            $patterns       = $this->getIgnorePatterns($sniffCode);
            foreach ($patterns as $pattern => $type) {
                $replacements = [
                    '\\,' => ',',
                    '*'   => '.*',
                ];

                $ignorePatterns[] = strtr($pattern, $replacements);
            }

            $includePatterns = [];
            $patterns        = $this->getIncludePatterns($sniffCode);
            foreach ($patterns as $pattern => $type) {
                $replacements = [
                    '\\,' => ',',
                    '*'   => '.*',
                ];

                $includePatterns[] = strtr($pattern, $replacements);
            }

            foreach ($tokens as $token) {
                if (isset($this->tokenListeners[$token]) === false) {
                    $this->tokenListeners[$token] = [];
                }

                if (isset($this->tokenListeners[$token][$sniffClass]) === false) {
                    $this->tokenListeners[$token][$sniffClass] = [
                        'class'      => $sniffClass,
                        'source'     => $sniffCode,
                        'tokenizers' => $tokenizers,
                        'ignore'     => $ignorePatterns,
                        'include'    => $includePatterns,
                    ];
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

        if ($value === '') {
            $value = null;
        }

        // Special case for booleans.
        if ($value === 'true') {
            $value = true;
        } else if ($value === 'false') {
            $value = false;
        } else if (substr($name, -2) === '[]') {
            $name   = substr($name, 0, -2);
            $values = [];
            if ($value !== null) {
                foreach (explode(',', $value) as $val) {
                    list($k, $v) = explode('=>', $val.'=>');
                    if ($v !== '') {
                        $values[trim($k)] = trim($v);
                    } else {
                        $values[] = trim($k);
                    }
                }
            }

            $value = $values;
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

        return [];

    }//end getIgnorePatterns()


    /**
     * Gets the array of include patterns.
     *
     * Optionally takes a listener to get include patterns specified
     * for that sniff only.
     *
     * @param string $listener The listener to get patterns for. If NULL, all
     *                         patterns are returned.
     *
     * @return array
     */
    public function getIncludePatterns($listener=null)
    {
        if ($listener === null) {
            return $this->includePatterns;
        }

        if (isset($this->includePatterns[$listener]) === true) {
            return $this->includePatterns[$listener];
        }

        return [];

    }//end getIncludePatterns()


}//end class
