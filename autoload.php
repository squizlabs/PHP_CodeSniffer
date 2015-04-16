<?php
namespace PHP_CodeSniffer;

class Autoload {

    private static $loadedClasses = array();
    private static $loadedFiles = array();

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
    }

    public static function loadFile($path)
    {
        if (isset(self::$loadedClasses[$path]) === true) {
            return self::$loadedClasses[$path];
        }

        $classes    = get_declared_classes();
        $interfaces = get_declared_interfaces();

        include $path;

        $className = array_pop(array_diff(get_declared_classes(), $classes));
        if ($className === null) {
            $className = array_pop(array_diff(get_declared_interfaces(), $interfaces));
        }

        self::$loadedClasses[$path] = $className;
        self::$loadedFiles[$className] = $path;
        return self::$loadedClasses[$path];
    }

    public static function getLoadedClassName($file)
    {
        if (isset(self::$loadedClasses[$file]) === false) {
            throw new \Exception("Cannot get class name for $file; file has not been included");
        }

        return self::$loadedClasses[$file];
    }

    public static function getLoadedFileName($class)
    {
        if (isset(self::$loadedFiles[$class]) === false) {
            throw new \Exception("Cannot get file name for $class; class has not been included");
        }

        return self::$loadedFiles[$class];
    }

}

spl_autoload_register(__NAMESPACE__.'\Autoload::load', true, true);