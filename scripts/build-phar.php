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
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

error_reporting(E_ALL | E_STRICT);

if (ini_get('phar.readonly') === '1') {
    echo 'Unable to build, phar.readonly in php.ini is set to read only.'."\n";
    exit(1);
}

$validOptions = array(
                 '--build-full'     => 'Build a full installation of phpcs (including tests)',
                 '--build-only'     => 'Build phpcs with only the specified standard.',
                 '--build-standard' => 'Build the standard phpcs',
                );
$requireOpts  = array('--build-only');

$name     = NULL;
$options  = array();
$destDir  = dirname(dirname(__FILE__));
$showHelp = false;
foreach ($argv as $index => $arg) {
    if ($arg === '--help') {
        $showHelp = 'Showing help...';
        break;
    }

    if ($index === 0) {
        // Skipping script name.
        continue;
    } else if ($index === 1 && strpos($arg, '-') !== 0) {
        $name = $arg;
    } else if ($index === 2 && strpos($arg, '-') !== 0) {
        $destDir = $arg;
    } else {
        // Other arg.
        $value = null;
        if (strpos($arg, '--') === 0 && strpos($arg, '=') !== false) {
            list($arg, $value) = explode('=', $arg);
            if (array_key_exists($arg, $validOptions) === false) {
                $showHelp = 'Not a valid arg: '.$arg;
            }
        } else if (strpos($arg, '--') === 0 && strpos($arg, '=') === false) {
            $value = true;

            if (array_key_exists($arg, $validOptions) === false) {
                $showHelp = 'Not a valid arg: '.$arg;
            } else if (in_array($arg, $requireOpts) === true) {
                $showHelp = 'Missing value on '.$arg;
            }
        } else {
            $showHelp = 'Unknown arg: '.$arg;
        }

        $arg           = ltrim($arg, '-');
        $options[$arg] = $value;
    }
}

if ($name === NULL || $showHelp !== false) {
    if ($showHelp !== false) {
        echo $showHelp."\n";
    }

    echo 'Usage: '.$argv[0].' <name> <destinationDir> <options>'."\n";
    echo ' where <options> can be:'."\n";
    foreach ($validOptions as $option => $optHelp) {
        echo "\t".$option;
        $max = 20;
        if (in_array($option, $requireOpts) === true) {
            echo '=xx';
            $max = ($max - 3);
        }

        echo str_pad('', ($max - strlen($option)), ' ');
        echo $optHelp."\n";
    }

    exit(0);
}

if (is_file(dirname(__FILE__).'/../CodeSniffer.php') === true) {
    include_once dirname(__FILE__).'/../CodeSniffer.php';
} else {
    include_once 'PHP/CodeSniffer.php';
}

build($name, $destDir, $options);


/**
 * Build CodeSniffer into a phar file.
 *
 * @param string $name    The name of the phar file.
 * @param string $destDir Where to place the finished product.
 * @param array  $options The build options.
 *
 * @return void
 */
function build($name, $destDir, $options)
{
    if (substr(strtolower($name), -5) !== '.phar') {
        $name .= '.phar';
    }

    $pharFile = PHP_CodeSniffer::realpath($destDir).'/'.$name;

    // Force remove itself and git files.
    $remove[] = '.git';
    $remove[] = '.gitattributes';
    $remove[] = '.gitignore';
    $remove[] = 'scripts';
    $remove[] = basename($name);
    if (file_exists($name) === true) {
        unlink($name);
    }

    if (array_key_exists('build-full', $options) === false) {
        $remove[] = 'tests';
        $remove[] = 'Tests';
    }//end if

    $whitelist = array();
    if (array_key_exists('build-only', $options) === true
        && $options['build-only'] !== true
    ) {
        if (PHP_CodeSniffer::isInstalledStandard($options['build-only']) === true) {
            $phpcs       = new PHP_CodeSniffer();
            $whitelist   = $phpcs->getSniffFiles(dirname(__FILE__).'/CodeSniffer/Standards/'.$options['build-only'], $options['build-only']);
            foreach ($whitelist as $file) {
                findDependencies($file, $whitelist);
            }

            $whitelist[] = dirname(__FILE__).'/CodeSniffer/Standards';
            $whitelist[] = dirname(__FILE__).'/CodeSniffer/Standards/AbstractPatternSniff.php';
            $whitelist[] = dirname(__FILE__).'/CodeSniffer/Standards/AbstractScopeSniff.php';
            $whitelist[] = dirname(__FILE__).'/CodeSniffer/Standards/AbstractVariableSniff.php';
            $whitelist[] = dirname(__FILE__).'/CodeSniffer/Standards/IncorrectPatternException.php';
            $whitelist[] = dirname(__FILE__).'/CodeSniffer/Standards/'.$options['build-only'];
            $whitelist[] = dirname(__FILE__).'/CodeSniffer/Standards/'.$options['build-only'].'/ruleset.xml';
            $remove[]    = 'Standards';
        } else {
            echo 'Unable to build phar file with non-existing standard: '.$options['build-only']."\n";
            return;
        }
    }

    $phar = new Phar($pharFile, 0, 'CodeSniffer.phar');
    buildFromDirectory($phar, dirname(dirname(__FILE__)), $remove, $whitelist);
    addStub($phar);
    addConfigFile($phar, $options);

}//end build()


