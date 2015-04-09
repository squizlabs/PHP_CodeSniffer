<?php
spl_autoload_register(
    function ($class) {
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
            include $path;
            return true;
        }

        return false;
    }
);