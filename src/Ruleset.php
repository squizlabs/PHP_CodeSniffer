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

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Standards;

class Ruleset
{

    /**
     * A list of CLI args found while processing.
     *
     * @var []
     */
    private $cliArgs = [];

    /**
     * The config data for the run.
     *
     * @var \PHP_CodeSniffer\Config
     */
    private $config = null;

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
     * A mapping of sniff codes to fully qualified class names.
     *
     * The key is the sniff code and the value
     * is the fully qualified name of the sniff class.
     *
     * @var array<string, string>
     */
    public $sniffCodes = [];

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
     * An array of token types and the sniffs that are listening for them.
     *
     * The key is the token name being listened for and the value
     * is the sniff object.
     *
     * @var array<int, \PHP_CodeSniffer\Sniffs\Sniff>
     */
    public $tokenListeners = [];


    /**
     * Initialise the ruleset that the run will use.
     *
     * @param \PHP_CodeSniffer\Config $config The config data for the run.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If no sniffs were registered.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $restrictions = $config->sniffs;
        $exclusions   = $config->exclude;
        $sniffs       = [];

        $standardPaths = [];
        foreach ($config->standards as $standard) {
            $installed = Standards::getInstalledStandardPath($standard);
            if ($installed === null) {
                $standard = Common::realpath($standard);
                if (is_dir($standard) === true
                    && is_file(Common::realpath($standard.DIRECTORY_SEPARATOR.'ruleset.xml')) === true
                ) {
                    $standard = Common::realpath($standard.DIRECTORY_SEPARATOR.'ruleset.xml');
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
                Common::printStatusMessage("Registering sniffs in the $standardName standard... ", 0, true);
                if (count($config->standards) > 1 || PHP_CODESNIFFER_VERBOSITY > 2) {
                    Common::printStatusMessage(PHP_EOL, 0, true);
                }
            }

            $sniffs = array_merge($sniffs, $this->processRuleset($standard));
        }//end foreach

        // Ignore sniff restrictions if caching is on.
        if ($config->cache === true) {
            $restrictions = [];
            $exclusions   = [];
        }

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
            Common::printStatusMessage("DONE ($numSniffs sniffs registered)");
        }

        if ($numSniffs === 0) {
            throw new RuntimeException('No sniffs were registered');
        }

    }//end __construct()


    /**
     * Get the config that this ruleset is using.
     *
     * @return \PHP_CodeSniffer\Config
     */
    public function getConfig()
    {
        return $this->config;

    }//end getConfig()


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
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException - If the ruleset path is invalid.
     *                                                      - If a specified autoload file could not be found.
     */
    public function processRuleset($rulesetPath, $depth=0)
    {
        $rulesetPath = Common::realpath($rulesetPath);
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            Common::printStatusMessage('Processing ruleset '.Common::stripBasepath($rulesetPath, $this->config->basepath), $depth);
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
                Common::printStatusMessage('Adding sniff files from '.Common::stripBasepath($sniffDir, $this->config->basepath).' directory', ($depth + 1));
            }

