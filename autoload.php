<?php
/**
 * Autoloads files for PHP_CodeSniffer and tracks what has been loaded.
 *
 * Due to different namespaces being used for custom coding standards,
 * the autoloader keeps track of what class is loaded after a file is included,
 * even if the file is ultimately included by another autoloader (such as composer).
 *
 * This allows PHP_CodeSniffer to request the class name after loading a class
 * when it only knows the filename, without having to parse the file to find it.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer;

class Autoload
{

    /**
     * A mapping of file names to class names.
     *
     * @var array<string, string>
     */
    private static $loadedClasses = array();

    /**
     * A mapping of class names to file names.
     *
     * @var array<string, string>
     */
    private static $loadedFiles = array();


    /**
     * Loads a class.
     *
     * This method only loads classes that exist in the PHP_CodeSniffer namespace.
     * All other classes are ignored and loaded by subsequent autoloaders.
     *
     * @param string $class The name of the class to load.
     *
     * @return bool
     */
    public static function load($class)
    {
        $ds   = DIRECTORY_SEPARATOR;
        $path = null;

        if (substr($class, 0, 16) === 'PHP_CodeSniffer\\') {
            if (substr($class, 0, 22) === 'PHP_CodeSniffer\Tests\\') {
                $path = __DIR__.$ds.'tests'.$ds.substr(str_replace('\\', $ds, $class), 22).'.php';
            } else {
                $path = __DIR__.$ds.'src'.$ds.substr(str_replace('\\', $ds, $class), 16).'.php';
            }
        }

        if ($path !== null && is_file($path) === true) {
            self::loadFile($path);
            return true;
        }

        return false;

    }//end load()


    /**
     * Includes a file and tracks what class or interface was loaded as a result.
     *
     * @param string $path The path of the file to load.
     *
     * @return string The fully qualified name of the class in the loaded file.
     */
    public static function loadFile($path)
    {
        if (isset(self::$loadedClasses[$path]) === true) {
            return self::$loadedClasses[$path];
        }

        $classes = get_declared_classes();

        include $path;

        $className  = null;
        $newClasses = array_diff(get_declared_classes(), $classes);
        foreach ($newClasses as $name) {
            if (isset(self::$loadedFiles[$name]) === false) {
                $className = $name;
                break;
            }
        }

        if ($className === null) {
            $newClasses = array_diff(get_declared_interfaces(), $classes);
            foreach ($newClasses as $name) {
                if (isset(self::$loadedFiles[$name]) === false) {
                    $className = $name;
                    break;
                }
            }
        }

        self::$loadedClasses[$path]    = $className;
        self::$loadedFiles[$className] = $path;
        return self::$loadedClasses[$path];

    }//end loadFile()


    /**
     * Gets the class name for the given file path.
     *
     * @param string $path The name of the file.
     *
     * @throws \Exception If the file path has not been loaded.
     * @return string
     */
    public static function getLoadedClassName($path)
    {
        if (isset(self::$loadedClasses[$path]) === false) {
            throw new \Exception("Cannot get class name for $path; file has not been included");
        }

        return self::$loadedClasses[$path];

    }//end getLoadedClassName()


    /**
     * Gets the file path for the given class name.
     *
     * @param string $class The name of the class.
     *
     * @throws \Exception If the class name has not been loaded
     * @return string
     */
    public static function getLoadedFileName($class)
    {
        if (isset(self::$loadedFiles[$class]) === false) {
            throw new \Exception("Cannot get file name for $class; class has not been included");
        }

        return self::$loadedFiles[$class];

    }//end getLoadedFileName()


    /**
     * Gets the mapping of file names to class names.
     *
     * @return array<string, string>
     */
    public static function getLoadedClasses()
    {
        return self::$loadedClasses;

    }//end getLoadedClasses()


    /**
     * Gets the mapping of class names to file names.
     *
     * @return array<string, string>
     */
    public static function getLoadedFiles()
    {
        return self::$loadedFiles;

    }//end getLoadedFiles()


}//end class


// Register the autoloader before any existing autoloaders to ensure
// it gets a chance to hear about every autoload request, and record
// the file and class name for it.
spl_autoload_register(__NAMESPACE__.'\Autoload::load', true, true);
