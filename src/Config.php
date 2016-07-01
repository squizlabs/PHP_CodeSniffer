<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

final class Config
{
    /**
     * @var string
     */
    const VERSION = '3.0.0';

    /**
     * This array is not meant to be accessed directly. Instead, use the settings
     * as if they are class member vars so the __get() and __set() magic methods
     * can be used to validate the values. For example, to set the verbosity level to
     * level 2, use $this->verbosity = 2; insteas of accessing this property directly.
     *
     * @var array<string, mixed>
     */
    private $settings = [
        'files'           => null,
        'standards'       => ['PSR2'],
        'showSources'     => true,
        'recordErrors'     => true,
        'extensions'      => ['php' => 'PHP'],
        'sniffs'          => null,
        'reports'       => ['full' => null],
        'reportWidth'     => null,
        'cache'       => true,
    ];

    /**
     * Unknown arguments
     *
     * @var array<mixed>
     */
    private $values = array();

    /**
     * @var bool
     */
    private $recordErrors = true;

    /**
     * @var bool
     */
    public $showProgress = true;

//    /**
//     * Get the value of an inaccessible property.
//     *
//     * @param string $name The name of the property.
//     *
//     * @return mixed
//     * @throws RuntimeException If the setting name is invalid.
//     */
//    public function __get($name)
//    {
//        if (array_key_exists($name, $this->settings) === false) {
//            throw new RuntimeException("ERROR: unable to get value of property \"$name\"");
//        }
//
//        return $this->settings[$name];
//
//    }//end __get()


//    /**
//     * Set the value of an inaccessible property.
//     *
//     * @param string $name  The name of the property.
//     * @param mixed  $value The value of the property.
//     *
//     * @return void
//     * @throws RuntimeException If the setting name is invalid.
//     */
//    public function __set($name, $value)
//    {
//        if (array_key_exists($name, $this->settings) === false) {
//            throw new RuntimeException("Can't __set() $name; setting doesn't exist");
//        }
//
//        switch ($name) {
//        case 'standards' :
//            $cleaned = array();
//
//            // Check if the standard name is valid, or if the case is invalid.
//            $installedStandards = Util\Standards::getInstalledStandards();
//            foreach ($value as $standard) {
//                foreach ($installedStandards as $validStandard) {
//                    if (strtolower($standard) === strtolower($validStandard)) {
//                        $standard = $validStandard;
//                        break;
//                    }
//                }
//
//                $cleaned[] = $standard;
//            }
//
//            $value = $cleaned;
//            break;
//        default :
//            // No validation required.
//            break;
//        }//end switch
//
//        $this->settings[$name] = $value;
//
//    }//end __set()


//    /**
//     * Check if the value of an inaccessible property is set.
//     *
//     * @param string $name The name of the property.
//     *
//     * @return bool
//     */
//    public function __isset($name)
//    {
//        return isset($this->settings[$name]);
//
//    }//end __isset()

//
//    /**
//     * Unset the value of an inaccessible property.
//     *
//     * @param string $name The name of the property.
//     *
//     * @return void
//     */
//    public function __unset($name)
//    {
//        $this->settings[$name] = null;
//
//    }//end __unset()


    /**
     * Creates a Config object and populates it with command line values.
     *
     * @param array $cliArgs         An array of values gathered from CLI args.
     */
    public function __construct(array $cliArgs = [])
    {
        // set default report width
        if (preg_match('|\d+ (\d+)|', shell_exec('stty size 2>&1'), $matches) === 1) {
            $this->reportWidth = (int) $matches[1];
        }
    }

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
            } else if (substr($arg, 0, 9) === 'standard=') {
                $standards = trim(substr($arg, 9));
                if ($standards !== '') {
                    $this->standards = explode(',', $standards);
                }
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
