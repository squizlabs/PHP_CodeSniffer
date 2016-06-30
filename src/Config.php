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
     * bool     showSources     Show sniff source codes in report output.
     * bool     showProgress    Show basic progress information while running.
     * string[] sniffs          The sniffs that should be used for checking.
     *                          If empty, all sniffs in the supplied standards will be used.
     * bool     recordErrors    Record the content of error messages as well as error counts.
     *
     * @var array<string, mixed>
     */
    private $settings = array(
                         'files'           => null,
                         'standards'       => null,
                         'verbosity'       => null,
                         'showSources'     => null,
                         'showProgress'    => null,
                         'extensions'      => ['php' => 'PHP'],
                         'sniffs'          => null,
                         'reportFile'      => null,
                         'bootstrap'       => null,
                         'reports'         => null,
                         'basepath'        => null,
                         'reportWidth'     => null,
                         'recordErrors'    => null,
                         'cache'       => true,
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
        // set default report width
        if (preg_match('|\d+ (\d+)|', shell_exec('stty size 2>&1'), $matches) === 1) {
            $this->reportWidth = (int) $matches[1];
        }

        $this->restoreDefaults();
        $this->setCommandLineValues($cliArgs);
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
        $this->showSources     = false;
        $this->showProgress    = false;
        $this->extensions      = array('php' => 'PHP');
        $this->sniffs          = array();
        $this->reports         = array('full' => null);
        $this->recordErrors    = true;

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
            $this->printUsage();
            exit(0);
        case 'i' :
            Util\Standards::printInstalledStandards();
            exit(0);
        case 's' :
            $this->showSources = true;
            $this->overriddenDefaults['showSources'] = true;
            break;
        case 'p' :
            $this->showProgress = true;
            $this->overriddenDefaults['showProgress'] = true;
            break;
        case 'm' :
            $this->recordErrors = false;
            $this->overriddenDefaults['recordErrors'] = true;
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
            } else if (substr($arg, 0, 9) === 'standard=') {
                $standards = trim(substr($arg, 9));
                if ($standards !== '') {
                    $this->standards = explode(',', $standards);
                }

                $this->overriddenDefaults['standards'] = true;
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
        echo 'Usage: phpcs '.PHP_EOL;
        echo '    [--standard=<standard>] [--sniffs=<sniffs>]'.PHP_EOL;
        echo '    <file> - ...'.PHP_EOL;
        echo '        -s            Show sniff codes in all reports'.PHP_EOL;
        echo '        -p            Show progress of the run'.PHP_EOL;
        echo '        -m            Stop error messages from being recorded'.PHP_EOL;
        echo '                      (saves a lot of memory, but stops many reports from being used)'.PHP_EOL;
        echo '        -i            Show a list of installed coding standards'.PHP_EOL;
        echo '        --help        Print this help message'.PHP_EOL;
        echo '        --version     Print version information'.PHP_EOL;
        echo '        <file>        One or more files and/or directories to check'.PHP_EOL;
        echo '        <sniffs>      A comma separated list of sniff codes to limit the check to'.PHP_EOL;
        echo '                      (all sniffs must be part of the specified standard)'.PHP_EOL;
        echo '        <standard>    The name or path of the coding standard to use'.PHP_EOL;

    }//end printPHPCSUsage()


    /**
     * Prints out the usage information for PHPCBF.
     *
     * @return void
     */
    public function printPHPCBFUsage()
    {
        echo 'Usage: phpcbf '.PHP_EOL;
        echo '    [--standard=<standard>] [--sniffs=<sniffs>]'.PHP_EOL;
        echo '    <file> - ...'.PHP_EOL;
        echo '        -i            Show a list of installed coding standards'.PHP_EOL;
        echo '        --help        Print this help message'.PHP_EOL;
        echo '        --version     Print version information'.PHP_EOL;
        echo '        <file>        One or more files and/or directories to fix'.PHP_EOL;
        echo '        <sniffs>      A comma separated list of sniff codes to limit the fixes to'.PHP_EOL;
        echo '                      (all sniffs must be part of the specified standard)'.PHP_EOL;
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
