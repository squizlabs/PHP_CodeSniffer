<?php
/**
 * Check that each sniff is complete, i.e. has unit tests and documentation.
 *
 * This script should be run from the root of a PHPCS standards repo and can
 * be used by external standards as well.
 *
 * Configuration options:
 * -q: quiet, don't show warnings about missing documentation files.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

/**
 * Check that each sniff is complete, i.e. has unit tests and documentation.
 */
class CheckSniffCompleteness
{

    /**
     * The root directory of the project.
     *
     * @var string
     */
    protected $projectRoot = '';

    /**
     * List of all files in the repo.
     *
     * @var array
     */
    protected $allFiles = [];

    /**
     * List of all sniff files in the repo.
     *
     * @var array
     */
    protected $allSniffs = [];

    /**
     * Whether to use "quiet" mode.
     *
     * This will silence all warnings, but still show the errors.
     *
     * To enable "quiet" mode, pass `-q` on the command line when calling
     * the `check-sniff-completeness` script.
     *
     * @var boolean
     */
    private $quietMode = false;

    /**
     * Search & replace values to convert a sniff file path into a docs file path.
     *
     * Keys are the strings to search for, values the replacement values.
     *
     * @var array
     */
    private $sniffToDoc = [
        '/Sniffs/'  => '/Docs/',
        'Sniff.php' => 'Standard.xml',
    ];

    /**
     * Search & replace values to convert a sniff file path into a unit test file path.
     *
     * Keys are the strings to search for, values the replacement values.
     *
     * @var array
     */
    private $sniffToUnitTest = [
        '/Sniffs/' => '/Tests/',
        'Sniff.'   => 'UnitTest.',
    ];

    /**
     * Possible test case file extensions.
     *
     * @var array
     */
    private $testCaseExtensions = [
        '.inc',
        '.css',
        '.js',
        '.1.inc',
        '.1.css',
        '.1.js',
    ];


    /**
     * Constructor.
     */
    public function __construct()
    {
        $args = $_SERVER['argv'];
        if (isset($args[1]) === true && $args[1] === '-q') {
            $this->quietMode = true;
        }

        $this->projectRoot = getcwd();

        $allFiles = (new FileList($this->projectRoot, $this->projectRoot))->getList();
        sort($allFiles, SORT_NATURAL);
        $this->allFiles = array_flip($allFiles);

        $allSniffs = (new FileList(
            $this->projectRoot,
            $this->projectRoot,
            '`/Sniffs/(?!Abstract).+Sniff\.php$`Di'
        ))->getList();

        sort($allSniffs, SORT_NATURAL);
        $this->allSniffs = $allSniffs;

    }//end __construct()


    /**
     * Validate the completeness of the sniffs in the repository.
     *
     * @return void
     */
    public function validate()
    {
        if ($this->isComplete() !== true) {
            exit(1);
        }

        exit(0);

    }//end validate()


    /**
     * Verify if all files needed for a sniff to be considered complete are available.
     *
     * @return void
     */
    public function isComplete()
    {
        $valid = true;
        foreach ($this->allSniffs as $file) {
            if ($this->quietMode === false) {
                $docFile = str_replace(array_keys($this->sniffToDoc), $this->sniffToDoc, $file);
                if (isset($this->allFiles[$docFile]) === false) {
                    echo "WARNING: Documentation missing for {$file}.".PHP_EOL;
                    $valid = false;
                }
            }

            $testFile = str_replace(array_keys($this->sniffToUnitTest), $this->sniffToUnitTest, $file);
            if (isset($this->allFiles[$testFile]) === false) {
                echo "ERROR: Unit tests missing for {$file}.".PHP_EOL;
                $valid = false;
            } else {
                $fileFound = false;
                foreach ($this->testCaseExtensions as $extension) {
                    $testCaseFile = str_replace('.php', $extension, $testFile);
                    if (isset($this->allFiles[$testCaseFile]) === true) {
                        $fileFound = true;
                        break;
                    }
                }

                if ($fileFound === false) {
                    echo "ERROR: Unit test case file missing for {$file}.".PHP_EOL;
                    $valid = false;
                }
            }//end if
        }//end foreach

        if ($valid === true) {
            if ($this->quietMode === false) {
                echo 'All sniffs are accompanied by unit tests and documentation.'.PHP_EOL;
            } else {
                echo 'All sniffs are accompanied by unit tests.'.PHP_EOL;
            }
        }

        return $valid;

    }//end isComplete()


}//end class
