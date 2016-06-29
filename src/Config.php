<?php
/**
 * Stores the configuration used to run PHPCS and PHPCBF.
 *
 * Parses the command line to determine user supplied values
 * and provides functions to access data stored in config files.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\Exceptions\RuntimeException;

final class Config
{

    /**
     * @var string
     */
    const VERSION = '3.0.0';

    /**
     * An array of settings that PHPCS and PHPCBF accept.
     *
     * This array is not meant to be accessed directly. Instead, use the settings
     * as if they are class member vars so the __get() and __set() magic methods
     * can be used to validate the values. For example, to set the verbosity level to
     * level 2, use $this->verbosity = 2; insteas of accessing this property directly.
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
     * bool     explain         Explain the coding standards.
     * bool     local           Process local files in directories only (no recursion).
     * bool     showSources     Show sniff source codes in report output.
     * bool     showProgress    Show basic progress information while running.
     * string[] sniffs          The sniffs that should be used for checking.
     *                          If empty, all sniffs in the supplied standards will be used.
     * string[] ignored         Regular expressions used to ignore files and folders during checking.
     * string   reportFile      A file where the report output should be written.
     * string   filter          The filter to use for the run.
     * string[] bootstrap       One of more files to include before the run begins.
     *                          Set to "auto" for have this value changed to the width of the terminal.
     * int      errorSeverity   The minimum severity an error must have to be displayed.
     * int      warningSeverity The minimum severity a warning must have to be displayed.
     * bool     recordErrors    Record the content of error messages as well as error counts.
     * string   basepath        A file system location to strip from the paths of files shown in reports.
     * bool     stdin           Read content from STDIN instead of supplied files.
     * string   stdinContent    Content passed directly to PHPCS on STDIN.
     * string   stdinPath       The path to use for content passed on STDIN.
     *
     * @var array<string, mixed>
     */
    private $settings = array(
                         'files'           => null,
                         'standards'       => null,
                         'verbosity'       => null,
                         'interactive'     => null,
                         'explain'         => null,
                         'local'           => null,
                         'showSources'     => null,
                         'showProgress'    => null,
                         'extensions'      => ['php' => 'PHP'],
                         'sniffs'          => null,
                         'ignored'         => null,
                         'reportFile'      => null,
                         'filter'          => null,
                         'bootstrap'       => null,
                         'reports'         => null,
                         'basepath'        => null,
                         'reportWidth'     => null,
                         'errorSeverity'   => null,
                         'warningSeverity' => null,
                         'recordErrors'    => null,
                         'stdin'           => null,
                         'stdinContent'    => null,
                         'stdinPath'       => null,
                        );

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
     * Unknown arguments
     *
     * @var array<mixed>
     */
    private $values = array();

    /**
     * Config file data that has been loaded for the run.
     *
     * @var array<string, string>
     */
    private static $configData = null;

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
     * Creates a Config object and populates it with command line values.
     *
     * @param array $cliArgs         An array of values gathered from CLI args.
     *
     * @return void
     */
    public function __construct(array $cliArgs=array())
    {
        $checkStdin = false;
        if (empty($cliArgs) === true) {
            $cliArgs = $_SERVER['argv'];
            array_shift($cliArgs);
            $checkStdin = true;
        }

        // set default report width
        if (preg_match('|\d+ (\d+)|', shell_exec('stty size 2>&1'), $matches) === 1) {
            $this->reportWidth = (int) $matches[1];
        }

        $this->restoreDefaults();
        $this->setCommandLineValues($cliArgs);

        // Check for content on STDIN.
        if ($checkStdin === true) {
            $handle = fopen('php://stdin', 'r');
            if (stream_set_blocking($handle, false) === true) {
                $fileContents = '';
                while (($line = fgets(STDIN)) !== false) {
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
        $this->explain         = false;
        $this->local           = false;
        $this->showSources     = false;
        $this->showProgress    = false;
        $this->extensions      = array('php' => 'PHP');
        $this->sniffs          = array();
        $this->ignored         = array();
        $this->filter          = null;
        $this->reports         = array('full' => null);
        $this->errorSeverity   = 5;
        $this->warningSeverity = 5;
        $this->recordErrors    = true;
        $this->stdin           = false;
        $this->stdinContent    = null;
        $this->stdinPath       = null;

        $this->standards = array('PSR2');
    }


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
            $this->printUsage();
            exit(0);
        case 'i' :
            Util\Standards::printInstalledStandards();
            exit(0);
        case 'v' :
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
        case 'e':
            $this->explain = true;
            $this->overriddenDefaults['explain'] = true;
            break;
        case 'p' :
            $this->showProgress = true;
            $this->overriddenDefaults['showProgress'] = true;
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
            $this->processUnknownArgument('-'.$arg, $pos);
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
            $this->printUsage();
            exit(0);
        case 'version':
            echo 'Symplify\PHP7_CodeSniffer version '.self::VERSION;
            exit(0);
        default:
            if (substr($arg, 0, 7) === 'sniffs=') {
                $sniffs = explode(',', substr($arg, 7));
                foreach ($sniffs as $sniff) {
                    if (substr_count($sniff, '.') !== 2) {
                        echo 'ERROR: The specified sniff code "'.$sniff.'" is invalid'.PHP_EOL.PHP_EOL;
                        $this->printUsage();
                        exit(2);
                    }
                }

                $this->sniffs = $sniffs;
                $this->overriddenDefaults['sniffs'] = true;
            } else if (substr($arg, 0, 11) === 'stdin-path=') {
                $this->stdinPath = Util\Common::realpath(substr($arg, 11));

                // It may not exist and return false instead, so use whatever they gave us.
                if ($this->stdinPath === false) {
                    $this->stdinPath = trim(substr($arg, 11));
                }

                $this->overriddenDefaults['stdinPath'] = true;
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
            } else if (substr($arg, 0, 9) === 'severity=') {
                $this->errorSeverity   = (int) substr($arg, 9);
                $this->warningSeverity = $this->errorSeverity;
                $this->overriddenDefaults['errorSeverity']   = true;
                $this->overriddenDefaults['warningSeverity'] = true;
            } else if (substr($arg, 0, 15) === 'error-severity=') {
                $this->errorSeverity = (int) substr($arg, 15);
                $this->overriddenDefaults['errorSeverity'] = true;
            } else if (substr($arg, 0, 17) === 'warning-severity=') {
                $this->warningSeverity = (int) substr($arg, 17);
                $this->overriddenDefaults['warningSeverity'] = true;
            } else if (substr($arg, 0, 7) === 'ignore=') {
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
            } else {
                $this->processUnknownArgument('--'.$arg, $pos);
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
        // If we are processing STDIN, don't record any files to check.
        if ($this->stdin === true) {
            return;
        }

        // We don't know about any additional switches; just files.
        if ($arg{0} === '-') {
            echo "ERROR: option \"$arg\" not known".PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        }

        $file = Util\Common::realpath($arg);
        if (file_exists($file) === false) {
            echo 'ERROR: The file "'.$arg.'" does not exist.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        } else {
            $files       = $this->files;
            $files[]     = $file;
            $this->files = $files;
            $this->overriddenDefaults['files'] = true;
        }

    }//end processUnknownArgument()


    /**
     * Prints out the usage information for this script.
     *
     * @return void
     */
    public function printUsage()
    {
        if (PHP_CodeSniffer_CBF === true) {
            $this->printPHPCBFUsage();
        } else {
            $this->printPHPCSUsage();
        }

    }//end printUsage()


    /**
     * Prints out the usage information for PHPCS.
     *
     * @return void
     */
    public function printPHPCSUsage()
    {
        echo 'Usage: phpcs [-nwlsaepvi] [-d key[=value]]'.PHP_EOL;
        echo '    [--severity=<severity>] [--error-severity=<severity>] [--warning-severity=<severity>]'.PHP_EOL;
        echo '    [--standard=<standard>] [--sniffs=<sniffs>]'.PHP_EOL;
        echo '    [--ignore=<patterns>] <file> - ...'.PHP_EOL;
        echo '        -             Check STDIN instead of local files and directories'.PHP_EOL;
        echo '        -n            Do not print warnings (shortcut for --warning-severity=0)'.PHP_EOL;
        echo '        -s            Show sniff codes in all reports'.PHP_EOL;
        echo '        -e            Explain a standard by showing the sniffs it includes'.PHP_EOL;
        echo '        -p            Show progress of the run'.PHP_EOL;
        echo '        -m            Stop error messages from being recorded'.PHP_EOL;
        echo '                      (saves a lot of memory, but stops many reports from being used)'.PHP_EOL;
        echo '        -v[v]         Print verbose output'.PHP_EOL;
        echo '        -i            Show a list of installed coding standards'.PHP_EOL;
        echo '        -d            Set the [key] php.ini value to [value] or [true] if value is omitted'.PHP_EOL;
        echo '        --help        Print this help message'.PHP_EOL;
        echo '        --version     Print version information'.PHP_EOL;
        echo '        <file>        One or more files and/or directories to check'.PHP_EOL;
        echo '        <patterns>    A comma separated list of patterns to ignore files and directories'.PHP_EOL;
        echo '        <sniffs>      A comma separated list of sniff codes to limit the check to'.PHP_EOL;
        echo '                      (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo '        <severity>    The minimum severity required to display an error or warning'.PHP_EOL;
        echo '        <standard>    The name or path of the coding standard to use'.PHP_EOL;

    }//end printPHPCSUsage()


    /**
     * Prints out the usage information for PHPCBF.
     *
     * @return void
     */
    public function printPHPCBFUsage()
    {
        echo 'Usage: phpcbf [-nwli] [-d key[=value]]'.PHP_EOL;
        echo '    [--standard=<standard>] [--sniffs=<sniffs>]'.PHP_EOL;
        echo '    [--severity=<severity>] [--error-severity=<severity>] [--warning-severity=<severity>]'.PHP_EOL;
        echo '    [--ignore=<patterns>] <file> - ...'.PHP_EOL;
        echo '        -             Fix STDIN instead of local files and directories'.PHP_EOL;
        echo '        -n            Do not fix warnings (shortcut for --warning-severity=0)'.PHP_EOL;
        echo '        -i            Show a list of installed coding standards'.PHP_EOL;
        echo '        -d            Set the [key] php.ini value to [value] or [true] if value is omitted'.PHP_EOL;
        echo '        --help        Print this help message'.PHP_EOL;
        echo '        --version     Print version information'.PHP_EOL;
        echo '        <file>        One or more files and/or directories to fix'.PHP_EOL;
        echo '        <patterns>    A comma separated list of patterns to ignore files and directories'.PHP_EOL;
        echo '        <sniffs>      A comma separated list of sniff codes to limit the fixes to'.PHP_EOL;
        echo '                      (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo '        <severity>    The minimum severity required to fix an error or warning'.PHP_EOL;
        echo '        <standard>    The name or path of the coding standard to use'.PHP_EOL;

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

}//end class