/**
 * Build the phar for a directory.
 *
 * @param object &$phar     The Phar class.
 * @param string $baseDir   The directory to build.
 * @param array  $remove    Files to remove.
 * @param array  $whitelist The whitelist files to include.
 *
 * @return void
 */
function buildFromDirectory(&$phar, $baseDir, $remove=array(), $whitelist=array())
{
    $prefix   = 'phar://'.$phar->getPath();
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        $removed = false;
        foreach ($remove as $r) {
            if (($r === $file->getFileName()
                || preg_match('/\/'.$r.'(\/|$)/i', $file->getPath()) > 0)
                && in_array($file->getPath().'/'.$file->getFileName(), $whitelist) === false
            ) {
                $removed = true;
                break;
            }
        }

        if ($removed === true) {
            continue;
        }

        if ($file->isDir() === true) {
            $path = $file->getPath().'/'.$file->getFileName();
            $path = str_replace($baseDir, '', $path);
            $phar->addEmptyDir($path);
        } else {
            $fileLoc = ltrim(str_replace($baseDir, '', $file->getPath().'/'.$file->getFileName()), '/');
            $content = file_get_contents($file->getPath().'/'.$file->getFileName());
            file_put_contents($prefix.'/'.$fileLoc, $content);

            // Compress.
            $phar[$fileLoc]->compress(Phar::GZ);
        }//end if
    }//end foreach

}//end buildFromDirectory()


/**
 * Find the dependencies for a sniff file.
 *
 * @param string $file       The path to the sniff file.
 * @param array  &$whitelist The current whitelist.
 *
 * @return void
 */
function findDependencies($file, array &$whitelist)
{
    $className = str_replace(dirname(__FILE__).'/CodeSniffer/Standards/', '', substr($file, 0, -4));
    $className = str_replace('/', '_', $className);
    if (strpos($className, 'Abstract') === 0) {
        $className = 'PHP_CodeSniffer_Standards_'.$className;
    }

    if (class_exists($className) === false) {
        include_once $file;
    }

    if (class_exists($className) === true) {
        // Finding any parent sniff classes.
        $class       = new ReflectionClass($className);
        $parentClass = $class->getParentClass();
        if ($parentClass !== false) {
            $whitelist[] = $parentClass->getFileName();
            findDependencies($parentClass->getFileName(), $whitelist);
        }

        // Finding any class created inside the file.
        preg_match_all('/= new ([a-z0-9_]*)\(\)/ims', file_get_contents($file), $matches);
        if (isset($matches[1]) === true) {
            foreach ($matches[1] as $match) {
                if (isset($match) === true && strpos($match, '_Sniffs_') !== false) {
                    $interiorClass = $match;
                    $intClassFile  = dirname(__FILE__).'/CodeSniffer/Standards/'.str_replace('_', '/', $interiorClass).'.php';
                    $whitelist[]   = $intClassFile;
                    findDependencies($intClassFile, $whitelist);
                }
            }
        }

        // Finding class_exist() calls.
        preg_match_all('/class_exists\(\'(.*?)\'/ims', file_get_contents($file), $matches);
        if (isset($matches[1]) === true) {
            foreach ($matches[1] as $match) {
                if (isset($match) === true && strpos($match, '_Sniffs_') !== false) {
                    $interiorClass = $match;
                    $intClassFile  = dirname(__FILE__).'/CodeSniffer/Standards/'.str_replace('_', '/', $interiorClass).'.php';
                    $whitelist[]   = $intClassFile;
                    findDependencies($intClassFile, $whitelist);
                }
            }
        }
    }

}//end findDependencies()


/**
 * Add the stub file to the phar.
 *
 * @param object &$phar The phar class.
 *
 * @return void
 */
function addStub(&$phar)
{
    $stub  = '<?php error_reporting(E_ALL | E_STRICT);';
    $stub .= '@include_once "PHP/Timer.php";';
    $stub .= 'if (class_exists("PHP_Timer", false) === true) {';
    $stub .= '    PHP_Timer::start();';
    $stub .= '}';
    $stub .= 'include_once "phar://".__FILE__."/CodeSniffer/CLI.php";';
    $stub .= '$phpcs = new PHP_CodeSniffer_CLI();';
    $stub .= '$phpcs->checkRequirements();';
    $stub .= '$numErrors = $phpcs->process();';
    $stub .= '__HALT_COMPILER(); ?'.'>';
    $phar->setStub($stub);

}//end addStub()


/**
 * Add a config to the phar file.
 *
 * @param object &$phar   The phar file.
 * @param array  $options The options to build with.
 *
 * @return void
 */
function addConfigFile(&$phar, $options)
{
    $phpCodeSnifferConfig = PHP_CodeSniffer::getAllConfigData();
    if (isset($options['build-only']) === true) {
        $phpCodeSnifferConfig['standard'] = $options['build-only'];
    }

    $output  = '<'.'?php'."\n".' $phpCodeSnifferConfig = ';
    $output .= var_export($phpCodeSnifferConfig, true);
    $output .= "\n?".'>';

    $phar->addFromString('CodeSniffer.conf', $output);
    $phar['CodeSniffer.conf']->compress(Phar::GZ);

}//end addConfigFile()

