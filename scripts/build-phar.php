#!/usr/bin/env php
<?php
/**
 * Build a PHPCS phar.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

error_reporting(E_ALL | E_STRICT);

if (ini_get('phar.readonly') === '1') {
    echo 'Unable to build, phar.readonly in php.ini is set to read only.'.PHP_EOL;
    exit(1);
}

$scripts = [
    'phpcs',
    'phpcbf',
];

foreach ($scripts as $script) {
    echo "Building $script phar".PHP_EOL;

    $pharName = $script.'.phar';
    $pharFile = getcwd().'/'.$pharName;
    echo "\t=> $pharFile".PHP_EOL;
    if (file_exists($pharFile) === true) {
        echo "\t** file exists, removing **".PHP_EOL;
        unlink($pharFile);
    }

    $phar = new Phar($pharFile, 0, $pharName);

    /*
        Add the files.
    */

    echo "\t=> adding files... ";

    $srcDir    = realpath(__DIR__.'/../src');
    $srcDirLen = strlen($srcDir);

    $rdi = new \RecursiveDirectoryIterator($srcDir, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
    $di  = new \RecursiveIteratorIterator($rdi, 0, \RecursiveIteratorIterator::CATCH_GET_CHILD);

    foreach ($di as $file) {
        $filename = $file->getFilename();

        // Skip hidden files.
        if (substr($filename, 0, 1) === '.') {
            continue;
        }

        $fullpath = $file->getPathname();
        if (strpos($fullpath, '/Tests/') !== false) {
            continue;
        }

        $path = 'src'.substr($fullpath, $srcDirLen);

        $phar->addFromString($path, php_strip_whitespace($fullpath));
    }

    // Add autoloader.
    $phar->addFromString('autoload.php', php_strip_whitespace(realpath(__DIR__.'/../autoload.php')));

    // Add licence file.
    $phar->addFromString('licence.txt', php_strip_whitespace(realpath(__DIR__.'/../licence.txt')));

    echo 'done'.PHP_EOL;

    /*
        Add the stub.
    */

    echo "\t=> adding stub... ";
    $stub  = '#!/usr/bin/env php'."\n";
    $stub .= '<?php'."\n";
    $stub .= 'Phar::mapPhar(\''.$pharName.'\');'."\n";
    $stub .= 'require_once "phar://'.$pharName.'/autoload.php";'."\n";
    $stub .= '$runner = new PHP_CodeSniffer\Runner();'."\n";
    $stub .= '$exitCode = $runner->run'.$script.'();'."\n";
    $stub .= 'exit($exitCode);'."\n";
    $stub .= '__HALT_COMPILER();';
    $phar->setStub($stub);

    echo 'done'.PHP_EOL;
}//end foreach
