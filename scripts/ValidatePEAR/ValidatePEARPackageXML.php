<?php
/**
 * Validate the PHP_CodeSniffer PEAR package.xml file.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

use PHP_CodeSniffer\Tests\FileList;

/**
 * Validate the PHP_CodeSniffer PEAR package.xml file.
 */
class ValidatePEARPackageXML
{

    /**
     * The root directory of the project.
     *
     * @var string
     */
    protected $projectRoot = '';

    /**
     * The contents of the package.xml file.
     *
     * @var \SimpleXMLElement
     */
    protected $packageXML;

    /**
     * List of all files in the repo.
     *
     * @var array
     */
    protected $allFiles = [];

    /**
     * Valid file roles.
     *
     * @var array
     *
     * @link https://pear.php.net/manual/en/developers.packagedef.intro.php#developers.packagedef.roles
     */
    private $validRoles = [
        'data'   => true,
        'doc'    => true,
        'ext'    => true,
        'extsrc' => true,
        'php'    => true,
        'script' => true,
        'src'    => true,
        'test'   => true,
    ];

    /**
     * Files encountered in the package.xml <contents> tag.
     *
     * @var array
     */
    private $listedContents = [];


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->projectRoot = dirname(dirname(__DIR__)).'/';
        $this->packageXML  = simplexml_load_file($this->projectRoot.'package.xml');

