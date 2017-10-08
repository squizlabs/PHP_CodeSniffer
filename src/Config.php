<?php
/**
 * Stores the configuration used to run PHPCS and PHPCBF.
 *
 * Parses the command line to determine user supplied values
 * and provides functions to access data stored in config files.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Exceptions\DeepExitException;

class Config
{

    /**
     * The current version.
     *
     * @var string
     */
    const VERSION = '3.1.1';

    /**
     * Package stability; either stable, beta or alpha.
     *
     * @var string
     */
    const STABILITY = 'stable';

    /**
     * An array of settings that PHPCS and PHPCBF accept.
     *
     * This array is not meant to be accessed directly. Instead, use the settings
     * as if they are class member vars so the __get() and __set() magic methods
     * can be used to validate the values. For example, to set the verbosity level to
     * level 2, use $this->verbosity = 2; instead of accessing this property directly.
     *
     * The list of settings are:
     *
     * string[] files           The files and directories to check.
     * string[] standards       The standards being used for checking.
     * int      verbosity       How verbose the output should be.
     *                          0: no unnecessary output
     *                          1: basic output for files being checked
     *                          2: ruleset and file parsing output
     *                          3: sniff execution output
     * bool     interactive     Enable interactive checking mode.
     * bool     parallel        Check files in parallel.
     * bool     cache           Enable the use of the file cache.
     * bool     cacheFile       A file where the cache data should be written
     * bool     colors          Display colours in output.
     * bool     explain         Explain the coding standards.
     * bool     local           Process local files in directories only (no recursion).
     * bool     showSources     Show sniff source codes in report output.
     * bool     showProgress    Show basic progress information while running.
     * bool     quiet           Quiet mode; disables progress and verbose output.
     * bool     annotations     Process @codingStandard annotations.
     * int      tabWidth        How many spaces each tab is worth.
     * string   encoding        The encoding of the files being checked.
     * string[] sniffs          The sniffs that should be used for checking.
     *                          If empty, all sniffs in the supplied standards will be used.
     * string[] exclude         The sniffs that should be excluded from checking.
     *                          If empty, all sniffs in the supplied standards will be used.
     * string[] ignored         Regular expressions used to ignore files and folders during checking.
     * string   reportFile      A file where the report output should be written.
     * string   generator       The documentation generator to use.
     * string   filter          The filter to use for the run.
     * string[] bootstrap       One of more files to include before the run begins.
     * int      reportWidth     The maximum number of columns that reports should use for output.
     *                          Set to "auto" for have this value changed to the width of the terminal.
     * int      errorSeverity   The minimum severity an error must have to be displayed.
     * int      warningSeverity The minimum severity a warning must have to be displayed.
     * bool     recordErrors    Record the content of error messages as well as error counts.
     * string   suffix          A suffix to add to fixed files.
     * string   basepath        A file system location to strip from the paths of files shown in reports.
     * bool     stdin           Read content from STDIN instead of supplied files.
     * string   stdinContent    Content passed directly to PHPCS on STDIN.
     * string   stdinPath       The path to use for content passed on STDIN.
     *
     * array<string, string>      extensions File extensions that should be checked, and what tokenizer to use.
     *                                       E.g., array('inc' => 'PHP');
     * array<string, string|null> reports    The reports to use for printing output after the run.
     *                                       The format of the array is:
     *                                           array(
     *                                            'reportName1' => 'outputFile',
     *                                            'reportName2' => null,
     *                                           );
     *                                       If the array value is NULL, the report will be written to the screen.
     *
     * string[] unknown Any arguments gathered on the command line that are unknown to us.
     *                  E.g., using `phpcs -c` will give array('c');
     *
     * @var array<string, mixed>
     */
    private $settings = array(
                         'files'           => null,
                         'standards'       => null,
                         'verbosity'       => null,
                         'interactive'     => null,
                         'parallel'        => null,
                         'cache'           => null,
                         'cacheFile'       => null,
                         'colors'          => null,
                         'explain'         => null,
                         'local'           => null,
                         'showSources'     => null,
                         'showProgress'    => null,
                         'quiet'           => null,
                         'annotations'     => null,
                         'tabWidth'        => null,
                         'encoding'        => null,
                         'extensions'      => null,
                         'sniffs'          => null,
                         'exclude'         => null,
                         'ignored'         => null,
                         'reportFile'      => null,
                         'generator'       => null,
                         'filter'          => null,
                         'bootstrap'       => null,
                         'reports'         => null,
                         'basepath'        => null,
                         'reportWidth'     => null,
                         'errorSeverity'   => null,
                         'warningSeverity' => null,
                         'recordErrors'    => null,
                         'suffix'          => null,
                         'stdin'           => null,
                         'stdinContent'    => null,
                         'stdinPath'       => null,
                         'unknown'         => null,
                        );

    /**
     * Whether or not to kill the process when an unknown command line arg is found.
     *
     * If FALSE, arguments that are not command line options or file/directory paths
     * will be ignored and execution will continue. These values will be stored in
     * $this->unknown.
     *
     * @var boolean
     */
    public $dieOnUnknownArg;

    /**
     * The current command line arguments we are processing.
     *
     * @var string[]
     */
    private $cliArgs = array();

    /**
     * Command line values that the user has supplied directly.
     *
     * @var array<string, TRUE>
     */
    private $overriddenDefaults = array();

    /**
     * Config file data that has been loaded for the run.
     *
     * @var array<string, string>
     */
    private static $configData = null;

    /**
     * The full path to the config data file that has been loaded.
     *
     * @var string
     */
    private static $configDataFile = null;

    /**
     * Automatically discovered executable utility paths.
     *
     * @var array<string, string>
     */
    private static $executablePaths = array();


    /**
     * Get the value of an inaccessible property.
     *
     * @param string $name The name of the property.
     *
     * @return mixed
     * @throws RuntimeException If the setting name is invalid.
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->settings) === false) {
            throw new RuntimeException("ERROR: unable to get value of property \"$name\"");
        }

        return $this->settings[$name];

    }//end __get()


    /**
     * Set the value of an inaccessible property.
     *
     * @param string $name  The name of the property.
     * @param mixed  $value The value of the property.
     *
     * @return void
     * @throws RuntimeException If the setting name is invalid.
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->settings) === false) {
            throw new RuntimeException("Can't __set() $name; setting doesn't exist");
        }

        switch ($name) {
        case 'reportWidth' :
            // Support auto terminal width.
            if ($value === 'auto' && preg_match('|\d+ (\d+)|', shell_exec('stty size 2>&1'), $matches) === 1) {
                $value = (int) $matches[1];
            } else {
                $value = (int) $value;
            }
            break;
        case 'standards' :
            $cleaned = array();

            // Check if the standard name is valid, or if the case is invalid.
            $installedStandards = Util\Standards::getInstalledStandards();
            foreach ($value as $standard) {
                foreach ($installedStandards as $validStandard) {
                    if (strtolower($standard) === strtolower($validStandard)) {
                        $standard = $validStandard;
                        break;
                    }
                }

                $cleaned[] = $standard;
            }

            $value = $cleaned;
            break;
        default :
            // No validation required.
            break;
        }//end switch

        $this->settings[$name] = $value;

    }//end __set()


    /**
     * Check if the value of an inaccessible property is set.
     *
     * @param string $name The name of the property.
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->settings[$name]);

    }//end __isset()


    /**
     * Unset the value of an inaccessible property.
     *
     * @param string $name The name of the property.
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->settings[$name] = null;

    }//end __unset()


    /**
     * Get the array of all config settings.
     *
     * @return array<string, mixed>
     */
    public function getSettings()
    {
        return $this->settings;

    }//end getSettings()


    /**
     * Set the array of all config settings.
     *
     * @param array<string, mixed> $settings The array of config settings.
     *
     * @return void
     */
    public function setSettings($settings)
    {
        return $this->settings = $settings;

    }//end setSettings()


    /**
     * Creates a Config object and populates it with command line values.
     *
     * @param array $cliArgs         An array of values gathered from CLI args.
     * @param bool  $dieOnUnknownArg Whether or not to kill the process when an
     *                               unknown command line arg is found.
     *
     * @return void
     */
    public function __construct(array $cliArgs=array(), $dieOnUnknownArg=true)
    {
        if (defined('PHP_CODESNIFFER_IN_TESTS') === true) {
            // Let everything through during testing so that we can
            // make use of PHPUnit command line arguments as well.
            $this->dieOnUnknownArg = false;
        } else {
            $this->dieOnUnknownArg = $dieOnUnknownArg;
        }

        $checkStdin = false;
        if (empty($cliArgs) === true) {
            $cliArgs = $_SERVER['argv'];
            array_shift($cliArgs);
            $checkStdin = true;
        }

        $this->restoreDefaults();
        $this->setCommandLineValues($cliArgs);

        if (isset($this->overriddenDefaults['standards']) === false) {
            // They did not supply a standard to use.
            // Look for a default ruleset in the current directory or higher.
            $currentDir = getcwd();

            $defaultFiles = array(
                             '.phpcs.xml',
                             'phpcs.xml',
                             '.phpcs.xml.dist',
                             'phpcs.xml.dist',
                            );

            do {
                foreach ($defaultFiles as $defaultFilename) {
                    $default = $currentDir.DIRECTORY_SEPARATOR.$defaultFilename;
                    if (is_file($default) === true) {
                        $this->standards = array($default);
                        break(2);
                    }
                }

                $lastDir    = $currentDir;
                $currentDir = dirname($currentDir);
            } while ($currentDir !== '.' && $currentDir !== $lastDir);
        }//end if

        // Check for content on STDIN.
        if ($checkStdin === true) {
            $handle = fopen('php://stdin', 'r');
            if (stream_set_blocking($handle, false) === true) {
                $fileContents = '';
                while (($line = fgets($handle)) !== false) {
                    $fileContents .= $line;
                    usleep(10);
                }

                stream_set_blocking($handle, true);
                fclose($handle);
                if (trim($fileContents) !== '') {
                    $this->stdin        = true;
                    $this->stdinContent = $fileContents;
                    $this->overriddenDefaults['stdin']        = true;
                    $this->overriddenDefaults['stdinContent'] = true;
                }
            }
        }

    }//end __construct()


    /**
     * Set the command line values.
     *
     * @param array $args An array of command line arguments to set.
     *
     * @return void
     */
    public function setCommandLineValues($args)
    {
        $this->cliArgs = $args;
        $numArgs       = count($args);

        for ($i = 0; $i < $numArgs; $i++) {
            $arg = $this->cliArgs[$i];
            if ($arg === '') {
                continue;
            }

            if ($arg{0} === '-') {
                if ($arg === '-') {
                    // Asking to read from STDIN.
                    $this->stdin = true;
                    $this->overriddenDefaults['stdin'] = true;
                    continue;
                }

                if ($arg === '--') {
                    // Empty argument, ignore it.
                    continue;
                }

                if ($arg{1} === '-') {
                    $this->processLongArgument(substr($arg, 2), $i);
                } else {
                    $switches = str_split($arg);
                    foreach ($switches as $switch) {
                        if ($switch === '-') {
                            continue;
                        }

                        $this->processShortArgument($switch, $i);
                    }
                }
            } else {
                $this->processUnknownArgument($arg, $i);
            }//end if
        }//end for

    }//end setCommandLineValues()


    /**
     * Restore default values for all possible command line arguments.
     *
     * @return array
     */
    public function restoreDefaults()
    {
        $this->files           = array();
        $this->standards       = array('PEAR');
        $this->verbosity       = 0;
        $this->interactive     = false;
        $this->cache           = false;
        $this->cacheFile       = null;
        $this->colors          = false;
        $this->explain         = false;
        $this->local           = false;
        $this->showSources     = false;
        $this->showProgress    = false;
        $this->quiet           = false;
        $this->annotations     = true;
        $this->parallel        = 1;
        $this->tabWidth        = 0;
        $this->encoding        = 'utf-8';
        $this->extensions      = array(
                                  'php' => 'PHP',
                                  'inc' => 'PHP',
                                  'js'  => 'JS',
                                  'css' => 'CSS',
                                 );
        $this->sniffs          = array();
        $this->exclude         = array();
        $this->ignored         = array();
        $this->reportFile      = null;
        $this->generator       = null;
        $this->filter          = null;
        $this->bootstrap       = array();
        $this->basepath        = null;
        $this->reports         = array('full' => null);
        $this->reportWidth     = 'auto';
        $this->errorSeverity   = 5;
        $this->warningSeverity = 5;
        $this->recordErrors    = true;
        $this->suffix          = '';
        $this->stdin           = false;
        $this->stdinContent    = null;
        $this->stdinPath       = null;
        $this->unknown         = array();

        $standard = self::getConfigData('default_standard');
        if ($standard !== null) {
            $this->standards = explode(',', $standard);
        }

        $reportFormat = self::getConfigData('report_format');
        if ($reportFormat !== null) {
            $this->reports = array($reportFormat => null);
        }

        $tabWidth = self::getConfigData('tab_width');
        if ($tabWidth !== null) {
            $this->tabWidth = (int) $tabWidth;
        }

        $encoding = self::getConfigData('encoding');
        if ($encoding !== null) {
            $this->encoding = strtolower($encoding);
        }

        $severity = self::getConfigData('severity');
        if ($severity !== null) {
            $this->errorSeverity   = (int) $severity;
            $this->warningSeverity = (int) $severity;
        }

        $severity = self::getConfigData('error_severity');
        if ($severity !== null) {
            $this->errorSeverity = (int) $severity;
        }

        $severity = self::getConfigData('warning_severity');
        if ($severity !== null) {
            $this->warningSeverity = (int) $severity;
        }

        $showWarnings = self::getConfigData('show_warnings');
        if ($showWarnings !== null) {
            $showWarnings = (bool) $showWarnings;
            if ($showWarnings === false) {
                $this->warningSeverity = 0;
            }
        }

        $reportWidth = self::getConfigData('report_width');
        if ($reportWidth !== null) {
            $this->reportWidth = $reportWidth;
        }

        $showProgress = self::getConfigData('show_progress');
        if ($showProgress !== null) {
            $this->showProgress = (bool) $showProgress;
        }

        $quiet = self::getConfigData('quiet');
        if ($quiet !== null) {
            $this->quiet = (bool) $quiet;
        }

        $colors = self::getConfigData('colors');
        if ($colors !== null) {
            $this->colors = (bool) $colors;
        }

        if (defined('PHP_CODESNIFFER_IN_TESTS') === false) {
            $cache = self::getConfigData('cache');
            if ($cache !== null) {
                $this->cache = (bool) $cache;
            }

            $parallel = self::getConfigData('parallel');
            if ($parallel !== null) {
                $this->parallel = max((int) $parallel, 1);
            }
        }

    }//end restoreDefaults()


    /**
     * Processes a short (-e) command line argument.
     *
     * @param string $arg The command line argument.
     * @param int    $pos The position of the argument on the command line.
     *
     * @return void
     */
    public function processShortArgument($arg, $pos)
    {
        switch ($arg) {
        case 'h':
        case '?':
            ob_start();
            $this->printUsage();
            $output = ob_get_contents();
            ob_end_clean();
            throw new DeepExitException($output, 0);
        case 'i' :
            ob_start();
            Util\Standards::printInstalledStandards();
            $output = ob_get_contents();
            ob_end_clean();
            throw new DeepExitException($output, 0);
        case 'v' :
            if ($this->quiet === true) {
                // Ignore when quiet mode is enabled.
                break;
            }

            $this->verbosity++;
            $this->overriddenDefaults['verbosity'] = true;
            break;
        case 'l' :
            $this->local = true;
            $this->overriddenDefaults['local'] = true;
            break;
        case 's' :
            $this->showSources = true;
            $this->overriddenDefaults['showSources'] = true;
            break;
        case 'a' :
            $this->interactive = true;
            $this->overriddenDefaults['interactive'] = true;
            break;
        case 'e':
            $this->explain = true;
            $this->overriddenDefaults['explain'] = true;
            break;
        case 'p' :
            if ($this->quiet === true) {
                // Ignore when quiet mode is enabled.
                break;
            }

            $this->showProgress = true;
            $this->overriddenDefaults['showProgress'] = true;
            break;
        case 'q' :
            // Quiet mode disables a few other settings as well.
            $this->quiet        = true;
            $this->showProgress = false;
            $this->verbosity    = 0;

            $this->overriddenDefaults['quiet'] = true;
            break;
        case 'm' :
            $this->recordErrors = false;
            $this->overriddenDefaults['recordErrors'] = true;
            break;
        case 'd' :
            $ini = explode('=', $this->cliArgs[($pos + 1)]);
            $this->cliArgs[($pos + 1)] = '';
            if (isset($ini[1]) === true) {
                ini_set($ini[0], $ini[1]);
            } else {
                ini_set($ini[0], true);
            }
            break;
        case 'n' :
            if (isset($this->overriddenDefaults['warningSeverity']) === false) {
                $this->warningSeverity = 0;
                $this->overriddenDefaults['warningSeverity'] = true;
            }
            break;
        case 'w' :
            if (isset($this->overriddenDefaults['warningSeverity']) === false) {
                $this->warningSeverity = $this->errorSeverity;
                $this->overriddenDefaults['warningSeverity'] = true;
            }
            break;
        default:
            if ($this->dieOnUnknownArg === false) {
                $unknown       = $this->unknown;
                $unknown[]     = $arg;
                $this->unknown = $unknown;
            } else {
                $this->processUnknownArgument('-'.$arg, $pos);
            }
        }//end switch

    }//end processShortArgument()


    /**
     * Processes a long (--example) command line argument.
     *
     * @param string $arg The command line argument.
     * @param int    $pos The position of the argument on the command line.
     *
     * @return void
     */
    public function processLongArgument($arg, $pos)
    {
        switch ($arg) {
        case 'help':
            ob_start();
            $this->printUsage();
            $output = ob_get_contents();
            ob_end_clean();
            throw new DeepExitException($output, 0);
        case 'version':
            $output  = 'PHP_CodeSniffer version '.self::VERSION.' ('.self::STABILITY.') ';
            $output .= 'by Squiz (http://www.squiz.net)'.PHP_EOL;
            throw new DeepExitException($output, 0);
        case 'colors':
            if (isset($this->overriddenDefaults['colors']) === true) {
                break;
            }

            $this->colors = true;
            $this->overriddenDefaults['colors'] = true;
            break;
        case 'no-colors':
            if (isset($this->overriddenDefaults['colors']) === true) {
                break;
            }

            $this->colors = false;
            $this->overriddenDefaults['colors'] = true;
            break;
        case 'cache':
            if (isset($this->overriddenDefaults['cache']) === true) {
                break;
            }

            if (defined('PHP_CODESNIFFER_IN_TESTS') === false) {
                $this->cache = true;
                $this->overriddenDefaults['cache'] = true;
            }
            break;
        case 'no-cache':
            if (isset($this->overriddenDefaults['cache']) === true) {
                break;
            }

            $this->cache = false;
            $this->overriddenDefaults['cache'] = true;
            break;
        case 'ignore-annotations':
            if (isset($this->overriddenDefaults['annotations']) === true) {
                break;
            }

            $this->annotations = false;
            $this->overriddenDefaults['annotations'] = true;
            break;
        case 'config-set':
            if (isset($this->cliArgs[($pos + 1)]) === false
                || isset($this->cliArgs[($pos + 2)]) === false
            ) {
                $error  = 'ERROR: Setting a config option requires a name and value'.PHP_EOL.PHP_EOL;
                $error .= $this->printShortUsage(true);
                throw new DeepExitException($error, 3);
            }

            $key     = $this->cliArgs[($pos + 1)];
            $value   = $this->cliArgs[($pos + 2)];
            $current = self::getConfigData($key);

            try {
                $this->setConfigData($key, $value);
            } catch (\Exception $e) {
                throw new DeepExitException($e->getMessage().PHP_EOL, 3);
            }

            $output = 'Using config file: '.self::$configDataFile.PHP_EOL.PHP_EOL;

            if ($current === null) {
                $output .= "Config value \"$key\" added successfully".PHP_EOL;
            } else {
                $output .= "Config value \"$key\" updated successfully; old value was \"$current\"".PHP_EOL;
            }
            throw new DeepExitException($output, 0);
        case 'config-delete':
            if (isset($this->cliArgs[($pos + 1)]) === false) {
                $error  = 'ERROR: Deleting a config option requires the name of the option'.PHP_EOL.PHP_EOL;
                $error .= $this->printShortUsage(true);
                throw new DeepExitException($error, 3);
            }

            $output = 'Using config file: '.self::$configDataFile.PHP_EOL.PHP_EOL;

            $key     = $this->cliArgs[($pos + 1)];
            $current = self::getConfigData($key);
            if ($current === null) {
                $output .= "Config value \"$key\" has not been set".PHP_EOL;
            } else {
                try {
                    $this->setConfigData($key, null);
                } catch (\Exception $e) {
                    throw new DeepExitException($e->getMessage().PHP_EOL, 3);
                }

                $output .= "Config value \"$key\" removed successfully; old value was \"$current\"".PHP_EOL;
            }
            throw new DeepExitException($output, 0);
        case 'config-show':
            ob_start();
            $data = self::getAllConfigData();
            echo 'Using config file: '.self::$configDataFile.PHP_EOL.PHP_EOL;
            $this->printConfigData($data);
            $output = ob_get_contents();
            ob_end_clean();
            throw new DeepExitException($output, 0);
        case 'runtime-set':
            if (isset($this->cliArgs[($pos + 1)]) === false
                || isset($this->cliArgs[($pos + 2)]) === false
            ) {
                $error  = 'ERROR: Setting a runtime config option requires a name and value'.PHP_EOL.PHP_EOL;
                $error .= $this->printShortUsage(true);
                throw new DeepExitException($error, 3);
            }

            $key   = $this->cliArgs[($pos + 1)];
            $value = $this->cliArgs[($pos + 2)];
            $this->cliArgs[($pos + 1)] = '';
            $this->cliArgs[($pos + 2)] = '';
            self::setConfigData($key, $value, true);
            break;
        default:
            if (substr($arg, 0, 7) === 'sniffs=') {
                if (isset($this->overriddenDefaults['sniffs']) === true) {
                    break;
                }

                $sniffs = explode(',', substr($arg, 7));
                foreach ($sniffs as $sniff) {
                    if (substr_count($sniff, '.') !== 2) {
                        $error  = 'ERROR: The specified sniff code "'.$sniff.'" is invalid'.PHP_EOL.PHP_EOL;
                        $error .= $this->printShortUsage(true);
                        throw new DeepExitException($error, 3);
                    }
                }

                $this->sniffs = $sniffs;
                $this->overriddenDefaults['sniffs'] = true;
            } else if (substr($arg, 0, 8) === 'exclude=') {
                if (isset($this->overriddenDefaults['exclude']) === true) {
                    break;
                }

                $sniffs = explode(',', substr($arg, 8));
                foreach ($sniffs as $sniff) {
                    if (substr_count($sniff, '.') !== 2) {
                        $error  = 'ERROR: The specified sniff code "'.$sniff.'" is invalid'.PHP_EOL.PHP_EOL;
                        $error .= $this->printShortUsage(true);
                        throw new DeepExitException($error, 3);
                    }
                }

                $this->exclude = $sniffs;
                $this->overriddenDefaults['exclude'] = true;
            } else if (defined('PHP_CODESNIFFER_IN_TESTS') === false
                && substr($arg, 0, 6) === 'cache='
            ) {
                if ((isset($this->overriddenDefaults['cache']) === true
                    && $this->cache === false)
                    || isset($this->overriddenDefaults['cacheFile']) === true
                ) {
                    break;
                }

                // Turn caching on.
                $this->cache = true;
                $this->overriddenDefaults['cache'] = true;

                $this->cacheFile = Util\Common::realpath(substr($arg, 6));

                // It may not exist and return false instead.
                if ($this->cacheFile === false) {
                    $this->cacheFile = substr($arg, 6);

                    $dir = dirname($this->cacheFile);
                    if (is_dir($dir) === false) {
                        $error  = 'ERROR: The specified cache file path "'.$this->cacheFile.'" points to a non-existent directory'.PHP_EOL.PHP_EOL;
                        $error .= $this->printShortUsage(true);
                        throw new DeepExitException($error, 3);
                    }

                    if ($dir === '.') {
                        // Passed cache file is a file in the current directory.
                        $this->cacheFile = getcwd().'/'.basename($this->cacheFile);
                    } else {
                        if ($dir{0} === '/') {
                            // An absolute path.
                            $dir = Util\Common::realpath($dir);
                        } else {
                            $dir = Util\Common::realpath(getcwd().'/'.$dir);
                        }

                        if ($dir !== false) {
                            // Cache file path is relative.
                            $this->cacheFile = $dir.'/'.basename($this->cacheFile);
                        }
                    }
                }//end if

                $this->overriddenDefaults['cacheFile'] = true;

                if (is_dir($this->cacheFile) === true) {
                    $error  = 'ERROR: The specified cache file path "'.$this->cacheFile.'" is a directory'.PHP_EOL.PHP_EOL;
                    $error .= $this->printShortUsage(true);
                    throw new DeepExitException($error, 3);
                }
            } else if (substr($arg, 0, 10) === 'bootstrap=') {
                $files     = explode(',', substr($arg, 10));
                $bootstrap = array();
                foreach ($files as $file) {
                    $path = Util\Common::realpath($file);
                    if ($path === false) {
                        $error  = 'ERROR: The specified bootstrap file "'.$file.'" does not exist'.PHP_EOL.PHP_EOL;
                        $error .= $this->printShortUsage(true);
                        throw new DeepExitException($error, 3);
                    }

                    $bootstrap[] = $path;
                }

                $this->bootstrap = array_merge($this->bootstrap, $bootstrap);
                $this->overriddenDefaults['bootstrap'] = true;
            } else if (substr($arg, 0, 10) === 'file-list=') {
                $fileList = substr($arg, 10);
                $path     = Util\Common::realpath($fileList);
                if ($path === false) {
                    $error  = 'ERROR: The specified file list "'.$fileList.'" does not exist'.PHP_EOL.PHP_EOL;
                    $error .= $this->printShortUsage(true);
                    throw new DeepExitException($error, 3);
                }

                $files = file($path);
                foreach ($files as $inputFile) {
                    $inputFile = trim($inputFile);

                    // Skip empty lines.
                    if ($inputFile === '') {
                        continue;
                    }

                    $this->processFilePath($inputFile);
                }
            } else if (substr($arg, 0, 11) === 'stdin-path=') {
                if (isset($this->overriddenDefaults['stdinPath']) === true) {
                    break;
                }

                $this->stdinPath = Util\Common::realpath(substr($arg, 11));

                // It may not exist and return false instead, so use whatever they gave us.
                if ($this->stdinPath === false) {
                    $this->stdinPath = trim(substr($arg, 11));
                }

                $this->overriddenDefaults['stdinPath'] = true;
            } else if (PHP_CODESNIFFER_CBF === false && substr($arg, 0, 12) === 'report-file=') {
                if (isset($this->overriddenDefaults['reportFile']) === true) {
                    break;
                }

                $this->reportFile = Util\Common::realpath(substr($arg, 12));

                // It may not exist and return false instead.
                if ($this->reportFile === false) {
                    $this->reportFile = substr($arg, 12);

                    $dir = dirname($this->reportFile);
                    if (is_dir($dir) === false) {
                        $error  = 'ERROR: The specified report file path "'.$this->reportFile.'" points to a non-existent directory'.PHP_EOL.PHP_EOL;
                        $error .= $this->printShortUsage(true);
                        throw new DeepExitException($error, 3);
                    }

                    if ($dir === '.') {
                        // Passed report file is a file in the current directory.
                        $this->reportFile = getcwd().'/'.basename($this->reportFile);
                    } else {
                        if ($dir{0} === '/') {
                            // An absolute path.
                            $dir = Util\Common::realpath($dir);
                        } else {
                            $dir = Util\Common::realpath(getcwd().'/'.$dir);
                        }

                        if ($dir !== false) {
                            // Report file path is relative.
                            $this->reportFile = $dir.'/'.basename($this->reportFile);
                        }
                    }
                }//end if

                $this->overriddenDefaults['reportFile'] = true;

                if (is_dir($this->reportFile) === true) {
                    $error  = 'ERROR: The specified report file path "'.$this->reportFile.'" is a directory'.PHP_EOL.PHP_EOL;
                    $error .= $this->printShortUsage(true);
                    throw new DeepExitException($error, 3);
                }
            } else if (substr($arg, 0, 13) === 'report-width=') {
                if (isset($this->overriddenDefaults['reportWidth']) === true) {
                    break;
                }

                $this->reportWidth = substr($arg, 13);
                $this->overriddenDefaults['reportWidth'] = true;
            } else if (substr($arg, 0, 9) === 'basepath=') {
                if (isset($this->overriddenDefaults['basepath']) === true) {
                    break;
                }

                $this->basepath = Util\Common::realpath(substr($arg, 9));

                // It may not exist and return false instead.
                if ($this->basepath === false) {
                    $this->basepath = substr($arg, 9);
                }

                $this->overriddenDefaults['basepath'] = true;

                if (is_dir($this->basepath) === false) {
                    $error  = 'ERROR: The specified basepath "'.$this->basepath.'" points to a non-existent directory'.PHP_EOL.PHP_EOL;
                    $error .= $this->printShortUsage(true);
                    throw new DeepExitException($error, 3);
                }
            } else if ((substr($arg, 0, 7) === 'report=' || substr($arg, 0, 7) === 'report-')) {
                $reports = array();

                if ($arg[6] === '-') {
                    // This is a report with file output.
                    $split = strpos($arg, '=');
                    if ($split === false) {
                        $report = substr($arg, 7);
                        $output = null;
                    } else {
                        $report = substr($arg, 7, ($split - 7));
                        $output = substr($arg, ($split + 1));
                        if ($output === false) {
                            $output = null;
                        } else {
                            $dir = dirname($output);
                            if ($dir === '.') {
                                // Passed report file is a filename in the current directory.
                                $output = getcwd().'/'.basename($output);
                            } else {
                                if ($dir{0} === '/') {
                                    // An absolute path.
                                    $dir = Util\Common::realpath($dir);
                                } else {
                                    $dir = Util\Common::realpath(getcwd().'/'.$dir);
                                }

                                if ($dir !== false) {
                                    // Report file path is relative.
                                    $output = $dir.'/'.basename($output);
                                }
                            }
                        }//end if
                    }//end if

                    $reports[$report] = $output;
                } else {
                    // This is a single report.
                    if (isset($this->overriddenDefaults['reports']) === true) {
                        break;
                    }

                    $reportNames = explode(',', substr($arg, 7));
                    foreach ($reportNames as $report) {
                        $reports[$report] = null;
                    }
                }//end if

                // Remove the default value so the CLI value overrides it.
                if (isset($this->overriddenDefaults['reports']) === false) {
                    $this->reports = $reports;
                } else {
                    $this->reports = array_merge($this->reports, $reports);
                }

                $this->overriddenDefaults['reports'] = true;
            } else if (substr($arg, 0, 7) === 'filter=') {
                if (isset($this->overriddenDefaults['filter']) === true) {
                    break;
                }

                $this->filter = substr($arg, 7);
                $this->overriddenDefaults['filter'] = true;
            } else if (substr($arg, 0, 9) === 'standard=') {
                $standards = trim(substr($arg, 9));
                if ($standards !== '') {
                    $this->standards = explode(',', $standards);
                }

                $this->overriddenDefaults['standards'] = true;
            } else if (substr($arg, 0, 11) === 'extensions=') {
                if (isset($this->overriddenDefaults['extensions']) === true) {
                    break;
                }

                $extensions    = explode(',', substr($arg, 11));
                $newExtensions = array();
                foreach ($extensions as $ext) {
                    $slash = strpos($ext, '/');
                    if ($slash !== false) {
                        // They specified the tokenizer too.
                        list($ext, $tokenizer) = explode('/', $ext);
                        $newExtensions[$ext]   = strtoupper($tokenizer);
                        continue;
                    }

                    if (isset($this->extensions[$ext]) === true) {
                        $newExtensions[$ext] = $this->extensions[$ext];
                    } else {
                        $newExtensions[$ext] = 'PHP';
                    }
                }

                $this->extensions = $newExtensions;
                $this->overriddenDefaults['extensions'] = true;
            } else if (substr($arg, 0, 7) === 'suffix=') {
                if (isset($this->overriddenDefaults['suffix']) === true) {
                    break;
                }

                $this->suffix = substr($arg, 7);
                $this->overriddenDefaults['suffix'] = true;
            } else if (substr($arg, 0, 9) === 'parallel=') {
                if (isset($this->overriddenDefaults['parallel']) === true) {
                    break;
                }

                $this->parallel = max((int) substr($arg, 9), 1);
                $this->overriddenDefaults['parallel'] = true;
            } else if (substr($arg, 0, 9) === 'severity=') {
                $this->errorSeverity   = (int) substr($arg, 9);
                $this->warningSeverity = $this->errorSeverity;
                if (isset($this->overriddenDefaults['errorSeverity']) === false) {
                    $this->overriddenDefaults['errorSeverity'] = true;
                }

                if (isset($this->overriddenDefaults['warningSeverity']) === false) {
                    $this->overriddenDefaults['warningSeverity'] = true;
                }
            } else if (substr($arg, 0, 15) === 'error-severity=') {
                if (isset($this->overriddenDefaults['errorSeverity']) === true) {
                    break;
                }

                $this->errorSeverity = (int) substr($arg, 15);
                $this->overriddenDefaults['errorSeverity'] = true;
            } else if (substr($arg, 0, 17) === 'warning-severity=') {
                if (isset($this->overriddenDefaults['warningSeverity']) === true) {
                    break;
                }

                $this->warningSeverity = (int) substr($arg, 17);
                $this->overriddenDefaults['warningSeverity'] = true;
            } else if (substr($arg, 0, 7) === 'ignore=') {
                if (isset($this->overriddenDefaults['ignored']) === true) {
                    break;
                }

                // Split the ignore string on commas, unless the comma is escaped
                // using 1 or 3 slashes (\, or \\\,).
                $patterns = preg_split(
                    '/(?<=(?<!\\\\)\\\\\\\\),|(?<!\\\\),/',
                    substr($arg, 7)
                );

                $ignored = array();
                foreach ($patterns as $pattern) {
                    $pattern = trim($pattern);
                    if ($pattern === '') {
                        continue;
                    }

                    $ignored[$pattern] = 'absolute';
                }

                $this->ignored = $ignored;
                $this->overriddenDefaults['ignored'] = true;
            } else if (substr($arg, 0, 10) === 'generator='
                && PHP_CODESNIFFER_CBF === false
            ) {
                if (isset($this->overriddenDefaults['generator']) === true) {
                    break;
                }

                $this->generator = substr($arg, 10);
                $this->overriddenDefaults['generator'] = true;
            } else if (substr($arg, 0, 9) === 'encoding=') {
                if (isset($this->overriddenDefaults['encoding']) === true) {
                    break;
                }

                $this->encoding = strtolower(substr($arg, 9));
                $this->overriddenDefaults['encoding'] = true;
            } else if (substr($arg, 0, 10) === 'tab-width=') {
                if (isset($this->overriddenDefaults['tabWidth']) === true) {
                    break;
                }

                $this->tabWidth = (int) substr($arg, 10);
                $this->overriddenDefaults['tabWidth'] = true;
            } else {
                if ($this->dieOnUnknownArg === false) {
                    $eqPos = strpos($arg, '=');
                    try {
                        if ($eqPos === false) {
                            $this->values[$arg] = $arg;
                        } else {
                            $value = substr($arg, ($eqPos + 1));
                            $arg   = substr($arg, 0, $eqPos);
                            $this->values[$arg] = $value;
                        }
                    } catch (RuntimeException $e) {
                        // Value is not valid, so just ignore it.
                    }
                } else {
                    $this->processUnknownArgument('--'.$arg, $pos);
                }
            }//end if

            break;
        }//end switch

    }//end processLongArgument()


    /**
     * Processes an unknown command line argument.
     *
     * Assumes all unknown arguments are files and folders to check.
     *
     * @param string $arg The command line argument.
     * @param int    $pos The position of the argument on the command line.
     *
     * @return void
     */
    public function processUnknownArgument($arg, $pos)
    {
        // We don't know about any additional switches; just files.
        if ($arg{0} === '-') {
            if ($this->dieOnUnknownArg === false) {
                return;
            }

            $error  = "ERROR: option \"$arg\" not known".PHP_EOL.PHP_EOL;
            $error .= $this->printShortUsage(true);
            throw new DeepExitException($error, 3);
        }

        $this->processFilePath($arg);

    }//end processUnknownArgument()


    /**
     * Processes a file path and add it to the file list.
     *
     * @param string $path The path to the file to add.
     *
     * @return void
     */
    public function processFilePath($path)
    {
        // If we are processing STDIN, don't record any files to check.
        if ($this->stdin === true) {
            return;
        }

        $file = Util\Common::realpath($path);
        if (file_exists($file) === false) {
            if ($this->dieOnUnknownArg === false) {
                return;
            }

            $error  = 'ERROR: The file "'.$path.'" does not exist.'.PHP_EOL.PHP_EOL;
            $error .= $this->printShortUsage(true);
            throw new DeepExitException($error, 3);
        } else {
            $files       = $this->files;
            $files[]     = $file;
            $this->files = $files;
            $this->overriddenDefaults['files'] = true;
        }

    }//end processFilePath()


    /**
     * Prints out the usage information for this script.
     *
     * @return void
     */
    public function printUsage()
    {
        echo PHP_EOL;

        if (PHP_CODESNIFFER_CBF === true) {
            $this->printPHPCBFUsage();
        } else {
            $this->printPHPCSUsage();
        }

        echo PHP_EOL;

    }//end printUsage()


    /**
     * Prints out the short usage information for this script.
     *
     * @param bool $return If TRUE, the usage string is returned
     *                     instead of output to screen.
     *
     * @return string|void
     */
    public function printShortUsage($return=false)
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $usage = 'Run "phpcbf --help" for usage information';
        } else {
            $usage = 'Run "phpcs --help" for usage information';
        }

        $usage .= PHP_EOL.PHP_EOL;

        if ($return === true) {
            return $usage;
        }

        echo $usage;

    }//end printShortUsage()


    /**
     * Prints out the usage information for PHPCS.
     *
     * @return void
     */
    public function printPHPCSUsage()
    {
        echo 'Usage: phpcs [-nwlsaepqvi] [-d key[=value]] [--colors] [--no-colors]'.PHP_EOL;
        echo '  [--cache[=<cacheFile>]] [--no-cache] [--tab-width=<tabWidth>]'.PHP_EOL;
        echo '  [--report=<report>] [--report-file=<reportFile>] [--report-<report>=<reportFile>]'.PHP_EOL;
        echo '  [--report-width=<reportWidth>] [--basepath=<basepath>] [--bootstrap=<bootstrap>]'.PHP_EOL;
        echo '  [--severity=<severity>] [--error-severity=<severity>] [--warning-severity=<severity>]'.PHP_EOL;
        echo '  [--runtime-set key value] [--config-set key value] [--config-delete key] [--config-show]'.PHP_EOL;
        echo '  [--standard=<standard>] [--sniffs=<sniffs>] [--exclude=<sniffs>]'.PHP_EOL;
        echo '  [--encoding=<encoding>] [--parallel=<processes>] [--generator=<generator>]'.PHP_EOL;
        echo '  [--extensions=<extensions>] [--ignore=<patterns>] [--ignore-annotations]'.PHP_EOL;
        echo '  [--stdin-path=<stdinPath>] [--file-list=<fileList>] <file> - ...'.PHP_EOL;
        echo PHP_EOL;
        echo ' -     Check STDIN instead of local files and directories'.PHP_EOL;
        echo ' -n    Do not print warnings (shortcut for --warning-severity=0)'.PHP_EOL;
        echo ' -w    Print both warnings and errors (this is the default)'.PHP_EOL;
        echo ' -l    Local directory only, no recursion'.PHP_EOL;
        echo ' -s    Show sniff codes in all reports'.PHP_EOL;
        echo ' -a    Run interactively'.PHP_EOL;
        echo ' -e    Explain a standard by showing the sniffs it includes'.PHP_EOL;
        echo ' -p    Show progress of the run'.PHP_EOL;
        echo ' -q    Quiet mode; disables progress and verbose output'.PHP_EOL;
        echo ' -m    Stop error messages from being recorded'.PHP_EOL;
        echo '       (saves a lot of memory, but stops many reports from being used)'.PHP_EOL;
        echo ' -v    Print processed files'.PHP_EOL;
        echo ' -vv   Print ruleset and token output'.PHP_EOL;
        echo ' -vvv  Print sniff processing information'.PHP_EOL;
        echo ' -i    Show a list of installed coding standards'.PHP_EOL;
        echo ' -d    Set the [key] php.ini value to [value] or [true] if value is omitted'.PHP_EOL;
        echo PHP_EOL;
        echo ' --help                Print this help message'.PHP_EOL;
        echo ' --version             Print version information'.PHP_EOL;
        echo ' --colors              Use colors in output'.PHP_EOL;
        echo ' --no-colors           Do not use colors in output (this is the default)'.PHP_EOL;
        echo ' --cache               Cache results between runs'.PHP_EOL;
        echo ' --no-cache            Do not cache results between runs (this is the default)'.PHP_EOL;
        echo ' --ignore-annotations  Ignore all @codingStandard annotations in code comments'.PHP_EOL;
        echo PHP_EOL;
        echo ' <cacheFile>    Use a specific file for caching (uses a temporary file by default)'.PHP_EOL;
        echo ' <basepath>     A path to strip from the front of file paths inside reports'.PHP_EOL;
        echo ' <bootstrap>    A comma separated list of files to run before processing begins'.PHP_EOL;
        echo ' <file>         One or more files and/or directories to check'.PHP_EOL;
        echo ' <fileList>     A file containing a list of files and/or directories to check (one per line)'.PHP_EOL;
        echo ' <encoding>     The encoding of the files being checked (default is utf-8)'.PHP_EOL;
        echo ' <extensions>   A comma separated list of file extensions to check'.PHP_EOL;
        echo '                The type of the file can be specified using: ext/type'.PHP_EOL;
        echo '                e.g., module/php,es/js'.PHP_EOL;
        echo ' <generator>    Uses either the "HTML", "Markdown" or "Text" generator'.PHP_EOL;
        echo '                (forces documentation generation instead of checking)'.PHP_EOL;
        echo ' <patterns>     A comma separated list of patterns to ignore files and directories'.PHP_EOL;
        echo ' <processes>    How many files should be checked simultaneously (default is 1)'.PHP_EOL;
        echo ' <report>       Print either the "full", "xml", "checkstyle", "csv"'.PHP_EOL;
        echo '                "json", "junit", "emacs", "source", "summary", "diff"'.PHP_EOL;
        echo '                "svnblame", "gitblame", "hgblame" or "notifysend" report'.PHP_EOL;
        echo '                (the "full" report is printed by default)'.PHP_EOL;
        echo ' <reportFile>   Write the report to the specified file path'.PHP_EOL;
        echo ' <reportWidth>  How many columns wide screen reports should be printed'.PHP_EOL;
        echo '                or set to "auto" to use current screen width, where supported'.PHP_EOL;
        echo ' <severity>     The minimum severity required to display an error or warning'.PHP_EOL;
        echo ' <sniffs>       A comma separated list of sniff codes to include or exclude from checking'.PHP_EOL;
        echo '                (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo ' <standard>     The name or path of the coding standard to use'.PHP_EOL;
        echo ' <stdinPath>    If processing STDIN, the file path that STDIN will be processed as'.PHP_EOL;
        echo ' <tabWidth>     The number of spaces each tab represents'.PHP_EOL;

    }//end printPHPCSUsage()


    /**
     * Prints out the usage information for PHPCBF.
     *
     * @return void
     */
    public function printPHPCBFUsage()
    {
        echo 'Usage: phpcbf [-nwli] [-d key[=value]] [--ignore-annotations] [--bootstrap=<bootstrap>]'.PHP_EOL;
        echo '  [--standard=<standard>] [--sniffs=<sniffs>] [--exclude=<sniffs>] [--suffix=<suffix>]'.PHP_EOL;
        echo '  [--severity=<severity>] [--error-severity=<severity>] [--warning-severity=<severity>]'.PHP_EOL;
        echo '  [--tab-width=<tabWidth>] [--encoding=<encoding>] [--parallel=<processes>]'.PHP_EOL;
        echo '  [--basepath=<basepath>] [--extensions=<extensions>] [--ignore=<patterns>]'.PHP_EOL;
        echo '  [--stdin-path=<stdinPath>] [--file-list=<fileList>] <file> - ...'.PHP_EOL;
        echo PHP_EOL;
        echo ' -     Fix STDIN instead of local files and directories'.PHP_EOL;
        echo ' -n    Do not fix warnings (shortcut for --warning-severity=0)'.PHP_EOL;
        echo ' -w    Fix both warnings and errors (on by default)'.PHP_EOL;
        echo ' -l    Local directory only, no recursion'.PHP_EOL;
        echo ' -p    Show progress of the run'.PHP_EOL;
        echo ' -q    Quiet mode; disables progress and verbose output'.PHP_EOL;
        echo ' -v    Print processed files'.PHP_EOL;
        echo ' -vv   Print ruleset and token output'.PHP_EOL;
        echo ' -vvv  Print sniff processing information'.PHP_EOL;
        echo ' -i    Show a list of installed coding standards'.PHP_EOL;
        echo ' -d    Set the [key] php.ini value to [value] or [true] if value is omitted'.PHP_EOL;
        echo PHP_EOL;
        echo ' --help                Print this help message'.PHP_EOL;
        echo ' --version             Print version information'.PHP_EOL;
        echo ' --ignore-annotations  Ignore all @codingStandard annotations in code comments'.PHP_EOL;
        echo PHP_EOL;
        echo ' <basepath>    A path to strip from the front of file paths inside reports'.PHP_EOL;
        echo ' <bootstrap>   A comma separated list of files to run before processing begins'.PHP_EOL;
        echo ' <file>        One or more files and/or directories to fix'.PHP_EOL;
        echo ' <fileList>    A file containing a list of files and/or directories to fix (one per line)'.PHP_EOL;
        echo ' <encoding>    The encoding of the files being fixed (default is utf-8)'.PHP_EOL;
        echo ' <extensions>  A comma separated list of file extensions to fix'.PHP_EOL;
        echo '               The type of the file can be specified using: ext/type'.PHP_EOL;
        echo '               e.g., module/php,es/js'.PHP_EOL;
        echo ' <patterns>    A comma separated list of patterns to ignore files and directories'.PHP_EOL;
        echo ' <processes>   How many files should be fixed simultaneously (default is 1)'.PHP_EOL;
        echo ' <severity>    The minimum severity required to fix an error or warning'.PHP_EOL;
        echo ' <sniffs>      A comma separated list of sniff codes to include or exclude from fixing'.PHP_EOL;
        echo '               (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo ' <standard>    The name or path of the coding standard to use'.PHP_EOL;
        echo ' <stdinPath>   If processing STDIN, the file path that STDIN will be processed as'.PHP_EOL;
        echo ' <suffix>      Write modified files to a filename using this suffix'.PHP_EOL;
        echo '               ("diff" and "patch" are not used in this mode)'.PHP_EOL;
        echo ' <tabWidth>    The number of spaces each tab represents'.PHP_EOL;

    }//end printPHPCBFUsage()


    /**
     * Get a single config value.
     *
     * @param string $key The name of the config value.
     *
     * @return string|null
     * @see    setConfigData()
     * @see    getAllConfigData()
     */
    public static function getConfigData($key)
    {
        $phpCodeSnifferConfig = self::getAllConfigData();

        if ($phpCodeSnifferConfig === null) {
            return null;
        }

        if (isset($phpCodeSnifferConfig[$key]) === false) {
            return null;
        }

        return $phpCodeSnifferConfig[$key];

    }//end getConfigData()


    /**
     * Get the path to an executable utility.
     *
     * @param string $name The name of the executable utility.
     *
     * @return string|null
     * @see    getConfigData()
     */
    public static function getExecutablePath($name)
    {
        $data = self::getConfigData($name.'_path');
        if ($data !== null) {
            return $data;
        }

        if (array_key_exists($name, self::$executablePaths) === true) {
            return self::$executablePaths[$name];
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = 'where '.escapeshellarg($name).' 2> nul';
        } else {
            $cmd = 'which '.escapeshellarg($name).' 2> /dev/null';
        }

        $result = exec($cmd, $output, $retVal);
        if ($retVal !== 0) {
            $result = null;
        }

        self::$executablePaths[$name] = $result;
        return $result;

    }//end getExecutablePath()


    /**
     * Set a single config value.
     *
     * @param string      $key   The name of the config value.
     * @param string|null $value The value to set. If null, the config
     *                           entry is deleted, reverting it to the
     *                           default value.
     * @param boolean     $temp  Set this config data temporarily for this
     *                           script run. This will not write the config
     *                           data to the config file.
     *
     * @return bool
     * @see    getConfigData()
     * @throws RuntimeException If the config file can not be written.
     */
    public static function setConfigData($key, $value, $temp=false)
    {
        if ($temp === false) {
            $path = '';
            if (is_callable('\Phar::running') === true) {
                $path = \Phar::running(false);
            }

            if ($path !== '') {
                $configFile = dirname($path).'/CodeSniffer.conf';
            } else {
                $configFile = dirname(__DIR__).'/CodeSniffer.conf';
                if (is_file($configFile) === false
                    && strpos('@data_dir@', '@data_dir') === false
                ) {
                    // If data_dir was replaced, this is a PEAR install and we can
                    // use the PEAR data dir to store the conf file.
                    $configFile = '@data_dir@/PHP_CodeSniffer/CodeSniffer.conf';
                }
            }

            if (is_file($configFile) === true
                && is_writable($configFile) === false
            ) {
                $error = 'ERROR: Config file '.$configFile.' is not writable'.PHP_EOL.PHP_EOL;
                throw new DeepExitException($error, 3);
            }
        }//end if

        $phpCodeSnifferConfig = self::getAllConfigData();

        if ($value === null) {
            if (isset($phpCodeSnifferConfig[$key]) === true) {
                unset($phpCodeSnifferConfig[$key]);
            }
        } else {
            $phpCodeSnifferConfig[$key] = $value;
        }

        if ($temp === false) {
            $output  = '<'.'?php'."\n".' $phpCodeSnifferConfig = ';
            $output .= var_export($phpCodeSnifferConfig, true);
            $output .= "\n?".'>';

            if (file_put_contents($configFile, $output) === false) {
                $error = 'ERROR: Config file '.$configFile.' could not be written'.PHP_EOL.PHP_EOL;
                throw new DeepExitException($error, 3);
            }

            self::$configDataFile = $configFile;
        }

        self::$configData = $phpCodeSnifferConfig;

        // If the installed paths are being set, make sure all known
        // standards paths are added to the autoloader.
        if ($key === 'installed_paths') {
            $installedStandards = Util\Standards::getInstalledStandardDetails();
            foreach ($installedStandards as $name => $details) {
                Autoload::addSearchPath($details['path'], $details['namespace']);
            }
        }

        return true;

    }//end setConfigData()


    /**
     * Get all config data.
     *
     * @return array<string, string>
     * @see    getConfigData()
     */
    public static function getAllConfigData()
    {
        if (self::$configData !== null) {
            return self::$configData;
        }

        $path = '';
        if (is_callable('\Phar::running') === true) {
            $path = \Phar::running(false);
        }

        if ($path !== '') {
            $configFile = dirname($path).'/CodeSniffer.conf';
        } else {
            $configFile = dirname(__DIR__).'/CodeSniffer.conf';
            if (is_file($configFile) === false
                && strpos('@data_dir@', '@data_dir') === false
            ) {
                $configFile = '@data_dir@/PHP_CodeSniffer/CodeSniffer.conf';
            }
        }

        if (is_file($configFile) === false) {
            self::$configData = array();
            return array();
        }

        if (is_readable($configFile) === false) {
            $error = 'ERROR: Config file '.$configFile.' is not readable'.PHP_EOL.PHP_EOL;
            throw new DeepExitException($error, 3);
        }

        include $configFile;
        self::$configDataFile = $configFile;
        self::$configData     = $phpCodeSnifferConfig;
        return self::$configData;

    }//end getAllConfigData()


    /**
     * Prints out the gathered config data.
     *
     * @param array $data The config data to print.
     *
     * @return void
     */
    public function printConfigData($data)
    {
        $max  = 0;
        $keys = array_keys($data);
        foreach ($keys as $key) {
            $len = strlen($key);
            if (strlen($key) > $max) {
                $max = $len;
            }
        }

        if ($max === 0) {
            return;
        }

        $max += 2;
        ksort($data);
        foreach ($data as $name => $value) {
            echo str_pad($name.': ', $max).$value.PHP_EOL;
        }

    }//end printConfigData()


}//end class
