<?php
/**
 * A class to process command line phpcs scripts.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (is_file(dirname(__FILE__).'/../CodeSniffer.php') === true) {
    include_once dirname(__FILE__).'/../CodeSniffer.php';
} else {
    include_once 'PHP/CodeSniffer.php';
}

/**
 * A class to process command line phpcs scripts.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_CLI
{


    /**
     * Exits if the minimum requirements of PHP_CodSniffer are not met.
     *
     * @return array
     */
    public function checkRequirements()
    {
        // Check the PHP version.
        if (version_compare(PHP_VERSION, '5.1.2') === -1) {
            echo 'ERROR: PHP_CodeSniffer requires PHP version 5.1.2 or greater.'.PHP_EOL;
            exit(2);
        }

        if (extension_loaded('tokenizer') === false) {
            echo 'ERROR: PHP_CodeSniffer requires the tokenizer extension to be enabled.'.PHP_EOL;
            exit(2);
        }

    }//end checkRequirements()


    /**
     * Get a list of default values for all possible command line arguments.
     *
     * @return array
     */
    public function getDefaults()
    {
        // The default values for config settings.
        $defaults['files']      = array();
        $defaults['standard']   = null;
        $defaults['verbosity']  = 0;
        $defaults['local']      = false;
        $defaults['extensions'] = array();
        $defaults['ignored']    = array();
        $defaults['generator']  = '';

        $defaults['report'] = PHP_CodeSniffer::getConfigData('report_format');
        if ($defaults['report'] === null) {
            $defaults['report'] = 'full';
        }

        $showWarnings = PHP_CodeSniffer::getConfigData('show_warnings');
        if ($showWarnings === null) {
            $defaults['showWarnings'] = true;
        } else {
            $defaults['showWarnings'] = (bool) $showWarnings;
        }

        $tabWidth = PHP_CodeSniffer::getConfigData('tab_width');
        if ($tabWidth === null) {
            $defaults['tabWidth'] = 0;
        } else {
            $defaults['tabWidth'] = (int) $tabWidth;
        }

        return $defaults;

    }//end getDefaults()


    /**
     * Process the command line arguments and returns the values.
     *
     * @return array
     */
    public function getCommandLineValues()
    {
        $values = $this->getDefaults();

        for ($i = 1; $i < $_SERVER['argc']; $i++) {
            $arg = $_SERVER['argv'][$i];
            if ($arg{0} === '-') {
                if ($arg{1} === '-') {
                    $values = $this->processLongArgument(substr($arg, 2), $i, $values);
                } else {
                    $switches = str_split($arg);
                    foreach ($switches as $switch) {
                        if ($switch === '-') {
                            continue;
                        }

                        $values = $this->processShortArgument($switch, $i, $values);
                    }
                }
            } else {
                $values = $this->processUnknownArgument($arg, $i, $values);
            }
        }//end for

        return $values;

    }//end getCommandLineValues()


    /**
     * Processes a sort (-e) command line argument.
     *
     * @param string $arg    The command line argument.
     * @param int    $pos    The position of the argument on the command line.
     * @param array  $values An array of values determined from CLI args.
     *
     * @return array The updated CLI values.
     * @see getCommandLineValues()
     */
    public function processShortArgument($arg, $pos, $values)
    {
        switch ($arg) {
        case 'h':
        case '?':
            $this->printUsage();
            exit(0);
            break;
        case 'i' :
            $this->printInstalledStandards();
            exit(0);
            break;
        case 'v' :
            $values['verbosity']++;
            break;
        case 'l' :
            $values['local'] = true;
            break;
        case 'n' :
            $values['showWarnings'] = false;
            break;
        case 'w' :
            $values['showWarnings'] = true;
            break;
        default:
            $values = $this->processUnknownArgument('-'.$arg, $pos, $values);
        }//end switch

        return $values;

    }//end processShortArgument()


    /**
     * Processes a long (--example) command line argument.
     *
     * @param string $arg    The command line argument.
     * @param int    $pos    The position of the argument on the command line.
     * @param array  $values An array of values determined from CLI args.
     *
     * @return array The updated CLI values.
     * @see getCommandLineValues()
     */
    public function processLongArgument($arg, $pos, $values)
    {
        switch ($arg) {
        case 'help':
            $this->printUsage();
            exit(0);
            break;
        case 'version':
            echo 'PHP_CodeSniffer version @package_version@ (@package_state@) ';
            echo 'by Squiz Pty Ltd. (http://www.squiz.net)'.PHP_EOL;
            exit(0);
            break;
        case 'config-set':
            $key   = $_SERVER['argv'][($pos + 1)];
            $value = $_SERVER['argv'][($pos + 2)];
            PHP_CodeSniffer::setConfigData($key, $value);
            exit(0);
            break;
        case 'config-delete':
            $key = $_SERVER['argv'][($pos + 1)];
            PHP_CodeSniffer::setConfigData($key, null);
            exit(0);
            break;
        case 'config-show':
            $data = PHP_CodeSniffer::getAllConfigData();
            print_r($data);
            exit(0);
            break;
        default:
            if (substr($arg, 0, 7) === 'report=') {
                $values['report'] = substr($arg, 7);
                $validReports     = array(
                                     'full',
                                     'xml',
                                     'checkstyle',
                                     'csv',
                                     'emacs',
                                     'summary',
                                    );

                if (in_array($values['report'], $validReports) === false) {
                    echo 'ERROR: Report type "'.$report.'" not known.'.PHP_EOL;
                    exit(2);
                }
            } else if (substr($arg, 0, 9) === 'standard=') {
                $values['standard'] = substr($arg, 9);
            } else if (substr($arg, 0, 11) === 'extensions=') {
                $values['extensions'] = explode(',', substr($arg, 11));
            } else if (substr($arg, 0, 7) === 'ignore=') {
                // Split the ignore string on commas, unless the comma is escaped
                // using 1 or 3 slashes (\, or \\\,).
                $values['ignored'] = preg_split('/(?<=(?<!\\\\)\\\\\\\\),|(?<!\\\\),/', substr($arg, 7));
            } else if (substr($arg, 0, 10) === 'generator=') {
                $values['generator'] = substr($arg, 10);
            } else if (substr($arg, 0, 10) === 'tab-width=') {
                $values['tabWidth'] = (int) substr($arg, 10);
            } else {
                $values = $this->processUnknownArgument('--'.$arg, $pos, $values);
            }//end if

            break;
        }//end switch

        return $values;

    }//end processLongArgument()


    /**
     * Processes an unknown command line argument.
     *
     * Assumes all unknown arguments are files and folders to check.
     *
     * @param string $arg    The command line argument.
     * @param int    $pos    The position of the argument on the command line.
     * @param array  $values An array of values determined from CLI args.
     *
     * @return array The updated CLI values.
     * @see getCommandLineValues()
     */
    public function processUnknownArgument($arg, $pos, $values)
    {
        // We don't know about any additional switches; just files.
        if ($arg{0} === '-') {
            echo 'ERROR: option "'.$arg.'" not known.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        }

        $file = realpath($arg);
        if (file_exists($file) === false) {
            echo 'ERROR: The file "'.$arg.'" does not exist.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        } else {
            $values['files'][] = $file;
        }

        return $values;

    }//end processUnknownArgument()


    /**
     * Runs PHP_CodeSniffer over files are directories.
     *
     * @param array $values An array of values determined from CLI args.
     *
     * @return int The number of error and warning messages shown.
     * @see getCommandLineValues()
     */
    public function process($values=array())
    {
        if (empty($values) === true) {
            $values = $this->getCommandLineValues();
        }

        if ($values['generator'] !== '') {
            $phpcs = new PHP_CodeSniffer($values['verbosity']);
            $phpcs->generateDocs($values['standard'], $values['files'], $values['generator']);
            exit(0);
        }

        if (empty($values['files']) === true) {
            echo 'ERROR: You must supply at least one file or directory to process.'.PHP_EOL.PHP_EOL;
            $this->printUsage();
            exit(2);
        }

        $values['standard'] = $this->validateStandard($values['standard']);
        if (PHP_CodeSniffer::isInstalledStandard($values['standard']) === false) {
            // They didn't select a valid coding standard, so help them
            // out by letting them know which standards are installed.
            echo 'ERROR: the "'.$values['standard'].'" coding standard is not installed. ';
            $this->printInstalledStandards();
            exit(2);
        }

        $phpcs = new PHP_CodeSniffer($values['verbosity'], $values['tabWidth']);

        // Set file extensions if they were specified. Otherwise,
        // let PHP_CodeSniffer decide on the defaults.
        if (empty($values['extensions']) === false) {
            $phpcs->setAllowedFileExtensions($values['extensions']);
        }

        // Set ignore patterns if they were specified.
        if (empty($values['ignored']) === false) {
            $phpcs->setIgnorePatterns($values['ignored']);
        }

        $phpcs->process($values['files'], $values['standard'], array(), $values['local']);
        return $this->printErrorReport($phpcs, $values['report'], $values['showWarnings']);

    }//end process()


    /**
     * Prints the error report.
     *
     * @param PHP_CodeSniffer $phpcs        The PHP_CodeSniffer object containing
     *                                      the errors.
     * @param string          $report       The type of report to print.
     * @param bool            $showWarnings TRUE if warnings should also be printed.
     *
     * @return int The number of error and warning messages shown.
     */
    public function printErrorReport($phpcs, $report, $showWarnings)
    {
        switch ($report) {
        case 'xml':
            $numErrors = $phpcs->printXMLErrorReport($showWarnings);
            break;
        case 'checkstyle':
            $numErrors = $phpcs->printCheckstyleErrorReport($showWarnings);
            break;
        case 'csv':
            $numErrors = $phpcs->printCSVErrorReport($showWarnings);
            break;
        case 'emacs':
            $numErrors = $phpcs->printEmacsErrorReport($showWarnings);
            break;
        case 'summary':
            $numErrors = $phpcs->printErrorReportSummary($showWarnings);
            break;
        default:
            $numErrors = $phpcs->printErrorReport($showWarnings);
            break;
        }

        return $numErrors;

    }//end printErrorReport()


    /**
     * Convert the passed standard into a valid standard.
     *
     * Checks things like default values and case.
     *
     * @param string $standard The standard to validate.
     *
     * @return string
     */
    public function validateStandard($standard)
    {
        if ($standard === null) {
            // They did not supply a standard to use.
            // Try to get the default from the config system.
            $standard = PHP_CodeSniffer::getConfigData('default_standard');
            if ($standard === null) {
                $standard = 'PEAR';
            }
        }

        // Check if the standard name is valid. If not, check that the case
        // was not entered incorrectly.
        if (PHP_CodeSniffer::isInstalledStandard($standard) === false) {
            $installedStandards = PHP_CodeSniffer::getInstalledStandards();
            foreach ($installedStandards as $validStandard) {
                if (strtolower($standard) === strtolower($validStandard)) {
                    $standard = $validStandard;
                    break;
                }
            }
        }

        return $standard;

    }//end validateStandard()


    /**
     * Prints out the usage information for this script.
     *
     * @return void
     */
    public function printUsage()
    {
        echo 'Usage: phpcs [-nwlvi] [--report=<report>] [--standard=<standard>]'.PHP_EOL;
        echo '    [--config-set key value] [--config-delete key] [--config-show]'.PHP_EOL;
        echo '    [--generator=<generator>] [--extensions=<extensions>]'.PHP_EOL;
        echo '    [--ignore=<patterns>] [--tab-width=<width>] <file> ...'.PHP_EOL;
        echo '        -n           Do not print warnings'.PHP_EOL;
        echo '        -w           Print both warnings and errors (on by default)'.PHP_EOL;
        echo '        -l           Local directory only, no recursion'.PHP_EOL;
        echo '        -v[v][v]     Print verbose output'.PHP_EOL;
        echo '        -i           Show a list of installed coding standards'.PHP_EOL;
        echo '        --help       Print this help message'.PHP_EOL;
        echo '        --version    Print version information'.PHP_EOL;
        echo '        <file>       One or more files and/or directories to check'.PHP_EOL;
        echo '        <extensions> A comma separated list of file extensions to check'.PHP_EOL;
        echo '                     (only valid if checking a directory)'.PHP_EOL;
        echo '        <patterns>   A comma separated list of patterns that are used'.PHP_EOL;
        echo '                     to ignore directories and files'.PHP_EOL;
        echo '        <standard>   The name of the coding standard to use'.PHP_EOL;
        echo '        <width>      The number of spaces each tab represents'.PHP_EOL;
        echo '        <generator>  The name of a doc generator to use'.PHP_EOL;
        echo '                     (forces doc generation instead of checking)'.PHP_EOL;
        echo '        <report>     Print either the "full", "xml", "checkstyle",'.PHP_EOL;
        echo '                     "csv", "emacs" or "summary" report'.PHP_EOL;
        echo '                     (the "full" report is printed by default)'.PHP_EOL;

    }//end printUsage()


    /**
     * Prints out a list of installed coding standards.
     *
     * @return void
     */
    public function printInstalledStandards()
    {
        $installedStandards = PHP_CodeSniffer::getInstalledStandards();
        $numStandards       = count($installedStandards);

        if ($numStandards === 0) {
            echo 'No coding standards are installed.'.PHP_EOL;
        } else {
            $lastStandard = array_pop($installedStandards);
            if ($numStandards === 1) {
                echo 'The only coding standard installed is $lastStandard'.PHP_EOL;
            } else {
                $standardList  = implode(', ', $installedStandards);
                $standardList .= ' and '.$lastStandard;
                echo 'The installed coding standards are '.$standardList.PHP_EOL;
            }
        }

    }//end printInstalledStandards()


}//end class

?>
