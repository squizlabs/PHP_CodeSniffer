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

if (class_exists('PHP_CodeSniffer\Autoload', false) === false) {
    class Autoload
    {

        /**
         * The composer autoloader.
         *
         * @var \Composer\Autoload\ClassLoader
         */
        private static $composerAutoloader = null;

        /**
         * A mapping of file names to class names.
         *
         * @var array<string, string>
         */
        private static $loadedClasses = [];

        /**
         * A mapping of class names to file names.
         *
         * @var array<string, string>
         */
        private static $loadedFiles = [];

        /**
         * A list of additional directories to search during autoloading.
         *
         * This is typically a list of coding standard directories.
         *
         * @var string[]
         */
        private static $searchPaths = [];


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
            // Include the composer autoloader if there is one, but re-register it
            // so this autoloader runs before the composer one as we need to include
            // all files so we can figure out what the class/interface/trait name is.
            if (self::$composerAutoloader === null) {
                // Make sure we don't try to load any of Composer's classes
                // while the autoloader is being setup.
                if (strpos($class, 'Composer\\') === 0) {
                    return;
                }

                if (strpos(__DIR__, 'phar://') !== 0
                    && @file_exists(__DIR__.'/../../autoload.php') === true
                ) {
                    self::$composerAutoloader = include __DIR__.'/../../autoload.php';
                    if (self::$composerAutoloader instanceof \Composer\Autoload\ClassLoader) {
                        self::$composerAutoloader->unregister();
                        self::$composerAutoloader->register();
                    } else {
                        // Something went wrong, so keep going without the autoloader
                        // although namespaced sniffs might error.
                        self::$composerAutoloader = false;
                    }
                } else {
                    self::$composerAutoloader = false;
                }
            }//end if

            $ds   = DIRECTORY_SEPARATOR;
            $path = false;

            if (substr($class, 0, 16) === 'PHP_CodeSniffer\\') {
                if (substr($class, 0, 22) === 'PHP_CodeSniffer\Tests\\') {
                    $isInstalled = !is_dir(__DIR__.$ds.'tests');
                    if ($isInstalled === false) {
                        $path = __DIR__.$ds.'tests';
                    } else {
                        $path = '@test_dir@'.$ds.'PHP_CodeSniffer'.$ds.'CodeSniffer';
                    }

                    $path .= $ds.substr(str_replace('\\', $ds, $class), 22).'.php';
                } else {
                    $path = __DIR__.$ds.'src'.$ds.substr(str_replace('\\', $ds, $class), 16).'.php';
                }
            }

            // See if the composer autoloader knows where the class is.
            if ($path === false && self::$composerAutoloader !== false) {
                $path = self::$composerAutoloader->findFile($class);
            }

            // See if the class is inside one of our alternate search paths.
            if ($path === false) {
                foreach (self::$searchPaths as $searchPath => $nsPrefix) {
                    $className = $class;
                    if ($nsPrefix !== '' && substr($class, 0, strlen($nsPrefix)) === $nsPrefix) {
                        $className = substr($class, (strlen($nsPrefix) + 1));
                    }

                    $path = $searchPath.$ds.str_replace('\\', $ds, $className).'.php';
                    if (is_file($path) === true) {
                        break;
                    }

                    $path = false;
                }
            }

            if ($path !== false && is_file($path) === true) {
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
            if (strpos(__DIR__, 'phar://') !== 0) {
                $path = realpath($path);
                if ($path === false) {
                    return false;
                }
            }

            if (isset(self::$loadedClasses[$path]) === true) {
                return self::$loadedClasses[$path];
            }

            $classesBeforeLoad = [
                'classes'    => get_declared_classes(),
                'interfaces' => get_declared_interfaces(),
                'traits'     => get_declared_traits(),
            ];

            include $path;

            $classesAfterLoad = [
                'classes'    => get_declared_classes(),
                'interfaces' => get_declared_interfaces(),
                'traits'     => get_declared_traits(),
            ];

            $className = self::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);

            self::$loadedClasses[$path]    = $className;
            self::$loadedFiles[$className] = $path;
            return self::$loadedClasses[$path];

        }//end loadFile()


        /**
         * Determine which class was loaded based on the before and after lists of loaded classes.
         *
         * @param array $classesBeforeLoad The classes/interfaces/traits before the file was included.
         * @param array $classesAfterLoad  The classes/interfaces/traits after the file was included.
         *
         * @return string The fully qualified name of the class in the loaded file.
         */
        public static function determineLoadedClass($classesBeforeLoad, $classesAfterLoad)
        {
            $className = null;

            $newClasses = array_diff($classesAfterLoad['classes'], $classesBeforeLoad['classes']);
            if (PHP_VERSION_ID < 70400) {
                $newClasses = array_reverse($newClasses);
            }

            // Since PHP 7.4 get_declared_classes() does not guarantee any order, making
            // it impossible to use order to determine which is the parent an which is the child.
            // Let's reduce the list of candidates by removing all the classes known to be "parents".
            // That way, at the end, only the "main" class just included will remain.
            $newClasses = array_reduce(
                $newClasses,
                function ($remaining, $current) {
                    return array_diff($remaining, class_parents($current));
                },
                $newClasses
            );

            foreach ($newClasses as $name) {
                if (isset(self::$loadedFiles[$name]) === false) {
                    $className = $name;
                    break;
                }
            }

            if ($className === null) {
                $newClasses = array_reverse(array_diff($classesAfterLoad['interfaces'], $classesBeforeLoad['interfaces']));
                foreach ($newClasses as $name) {
                    if (isset(self::$loadedFiles[$name]) === false) {
                        $className = $name;
                        break;
                    }
                }
            }

            if ($className === null) {
                $newClasses = array_reverse(array_diff($classesAfterLoad['traits'], $classesBeforeLoad['traits']));
                foreach ($newClasses as $name) {
                    if (isset(self::$loadedFiles[$name]) === false) {
                        $className = $name;
                        break;
                    }
                }
            }

            return $className;

        }//end determineLoadedClass()


        /**
         * Adds a directory to search during autoloading.
         *
         * @param string $path     The path to the directory to search.
         * @param string $nsPrefix The namespace prefix used by files under this path.
         *
         * @return void
         */
        public static function addSearchPath($path, $nsPrefix='')
        {
            self::$searchPaths[$path] = rtrim(trim((string) $nsPrefix), '\\');

        }//end addSearchPath()


        /**
         * Retrieve the namespaces and paths registered by external standards.
         *
         * @return array
         */
        public static function getSearchPaths()
        {
            return self::$searchPaths;

        }//end getSearchPaths()


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
}//end if