            $ownSniffs = $this->expandSniffDirectory($sniffDir, $depth);
        }

        foreach ($ruleset->children() as $child) {
            if ($this->shouldProcessElement($child) === false) {
                continue;
            }

            switch ($child->getName()) {
            case 'arg':
                // Process custom command line arguments.
                if (isset($child['name']) === true) {
                    $argString = '--'.(string) $child['name'];
                    if (isset($child['value']) === true) {
                        $argString .= '='.(string) $child['value'];
                    }
                } else {
                    $argString = '-'.(string) $child['value'];
                }

                $this->cliArgs[] = $argString;

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage("=> set command line value $argString", ($depth + 1));
                }
                break;
            case 'autoload':
                // Include custom autoloaders.
                $autoloadPath = (string) $child;

                // Try relative autoload paths first.
                $relativePath = Common::realPath(dirname($rulesetPath).DIRECTORY_SEPARATOR.$autoloadPath);

                if ($relativePath !== false && is_file($relativePath) === true) {
                    $autoloadPath = $relativePath;
                } else if (is_file($autoloadPath) === false) {
                    throw new RuntimeException('The specified autoload file "'.$autoloadPath.'" does not exist');
                }

                include_once $autoloadPath;

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage("=> included autoloader $autoloadPath", ($depth + 1));
                }
                break;
            case 'config':
                // Process custom sniff config settings.
                $this->config->setConfigData((string) $child['name'], (string) $child['value'], true);
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('=> set config value '.(string) $child['name'].': '.(string) $child['value'], ($depth + 1));
                }
                break;
            case 'exclude-pattern':
                // Process custom ignore pattern rules.
                if (isset($child['type']) === false) {
                    $child['type'] = 'absolute';
                }

                $this->ignorePatterns[(string) $child] = (string) $child['type'];
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('=> added global '.(string) $child['type'].' ignore pattern: '.(string) $child, ($depth + 1));
                }
                break;
            case 'file':
                // Process hard-coded file paths.
                if (empty($this->config->files) === false) {
                    break;
                }

                $file            = (string) $child;
                $this->cliArgs[] = $file;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage("=> added \"$file\" to the file list", ($depth + 1));
                }
                break;
            case 'ini':
                // Set custom php ini values as CLI args.
                if (isset($child['name']) === false) {
                    break;
                }

                $name      = (string) $child['name'];
                $argString = $name;
                if (isset($child['value']) === true) {
                    $value      = (string) $child['value'];
                    $argString .= "=$value";
                } else {
                    $value = 'true';
                }

                $this->cliArgs[] = '-d';
                $this->cliArgs[] = $argString;

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage("=> set PHP ini value $name to $value", ($depth + 1));
                }
                break;
            case 'rule':
                if (isset($child['ref']) === false) {
                    break;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('Processing rule "'.$child['ref'].'"', ($depth + 1));
                }

                $expandedSniffs = $this->expandRulesetReference((string) $child['ref'], $rulesetDir, $depth);
                $newSniffs      = array_diff($expandedSniffs, $includedSniffs);
                $includedSniffs = array_merge($includedSniffs, $expandedSniffs);

                $parts = explode('.', $child['ref']);
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
                        $this->ruleset[(string) $child['ref']]['severity'] = 5;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            Common::printStatusMessage('* disabling sniff exclusion for specific message code *', ($depth + 2));
                            Common::printStatusMessage('=> severity set to 5', ($depth + 2));
                        }
                    } else if (empty($newSniffs) === false) {
                        $newSniff = $newSniffs[0];
                        if (in_array($newSniff, $ownSniffs, true) === false) {
                            // Including a sniff that hasn't been included higher up, but
                            // only including a single message from it. So turn off all messages in
                            // the sniff, except this one.
                            $this->ruleset[$sniffCode]['severity'] = 0;
                            $this->ruleset[(string) $child['ref']]['severity'] = 5;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                Common::printStatusMessage('Excluding sniff "'.$sniffCode.'" except for "'.$parts[3].'"', ($depth + 2));
                            }
                        }
                    }//end if
                }//end if

                if (isset($child->exclude) === true) {
                    foreach ($child->exclude as $exclude) {
                        if (isset($exclude['name']) === false) {
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                Common::printStatusMessage('* ignoring empty exclude rule *', ($depth + 2));
                                Common::printStatusMessage('=> '.$exclude->asXML(), ($depth + 3));
                            }

                            continue;
                        }

                        if ($this->shouldProcessElement($exclude) === false) {
                            continue;
                        }

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            Common::printStatusMessage('Excluding rule "'.$exclude['name'].'"', ($depth + 2));
                        }

                        // Check if a single code is being excluded, which is a shortcut
                        // for setting the severity of the message to 0.
                        $parts = explode('.', $exclude['name']);
                        if (count($parts) === 4) {
                            $this->ruleset[(string) $exclude['name']]['severity'] = 0;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                Common::printStatusMessage('=> severity set to 0', ($depth + 2));
                            }
                        } else {
                            $excludedSniffs = array_merge(
                                $excludedSniffs,
                                $this->expandRulesetReference((string) $exclude['name'], $rulesetDir, ($depth + 1))
                            );
                        }
                    }//end foreach
                }//end if

                $this->processRule($child, $newSniffs, $depth);
                break;
            }//end switch
        }//end foreach

        if ($depth === 0 && empty($this->cliArgs) === false) {
            // Change the directory so all relative paths are worked
            // out based on the location of the ruleset instead of
            // the location of the user.
            $inPhar = Common::isPharFile($rulesetDir);
            if ($inPhar === false) {
                $currentDir = getcwd();
                chdir($rulesetDir);
            }

            $this->config->setCommandLineValues($this->cliArgs);

            if ($inPhar === false) {
                chdir($currentDir);
            }
        }

        $includedSniffs = array_unique(array_merge($ownSniffs, $includedSniffs));
        $excludedSniffs = array_unique($excludedSniffs);

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $included = count($includedSniffs);
            $excluded = count($excludedSniffs);
            Common::printStatusMessage("=> Ruleset processing complete; included $included sniffs and excluded $excluded", $depth);
        }

        // Merge our own sniff list with our externally included
        // sniff list, but filter out any excluded sniffs.
        $files = [];
        foreach ($includedSniffs as $sniff) {
            if (in_array($sniff, $excludedSniffs, true) === true) {
                continue;
            } else {
                $files[] = Common::realpath($sniff);
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
                Common::printStatusMessage('=> '.Common::stripBasepath($path, $this->config->basepath), ($depth + 2));
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
                Common::printStatusMessage('* ignoring internal sniff code *', ($depth + 2));
            }

            return [];
        }

        // As sniffs can't begin with a full stop, assume references in
        // this format are relative paths and attempt to convert them
        // to absolute paths. If this fails, let the reference run through
        // the normal checks and have it fail as normal.
        if (substr($ref, 0, 1) === '.') {
            $realpath = Common::realpath($rulesetDir.'/'.$ref);
            if ($realpath !== false) {
                $ref = $realpath;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('=> '.Common::stripBasepath($ref, $this->config->basepath), ($depth + 2));
                }
            }
        }

        // As sniffs can't begin with a tilde, assume references in
        // this format are relative to the user's home directory.
        if (substr($ref, 0, 2) === '~/') {
            $realpath = Common::realpath($ref);
            if ($realpath !== false) {
                $ref = $realpath;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('=> '.Common::stripBasepath($ref, $this->config->basepath), ($depth + 2));
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
            $path = Standards::getInstalledStandardPath($ref);
            if ($path !== null && Common::isPharFile($path) === true && strpos($path, 'ruleset.xml') === false) {
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
                    Common::printStatusMessage('=> '.Common::stripBasepath($ref, $this->config->basepath), ($depth + 2));
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
                $stdPath = Standards::getInstalledStandardPath($stdName);
                if ($stdPath !== null && $path !== '') {
                    if (Common::isPharFile($stdPath) === true
                        && strpos($stdPath, 'ruleset.xml') === false
                    ) {
                        // Phar files can only return the directory,
                        // since ruleset can be omitted if building one standard.
                        $newRef = Common::realpath($stdPath.$path);
                    } else {
                        $newRef = Common::realpath(dirname($stdPath).$path);
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

                        $newRef = Common::realpath($dir.$path);

                        if ($newRef !== false) {
                            $ref = $newRef;
                        }
                    }
                } else {
                    $ref = $newRef;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('=> '.Common::stripBasepath($ref, $this->config->basepath), ($depth + 2));
                }
            }//end if
        }//end if

        if (is_dir($ref) === true) {
            if (is_file($ref.DIRECTORY_SEPARATOR.'ruleset.xml') === true) {
                // We are referencing an external coding standard.
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('* rule is referencing a standard using directory name; processing *', ($depth + 2));
                }

                return $this->processRuleset($ref.DIRECTORY_SEPARATOR.'ruleset.xml', ($depth + 2));
            } else {
                // We are referencing a whole directory of sniffs.
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    Common::printStatusMessage('* rule is referencing a directory of sniffs *', ($depth + 2));
                    Common::printStatusMessage('Adding sniff files from directory', ($depth + 2));
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
                    Common::printStatusMessage('* rule is referencing a standard using ruleset path; processing *', ($depth + 2));
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

        $parts      = explode('.', $ref);
        $partsCount = count($parts);
        if ($partsCount <= 2
            || $partsCount > count(array_filter($parts))
            || in_array($ref, $newSniffs) === true
        ) {
            // We are processing a standard, a category of sniffs or a relative path inclusion.
            foreach ($newSniffs as $sniffFile) {
                $parts = explode(DIRECTORY_SEPARATOR, $sniffFile);
                if (count($parts) === 1 && DIRECTORY_SEPARATOR === '\\') {
                    // Path using forward slashes while running on Windows.
                    $parts = explode('/', $sniffFile);
                }

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
                    $statusMessage = '=> severity set to '.(int) $rule->severity;
                    if ($code !== $ref) {
                        $statusMessage .= " for $code";
                    }

                    Common::printStatusMessage($statusMessage, ($depth + 2));
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
                    $statusMessage = '=> message type set to '.(string) $rule->type;
                    if ($code !== $ref) {
                        $statusMessage .= " for $code";
                    }

                    Common::printStatusMessage($statusMessage, ($depth + 2));
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
                    $statusMessage = '=> message set to '.(string) $rule->message;
                    if ($code !== $ref) {
                        $statusMessage .= " for $code";
                    }

                    Common::printStatusMessage($statusMessage, ($depth + 2));
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
                        }

                        $this->ruleset[$code]['properties'][$name] = $values;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $statusMessage = "=> array property \"$name\" set to \"$printValue\"";
                            if ($code !== $ref) {
                                $statusMessage .= " for $code";
                            }

                            Common::printStatusMessage($statusMessage, ($depth + 2));
                        }
                    } else {
                        $this->ruleset[$code]['properties'][$name] = (string) $prop['value'];
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $statusMessage = "=> property \"$name\" set to \"".(string) $prop['value'].'"';
                            if ($code !== $ref) {
                                $statusMessage .= " for $code";
                            }

                            Common::printStatusMessage($statusMessage, ($depth + 2));
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
                    $statusMessage = '=> added rule-specific '.(string) $pattern['type'].' ignore pattern';
                    if ($code !== $ref) {
                        $statusMessage .= " for $code";
                    }

                    $statusMessage .= ': '.(string) $pattern;
                    Common::printStatusMessage($statusMessage, ($depth + 2));
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
                    $statusMessage = '=> added rule-specific '.(string) $pattern['type'].' include pattern';
                    if ($code !== $ref) {
                        $statusMessage .= " for $code";
                    }

                    $statusMessage .= ': '.(string) $pattern;
                    Common::printStatusMessage($statusMessage, ($depth + 2));
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
            $compareName = Common::cleanSniffClass($className);

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
                Common::printStatusMessage("Registered $className");
            }
        }//end foreach

        $this->sniffs = $listeners;

    }//end registerSniffs()


    /**
     * Populates the array of PHP_CodeSniffer_Sniff objects for this file.
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

            $sniffCode = Common::getSniffCode($sniffClass);
            $this->sniffCodes[$sniffCode] = $sniffClass;

            // Set custom properties.
            if (isset($this->ruleset[$sniffCode]['properties']) === true) {
                foreach ($this->ruleset[$sniffCode]['properties'] as $name => $value) {
                    $this->setSniffProperty($sniffClass, $name, $value);
                }
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
                        'class'   => $sniffClass,
                        'source'  => $sniffCode,
                        'ignore'  => $ignorePatterns,
                        'include' => $includePatterns,
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