        $allFiles       = (new FileList($this->projectRoot, $this->projectRoot))->getList();
        $this->allFiles = array_flip($allFiles);

    }//end __construct()


    /**
     * Validate the file listings in the package.xml file.
     *
     * @return void
     */
    public function validate()
    {
        $exitCode = 0;
        if ($this->checkContents() !== true) {
            $exitCode = 1;
        }

        if ($this->checkPHPRelease() !== true) {
            $exitCode = 1;
        }

        exit($exitCode);

    }//end validate()


    /**
     * Validate the file listings in the <contents> tag.
     *
     * @return bool
     */
    protected function checkContents()
    {
        echo PHP_EOL.'Checking Contents tag'.PHP_EOL;
        echo '====================='.PHP_EOL;

        $valid = true;

        /*
         * - Check that every file that is mentioned in the `<content>` tag exists in the repo.
         * - Check that the "role" value is valid.
         * - Check that the "baseinstalldir" value is valid.
         */

        $valid = $this->walkDirTag($this->packageXML->contents);
        if ($valid === true) {
            echo "Existing listings in the Contents tag are valid.".PHP_EOL;
        }

        /*
         * Verify that all files in the `src` and the `tests` directories are listed in the `<contents>` tag.
         */

        $srcFiles   = (new FileList(
            $this->projectRoot.'src/',
            $this->projectRoot,
            '`\.(css|fixed|inc|js|php|xml)$`Di'
        ))->getList();
        $testsFiles = (new FileList(
            $this->projectRoot.'tests/',
            $this->projectRoot,
            '`\.(css|inc|js|php|xml)$`Di'
        ))->getList();
        $files      = array_merge($srcFiles, $testsFiles);

        foreach ($files as $file) {
            if (isset($this->listedContents[$file]) === true) {
                continue;
            }

            echo "- File '{$file}' is missing from Contents tag.".PHP_EOL;
            $valid = false;
        }

        if ($valid === true) {
            echo "No missing files in the Contents tag.".PHP_EOL;
        }

        return $valid;

    }//end checkContents()


    /**
     * Validate all child tags within a <dir> tag.
     *
     * @param \SimpleXMLElement $tag              The current XML tag to examine.
     * @param string            $currentDirectory The complete relative path to the
     *                                            directory being examined.
     *
     * @return bool
     */
    protected function walkDirTag($tag, $currentDirectory='')
    {
        $valid = true;
        $name  = (string) $tag['name'];
        if ($name !== '/' && empty($name) === false) {
            $currentDirectory .= $name.'/';
        }

        $children = $tag->children();
        foreach ($children as $key => $value) {
            if ($key === 'dir') {
                if ($this->walkDirTag($value, $currentDirectory) === false) {
                    $valid = false;
                }
            }

            if ($key === 'file') {
                if ($this->checkFileTag($value, $currentDirectory) === false) {
                    $valid = false;
                }
            }
        }

        return $valid;

    }//end walkDirTag()


    /**
     * Validate the information within a <file> tag.
     *
     * @param \SimpleXMLElement $tag              The current XML tag to examine.
     * @param string            $currentDirectory The complete relative path to the
     *                                            directory being examined.
     *
     * @return bool
     */
    protected function checkFileTag($tag, $currentDirectory='')
    {
        $valid          = true;
        $attributes     = $tag->attributes();
        $baseinstalldir = (string) $attributes['baseinstalldir'];
        $name           = $currentDirectory.(string) $attributes['name'];
        $role           = (string) $attributes['role'];

        $this->listedContents[$name] = true;

        if (empty($name) === true) {
            echo "- Name attribute missing.".PHP_EOL;
            $valid = false;
        } else {
            if (isset($this->allFiles[$name]) === false) {
                echo "- File '{$name}' does not exist.".PHP_EOL;
                $valid = false;
            }

            if (empty($role) === true) {
                echo "- Role attribute missing for file '{$name}'.".PHP_EOL;
                $valid = false;
            } else {
                if (isset($this->validRoles[$role]) === false) {
                    echo "- Role for file '{$name}' is invalid.".PHP_EOL;
                    $valid = false;
                } else {
                    // Limited validation of the "role" tags.
                    if (strpos($name, 'Test.') !== false && $role !== 'test') {
                        echo "- Test files should have the role 'test'. Found: '$role' for file '{$name}'.".PHP_EOL;
                        $valid = false;
                    } else if ((strpos($name, 'Standard.xml') !== false || strpos($name, 'Sniff.php') !== false)
                        && $role !== 'php'
                    ) {
                        echo "- Sniff files, including sniff documentation files should have the role 'php'. Found: '$role' for file '{$name}'.".PHP_EOL;
                        $valid = false;
                    }
                }

                if (empty($baseinstalldir) === true) {
                    if ($role !== 'script' && strpos($name, 'tests/') !== 0) {
                        echo "- Baseinstalldir attribute missing for file '{$name}'.".PHP_EOL;
                        $valid = false;
                    }
                } else {
                    if ($role === 'script' ||  strpos($name, 'tests/') === 0) {
                        echo "- Baseinstalldir for file '{$name}' should be empty.".PHP_EOL;
                        $valid = false;
                    }

                    if ($role !== 'script' && $baseinstalldir !== 'PHP/CodeSniffer') {
                        echo "- Baseinstalldir for file '{$name}' is invalid.".PHP_EOL;
                        $valid = false;
                    }
                }
            }//end if
        }//end if

        return $valid;

    }//end checkFileTag()


    /**
     * Validate the file listings in the <phprelease> tags.
     *
     * @return bool True if the info in the "phprelease" tags is valid. False otherwise.
     */
    protected function checkPHPRelease()
    {
        echo PHP_EOL.'Checking PHPRelease tags'.PHP_EOL;
        echo '========================'.PHP_EOL;

        $valid       = true;
        $listedFiles = [];
        $releaseTags = 1;

        /*
         * - Check that every file that is mentioned in the `<phprelease>` tags exists in the repo.
         * - Check that the "as" value is valid.
         */

        foreach ($this->packageXML->phprelease as $release) {
            foreach ($release->filelist->install as $install) {
                $attributes = $install->attributes();
                $name       = (string) $attributes['name'];
                $as         = (string) $attributes['as'];

                $listedFiles[$releaseTags][$name] = $as;

                if (empty($as) === true || empty($name) === true) {
                    continue;
                }

                if (isset($this->allFiles[$name]) === false) {
                    echo "- File '{$name}' does not exist.".PHP_EOL;
                    $valid = false;
                }

                // Rest of the checks only apply to the test files.
                if (strpos($name, 'tests/') !== 0) {
                    continue;
                }

                // Check validity of the tags for files in the tests root directory.
                if (preg_match('`^tests/([^/]+\.php)$`', $name, $matches) === 1
                    && ($as === $name || $as === $matches[1])
                ) {
                    continue;
                }

                // Check validity of the tags for files in the tests root subdirectories.
                if (preg_match('`^tests/.+\.(php|inc|js|css|xml)$`', $name) === 1
                    && $as === str_replace('tests/', 'CodeSniffer/', $name)
                ) {
                    continue;
                }

                echo "- Invalid 'as' attribute '{$as}' for test file '{$name}'.".PHP_EOL;
                $valid = false;
            }//end foreach

            ++$releaseTags;
        }//end foreach

        if ($valid === true) {
            echo "Existing PHPRelease tags are valid.".PHP_EOL;
        }

        /*
         * Verify that all files in the `tests` directory are listed in both `<phprelease>` tags.
         */

        $testFiles = (new FileList($this->projectRoot.'tests/', $this->projectRoot, '`\.(inc|php|js|css|xml)$`Di'))->getList();

        foreach ($testFiles as $file) {
            foreach ($listedFiles as $key => $listed) {
                if (isset($listed[$file]) === true) {
                    continue;
                }

                echo "- File '{$file}' is missing from PHPRelease tag [{$key}] .".PHP_EOL;
                $valid = false;
            }
        }

        if ($valid === true) {
            echo "No missing PHPRelease tags.".PHP_EOL;
        }

        return $valid;

    }//end checkPHPRelease()


}//end class
