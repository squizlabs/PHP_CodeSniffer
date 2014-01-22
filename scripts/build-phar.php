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
                 '--use-package'    => 'Use a package.xml as the build list',
                 '--build-full'     => 'Build a full installation of phpcs (including tests)',
                 '--build-only'     => 'Build phpcs with only the specified standard.',
                 '--build-standard' => 'Build the standard phpcs',
                );
$requireOpts  = array('--build-only', '--use-package');

$name     = null;
$options  = array();
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

if ($name === null || $showHelp !== false) {
    if ($showHelp !== false) {
        echo $showHelp."\n";
    }

    echo 'Usage: '.$argv[0].' <filename> <options>'."\n";
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

require_once dirname(__FILE__).'/../CodeSniffer.php';
build($name, $options);


/**
 * Build CodeSniffer into a phar file.
 *
 * @param string $path    The path and name of the phar file.
 * @param array  $options The build options.
 *
 * @return void
 */
function build($path, $options)
{
    if (substr(strtolower($path), -5) !== '.phar') {
        // Sanity check, add the .phar to the end if not there.
        $path .= '.phar';
    }

    $pharFile = PHP_CodeSniffer::realpath(dirname($path)).'/'.basename($path);
    if (file_exists($pharFile) === true) {
        unlink($pharFile);
    }

    $phar = new Phar($pharFile, 0, 'CodeSniffer.phar');
    if (isset($options['use-package']) === true) {
        if (file_exists($options['use-package']) === false) {
            // Sanity check first.
            echo 'Invalid package file.'."\n";
            exit(1);
        }

        $includeTests = false;
        if (isset($options['build-full']) === true) {
            $includeTests = true;
        }

        $standard  = null;
        $whitelist = array();
        if (isset($options['build-only']) === true) {
            $standard  = $options['build-only'];
            $whitelist = getStandardWhitelist($options['build-only']);
        }

        $package = new DOMDocument('1.0', 'utf-8');
        $loaded  = $package->loadXML(file_get_contents($options['use-package']));
        if ($loaded === false) {
            // Unable to load the package file.
            echo 'Invalid package file.'."\n";
            exit(1);
        }

        buildFromPackage($phar, $package, $standard, $includeTests, $whitelist);
    } else {
        // Otherwise we are building from a directory.
        $whitelist = array();
        $remove    = array(
                      '.git',
                      '.gitattributes',
                      '.gitignore',
                      'scripts',
                      '.travis.yml',
                      basename($path),
                     );

        if (array_key_exists('build-full', $options) === false) {
            // Remove the tests from everything but build-full.
            $remove[] = 'tests';
            $remove[] = 'Tests';
        }//end if

        if (array_key_exists('build-only', $options) === true) {
            $whitelist = getStandardWhitelist($options['build-only']);
            $remove[]  = 'Standards';
        }

        buildFromDirectory($phar, dirname(dirname(__FILE__)), $remove, $whitelist);
    }//end if

    addStub($phar);
    addConfigFile($phar, $options);

}//end build()

/**
 * Return a whitelist for the standard.
 *
 * @param string $standard The standard to whitelist.
 *
 * @return array
 */
function getStandardWhitelist($standard=null)
{
    $whitelist = array();
    if ($standard !== null) {
        // Build a single standard (with dependencies).
        if (PHP_CodeSniffer::isInstalledStandard($standard) === true) {
            $phpcs       = new PHP_CodeSniffer();
            $whitelist   = $phpcs->processRuleset(dirname(dirname(__FILE__)).'/CodeSniffer/Standards/'.$standard.'/ruleset.xml');
            foreach ($whitelist as $file) {
                findDependencies($file, $whitelist);
            }

            $whitelist[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards';
            $whitelist[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/AbstractPatternSniff.php';
            $whitelist[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/AbstractScopeSniff.php';
            $whitelist[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/AbstractVariableSniff.php';
            $whitelist[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/IncorrectPatternException.php';
            $whitelist[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/'.$standard;
            $whitelist[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/'.$standard.'/ruleset.xml';

            // Finally, add in the dependent ruleset.xml files.
            $rulesets   = findDependentRuleset($standard);
            $extraFiles = array();
            foreach ($rulesets as $ruleset) {
                $rStandard  = str_replace(dirname(dirname($ruleset)).'/', '', dirname($ruleset));
                $extraFiles = array_merge($extraFiles, getStandardWhitelist($rStandard));
            }

            $whitelist = array_merge($whitelist, $rulesets);
            $whitelist = array_merge($whitelist, $extraFiles);
        } else {
            echo 'Unable to build phar file with non-existing standard: '.$standard."\n";
            exit(1);
        }
    }

    return $whitelist;

}//end getStandardWhitelist()


/**
 * Build from a package list.
 *
 * @param object  &$phar        The Phar class.
 * @param object  $dom          The package dom.
 * @param string  $standard     The standard to build (if needed).
 * @param boolean $includeTests If true, include the tests.
 * @param array   $whitelist    The standard whitelist.
 *
 * @return void
 */
function buildFromPackage(&$phar, $dom, $standard=null, $includeTests=false, $whitelist=array())
{
    $contents = $dom->getElementsByTagName('contents');
    if ($contents->length === 0 || $contents->item(0)->hasChildNodes() === false) {
        // Invalid xml.
        echo 'Invalid package file.'."\n";
        exit(1);
    }

    $roles = array('php', 'data');
    if ($includeTests === true) {
        $roles[] = 'test';
    }

    $topLevels = $contents->item(0)->childNodes;
    $tlLength  = $topLevels->length;
    for ($l = 0; $l < $tlLength; $l++) {
        $currentLevel = $topLevels->item($l);
        buildFromNode($phar, $currentLevel, $roles, '', $standard, $whitelist);
    }

    // Finally, a couple of last additions.
    $files = array(
              'README.markdown',
              'composer.json',
              'licence.txt',
              'package.xml',
             );
    foreach ($files as $file) {
        $phar->addFile(dirname(dirname(__FILE__)).'/'.$file, $file);
        $phar[$file]->compress(Phar::GZ);
    }

}//end buildFromPackage()


/**
 * Add from a node.
 *
 * @param object &$phar     The Phar class.
 * @param object $node      The node to add.
 * @param array  $roles     The roles allowed.
 * @param string $prefix    The prefix of the structure.
 * @param string $standard  The standard to include.
 * @param array  $whitelist The files that make up the standard.
 *
 * @return void
 */
function buildFromNode(&$phar, $node, $roles, $prefix='', $standard=null, $whitelist=array())
{
    $nodeName = $node->nodeName;
    if ($nodeName !== 'dir' && $nodeName !== 'file') {
        // Invalid node.
        return;
    }

    $path = $prefix.$node->getAttribute('name');
    if (in_array($node->getAttribute('role'), $roles) === true) {
        if ($standard === null
            || (strpos($path, '/Standards/') === false
            || verifyPath($path, $whitelist) === true)
        ) {
            $path = ltrim($path, '/');
            $phar->addFile(dirname(dirname(__FILE__)).'/'.$path, $path);
            $phar[$path]->compress(Phar::GZ);
        }
    }//end if

    if ($nodeName === 'dir') {
        // Descend into the depths.
        $path     = rtrim($path, '/').'/';
        $children = $node->childNodes;
        $childLn  = $children->length;
        for ($c = 0; $c < $childLn; $c++) {
            $child = $children->item($c);
            buildFromNode($phar, $child, $roles, $path, $standard, $whitelist);
        }
    }

}//end buildFromNode()


/**
 * Verify a path.
 *
 * @param string $path      The path to verify.
 * @param array  $whitelist The whitelist.
 *
 * @return boolean
 */
function verifyPath($path, $whitelist=array())
{
    // Return true, if the path is in the whitelist.
    $verified = false;
    foreach ($whitelist as $file) {
        if (strpos($file, $path) !== false) {
            $verified = true;
            break;
        }
    }

    return $verified;

}//end verifyPath()


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
            $path = ltrim(str_replace($baseDir, '', $file->getPath().'/'.$file->getFileName()), '/');
            if (strpos($path, '/') === false) {
                // Remove top level files.
                if (strpos($path, '.phar') !== false
                    || (strpos($path, '.php') !== false && $path !== 'CodeSniffer.php')
                ) {
                    // Only CodeSniffer.php should exist in the top level.
                    // As well, we better not add any phar files either.
                    continue;
                }
            }

            // Compress.
            $phar->addFile($baseDir.'/'.$path, $path);
            $phar[$path]->compress(Phar::GZ);
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
    $className = str_replace(dirname(dirname(__FILE__)).'/CodeSniffer/Standards/', '', substr($file, 0, -4));
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
                    $intClassFile  = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/'.str_replace('_', '/', $interiorClass).'.php';
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
                    $intClassFile  = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/'.str_replace('_', '/', $interiorClass).'.php';
                    $whitelist[]   = $intClassFile;
                    findDependencies($intClassFile, $whitelist);
                }
            }
        }
    }

}//end findDependencies()


/**
 * Return a list a dependent ruleset files.
 *
 * @param string $standard The standard to use.
 *
 * @return array
 */
function findDependentRuleset($standard=null)
{
    $files       = array();
    if (PHP_CodeSniffer::isInstalledStandard($standard) === true) {
        $rulesetPath = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/'.$standard.'/ruleset.xml';
        $ruleset     = simplexml_load_string(file_get_contents($rulesetPath));
        if ($ruleset === false) {
            return $files;
        }

        foreach ($ruleset->rule as $rule) {
            if (isset($rule['ref']) === false) {
                continue;
            }

            if (PHP_CodeSniffer::isInstalledStandard($rule['ref']) === true) {
                $files[] = dirname(dirname(__FILE__)).'/CodeSniffer/Standards/'.$rule['ref'].'/ruleset.xml';

                // Recursive.
                $files = array_merge($files, findDependentRuleset($rule['ref']));
            }
        }
    }//end if

    return $files;

}//end findDependentRuleset()


/**
 * Add the stub file to the phar.
 *
 * @param object &$phar The phar class.
 *
 * @return void
 */
function addStub(&$phar)
{
    $stub  = '#!/usr/bin/env php'."\n";
    $stub .= '<?php error_reporting(E_ALL | E_STRICT);';
    $stub .= '@include_once "PHP/Timer.php";';
    $stub .= 'if (class_exists("PHP_Timer", false) === true) {';
    $stub .= '    PHP_Timer::start();';
    $stub .= '}';
    $stub .= 'include_once "phar://".__FILE__."/CodeSniffer/CLI.php";';
    $stub .= 'include_once "phar://".__FILE__."/CodeSniffer.php";';
    $stub .= '$config = PHP_CodeSniffer::getAllConfigData();';
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

