<?php
/**
 * Functions for helping process standards.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util;

use PHP_CodeSniffer\Config;

class Standards
{


    /**
     * Saves the namespaces of the found standards and their short names.
     * ['Generic' => 'Generic', 'Rhorber\PHPCS\Standard' => 'Rhorber', ...]
     *
     * @var array
     */
    private static $namespaceStandardArray = null;


    /**
     * Get a list paths where standards are installed.
     *
     * @return array
     */
    public static function getInstalledStandardPaths()
    {
        $ds = DIRECTORY_SEPARATOR;

        $installedPaths = array(dirname(dirname(__DIR__)).$ds.'src'.$ds.'Standards');
        $configPaths    = Config::getConfigData('installed_paths');
        if ($configPaths !== null) {
            $installedPaths = array_merge($installedPaths, explode(',', $configPaths));
        }

        $resolvedInstalledPaths = array();
        foreach ($installedPaths as $installedPath) {
            if (substr($installedPath, 0, 1) === '.') {
                $installedPath = Common::realPath(__DIR__.$ds.'..'.$ds.'..'.$ds.$installedPath);
            }

            $resolvedInstalledPaths[] = $installedPath;
        }

        return $resolvedInstalledPaths;

    }//end getInstalledStandardPaths()


    /**
     * Get the details of all coding standards installed.
     *
     * Coding standards are directories located in the
     * CodeSniffer/Standards directory. Valid coding standards
     * include a Sniffs subdirectory.
     *
     * The details returned for each standard are:
     * - path:      the path to the coding standard's main directory
     * - name:      the name of the coding standard, as sourced from the ruleset.xml file
     * - namespace: the namespace used by the coding standard, as sourced from the ruleset.xml file
     *
     * If you only need the paths to the installed standards,
     * use getInstalledStandardPaths() instead as it performs less work to
     * retrieve coding standard names.
     *
     * @param boolean $includeGeneric If true, the special "Generic"
     *                                coding standard will be included
     *                                if installed.
     * @param string  $standardsDir   A specific directory to look for standards
     *                                in. If not specified, PHP_CodeSniffer will
     *                                look in its default locations.
     *
     * @return array
     * @see    getInstalledStandardPaths()
     */
    public static function getInstalledStandardDetails(
        $includeGeneric=false,
        $standardsDir=''
    ) {
        $rulesets = array();

        if ($standardsDir === '') {
            $installedPaths = self::getInstalledStandardPaths();
        } else {
            $installedPaths = array($standardsDir);
        }

        foreach ($installedPaths as $standardsDir) {
            // Check if the installed dir is actually a standard itself.
            $csFile = $standardsDir.'/ruleset.xml';
            if (is_file($csFile) === true) {
                $rulesets[] = $csFile;
                continue;
            }

            $di = new \DirectoryIterator($standardsDir);
            foreach ($di as $file) {
                if ($file->isDir() === true && $file->isDot() === false) {
                    $filename = $file->getFilename();

                    // Ignore the special "Generic" standard.
                    if ($includeGeneric === false && $filename === 'Generic') {
                        continue;
                    }

                    // Valid coding standard dirs include a ruleset.
                    $csFile = $file->getPathname().'/ruleset.xml';
                    if (is_file($csFile) === true) {
                        $rulesets[] = $csFile;
                    }
                }
            }
        }//end foreach

        $installedStandards = array();

        foreach ($rulesets as $rulesetPath) {
            $ruleset = simplexml_load_string(file_get_contents($rulesetPath));
            if ($ruleset === false) {
                continue;
            }

            $standardName = (string) $ruleset['name'];
            $dirname      = basename(dirname($rulesetPath));

            if (isset($ruleset['namespace']) === true) {
                $namespace = (string) $ruleset['namespace'];
            } else {
                $namespace = $dirname;
            }

            $installedStandards[$dirname] = array(
                                             'path'      => dirname($rulesetPath),
                                             'name'      => $standardName,
                                             'namespace' => $namespace,
                                            );
        }//end foreach

        return $installedStandards;

    }//end getInstalledStandardDetails()


    /**
     * Get a list of all coding standards installed.
     *
     * Coding standards are directories located in the
     * CodeSniffer/Standards directory. Valid coding standards
     * include a Sniffs subdirectory.
     *
     * @param boolean $includeGeneric If true, the special "Generic"
     *                                coding standard will be included
     *                                if installed.
     * @param string  $standardsDir   A specific directory to look for standards
     *                                in. If not specified, PHP_CodeSniffer will
     *                                look in its default locations.
     *
     * @return array
     * @see    isInstalledStandard()
     */
    public static function getInstalledStandards(
        $includeGeneric=false,
        $standardsDir=''
    ) {
        $installedStandards = array();

        if ($standardsDir === '') {
            $installedPaths = self::getInstalledStandardPaths();
        } else {
            $installedPaths = array($standardsDir);
        }

        foreach ($installedPaths as $standardsDir) {
            // Check if the installed dir is actually a standard itself.
            $csFile = $standardsDir.'/ruleset.xml';
            if (is_file($csFile) === true) {
                $installedStandards[] = basename($standardsDir);
                continue;
            }

            if (is_dir($standardsDir) === false) {
                // Doesn't exist.
                continue;
            }

            $di = new \DirectoryIterator($standardsDir);
            foreach ($di as $file) {
                if ($file->isDir() === true && $file->isDot() === false) {
                    $filename = $file->getFilename();

                    // Ignore the special "Generic" standard.
                    if ($includeGeneric === false && $filename === 'Generic') {
                        continue;
                    }

                    // Valid coding standard dirs include a ruleset.
                    $csFile = $file->getPathname().'/ruleset.xml';
                    if (is_file($csFile) === true) {
                        $installedStandards[] = $filename;
                    }
                }
            }
        }//end foreach

        return $installedStandards;

    }//end getInstalledStandards()


    /**
     * Determine if a standard is installed.
     *
     * Coding standards are directories located in the
     * CodeSniffer/Standards directory. Valid coding standards
     * include a ruleset.xml file.
     *
     * @param string $standard The name of the coding standard.
     *
     * @return boolean
     * @see    getInstalledStandards()
     */
    public static function isInstalledStandard($standard)
    {
        $path = self::getInstalledStandardPath($standard);
        if ($path !== null && strpos($path, 'ruleset.xml') !== false) {
            return true;
        } else {
            // This could be a custom standard, installed outside our
            // standards directory.
            $standard = Common::realPath($standard);

            // Might be an actual ruleset file itUtil.
            // If it has an XML extension, let's at least try it.
            if (is_file($standard) === true
                && (substr(strtolower($standard), -4) === '.xml'
                || substr(strtolower($standard), -9) === '.xml.dist')
            ) {
                return true;
            }

            // If it is a directory with a ruleset.xml file in it,
            // it is a standard.
            $ruleset = rtrim($standard, ' /\\').DIRECTORY_SEPARATOR.'ruleset.xml';
            if (is_file($ruleset) === true) {
                return true;
            }
        }//end if

        return false;

    }//end isInstalledStandard()


    /**
     * Return the path of an installed coding standard.
     *
     * Coding standards are directories located in the
     * CodeSniffer/Standards directory. Valid coding standards
     * include a ruleset.xml file.
     *
     * @param string $standard The name of the coding standard.
     *
     * @return string|null
     */
    public static function getInstalledStandardPath($standard)
    {
        $installedPaths = self::getInstalledStandardPaths();
        foreach ($installedPaths as $installedPath) {
            $standardPath = $installedPath.DIRECTORY_SEPARATOR.$standard;
            if (file_exists($standardPath) === false && basename($installedPath) === $standard) {
                $standardPath = $installedPath;
            }

            $path = Common::realpath($standardPath.DIRECTORY_SEPARATOR.'ruleset.xml');

            if (is_file($path) === true) {
                return $path;
            } else if (Common::isPharFile($standardPath) === true) {
                $path = Common::realpath($standardPath);
                if ($path !== false) {
                    return $path;
                }
            }
        }

        return null;

    }//end getInstalledStandardPath()


    /**
     * Prints out a list of installed coding standards.
     *
     * @return void
     */
    public static function printInstalledStandards()
    {
        $installedStandards = self::getInstalledStandards();
        $numStandards       = count($installedStandards);

        if ($numStandards === 0) {
            echo 'No coding standards are installed.'.PHP_EOL;
        } else {
            $lastStandard = array_pop($installedStandards);
            if ($numStandards === 1) {
                echo "The only coding standard installed is $lastStandard".PHP_EOL;
            } else {
                $standardList  = implode(', ', $installedStandards);
                $standardList .= ' and '.$lastStandard;
                echo 'The installed coding standards are '.$standardList.PHP_EOL;
            }
        }

    }//end printInstalledStandards()


    /**
     * Returns a standard's name for the given namespace.
     *
     * @param string $namespace The namespace to search the standard name for.
     *
     * @return string
     */
    public static function getStandardOfNamespace($namespace)
    {
        if (self::$namespaceStandardArray === null) {
            $standardDetails = self::getInstalledStandardDetails(true);
            foreach ($standardDetails as $standardName => $details) {
                $standardNamespace = strtolower($details['namespace']);
                self::$namespaceStandardArray[$standardNamespace] = $standardName;
            }
        }

        $lowerNamespace = strtolower($namespace);
        if (isset(self::$namespaceStandardArray[$lowerNamespace]) === true) {
            return self::$namespaceStandardArray[$lowerNamespace];
        } else {
            $parts = explode('\\', $namespace);
            return array_pop($parts);
        }

    }//end getStandardOfNamespace()


}//end class
