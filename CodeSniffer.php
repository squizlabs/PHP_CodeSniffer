<?php
/**
 * PHP_CodeSniffer tokenises PHP code and detects violations of a
 * defined set of coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

spl_autoload_register(array('PHP_CodeSniffer', 'autoload'));

if (class_exists('PHP_CodeSniffer_Exception', true) === false) {
    throw new Exception('Class PHP_CodeSniffer_Exception not found');
}

if (class_exists('PHP_CodeSniffer_File', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_File not found');
}

if (class_exists('PHP_CodeSniffer_Tokens', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Tokens not found');
}

if (interface_exists('PHP_CodeSniffer_Sniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Interface PHP_CodeSniffer_Sniff not found');
}

if (interface_exists('PHP_CodeSniffer_MultiFileSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Interface PHP_CodeSniffer_MultiFileSniff not found');
}

/**
 * PHP_CodeSniffer tokenises PHP code and detects violations of a
 * defined set of coding standards.
 *
 * Standards are specified by classes that implement the PHP_CodeSniffer_Sniff
 * interface. A sniff registers what token types it wishes to listen for, then
 * PHP_CodeSniffer encounters that token, the sniff is invoked and passed
 * information about where the token was found in the stack, and the token stack
 * itself.
 *
 * Sniff files and their containing class must be prefixed with Sniff, and
 * have an extension of .php.
 *
 * Multiple PHP_CodeSniffer operations can be performed by re-calling the
 * process function with different parameters.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer
{

    /**
     * The file or directory that is currently being processed.
     *
     * @var string
     */
    protected $file = array();

    /**
     * The directory to search for sniffs in.
     *
     * @var string
     */
    protected $standardDir = '';

    /**
     * The files that have been processed.
     *
     * @var array(PHP_CodeSniffer_File)
     */
    protected $files = array();

    /**
     * The CLI object controlling the run.
     *
     * @var string
     */
    protected $cli = null;

    /**
     * The path that that PHP_CodeSniffer is being run from.
     *
     * Stored so that the path can be restored after it is changed
     * in the constructor.
     *
     * @var string
     */
    private $_cwd = null;

    /**
     * The listeners array.
     *
     * @var array(PHP_CodeSniffer_Sniff)
     */
    protected $listeners = array();

    /**
     * The listeners array, indexed by token type.
     *
     * @var array()
     */
    private $_tokenListeners = array(
                                'file'      => array(),
                                'multifile' => array(),
                               );

    /**
     * An array of patterns to use for skipping files.
     *
     * @var array()
     */
    protected $ignorePatterns = array();

    /**
     * An array of extensions for files we will check.
     *
     * @var array
     */
    public $allowedFileExtensions = array(
                                     'php' => 'PHP',
                                     'inc' => 'PHP',
                                     'js'  => 'JS',
                                     'css' => 'CSS',
                                    );

    /**
     * An array of variable types for param/var we will check.
     *
     * @var array(string)
     */
    public static $allowedTypes = array(
                                   'array',
                                   'boolean',
                                   'float',
                                   'integer',
                                   'mixed',
                                   'object',
                                   'string',
                                  );


    /**
     * Constructs a PHP_CodeSniffer object.
     *
     * @param int  $verbosity   The verbosity level.
     *                          1: Print progress information.
     *                          2: Print developer debug information.
     * @param int  $tabWidth    The number of spaces each tab represents.
     *                          If greater than zero, tabs will be replaced
     *                          by spaces before testing each file.
     * @param bool $interactive If TRUE, will stop after each file with errors
     *                          and wait for user input.
     *
     * @see process()
     */
    public function __construct($verbosity=0, $tabWidth=0, $interactive=false)
    {
        if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
            define('PHP_CODESNIFFER_VERBOSITY', $verbosity);
        }

        if (defined('PHP_CODESNIFFER_TAB_WIDTH') === false) {
            define('PHP_CODESNIFFER_TAB_WIDTH', $tabWidth);
        }

        if (defined('PHP_CODESNIFFER_INTERACTIVE') === false) {
            define('PHP_CODESNIFFER_INTERACTIVE', $interactive);
        }

        // Change into a directory that we know about to stop any
        // relative path conflicts.
        $this->_cwd = getcwd();
        chdir(dirname(__FILE__).'/CodeSniffer/');

    }//end __construct()


    /**
     * Destructs a PHP_CodeSniffer object.
     *
     * Restores the current working directory to what it
     * was before we started our run.
     *
     * @return void
     */
    public function __destruct()
    {
        chdir($this->_cwd);

    }//end __destruct()


    /**
     * Autoload static method for loading classes and interfaces.
     *
     * @param string $className The name of the class or interface.
     *
     * @return void
     */
    public static function autoload($className)
    {
        if (substr($className, 0, 4) === 'PHP_') {
            $newClassName = substr($className, 4);
        } else {
            $newClassName = $className;
        }

        $path = str_replace('_', '/', $newClassName).'.php';

        if (is_file(dirname(__FILE__).'/'.$path) === true) {
            // Check standard file locations based on class name.
            include dirname(__FILE__).'/'.$path;
        } else if (is_file(dirname(__FILE__).'/CodeSniffer/Standards/'.$path) === true) {
            // Check for included sniffs.
            include dirname(__FILE__).'/CodeSniffer/Standards/'.$path;
        } else {
            // Everything else.
            @include $path;
        }

    }//end autoload()


    /**
     * Sets an array of file extensions that we will allow checking of.
     *
     * If the extension is one of the defaults, a specific tokenizer
     * will be used. Otherwise, the PHP tokenizer will be used for
     * all extensions passed.
     *
     * @param array $extensions An array of file extensions.
     *
     * @return void
     */
    public function setAllowedFileExtensions(array $extensions)
    {
        $newExtensions = array();
        foreach ($extensions as $ext) {
            if (isset($this->allowedFileExtensions[$ext]) === true) {
                $newExtensions[$ext] = $this->allowedFileExtensions[$ext];
            } else {
                $newExtensions[$ext] = 'PHP';
            }
        }

        $this->allowedFileExtensions = $newExtensions;

    }//end setAllowedFileExtensions()


    /**
     * Sets an array of ignore patterns that we use to skip files and folders.
     *
     * Patterns are not case sensitive.
     *
     * @param array $patterns An array of ignore patterns.
     *
     * @return void
     */
    public function setIgnorePatterns(array $patterns)
    {
        $this->ignorePatterns = $patterns;

    }//end setIgnorePatterns()


    /**
     * Sets the internal CLI object.
     *
     * @param object $cli The CLI object controlling the run.
     *
     * @return void
     */
    public function setCli($cli)
    {
        $this->cli = $cli;

    }//end setCli()


    /**
     * Adds a file to the list of checked files.
     *
     * Checked files are used to generate error reports after the run.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file to add.
     *
     * @return void
     */
    public function addFile(PHP_CodeSniffer_File $phpcsFile)
    {
        $this->files[] = $phpcsFile;

    }//end addFile()


    /**
     * Processes the files/directories that PHP_CodeSniffer was constructed with.
     *
     * @param string|array $files    The files and directories to process. For
     *                               directories, each sub directory will also
     *                               be traversed for source files.
     * @param string       $standard The set of code sniffs we are testing
     *                               against.
     * @param array        $sniffs   The sniff names to restrict the allowed
     *                               listeners to.
     * @param boolean      $local    If true, don't recurse into directories.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If files or standard are invalid.
     */
    public function process($files, $standard, array $sniffs=array(), $local=false)
    {
        if (is_array($files) === false) {
            if (is_string($files) === false || $files === null) {
                throw new PHP_CodeSniffer_Exception('$file must be a string');
            }

            $files = array($files);
        }

        if (is_string($standard) === false || $standard === null) {
            throw new PHP_CodeSniffer_Exception('$standard must be a string');
        }

        // Reset the members.
        $this->listeners       = array();
        $this->files           = array();
        $this->_tokenListeners = array(
                                  'file'      => array(),
                                  'multifile' => array(),
                                 );

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo "Registering sniffs in ".basename($standard)." standard... ";
            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                echo PHP_EOL;
            }
        }

        $this->setTokenListeners($standard, $sniffs);
        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $numSniffs = count($this->listeners);
            echo "DONE ($numSniffs sniffs registered)".PHP_EOL;
        }

        $this->populateTokenListeners();

        // The SVN pre-commit calls process() to init the sniffs
        // so there may not be any files to process.
        if (empty($files) === true) {
            return;
        }

        foreach ($files as $file) {
            $this->file = $file;
            if (is_dir($this->file) === true) {
                $this->processFiles($this->file, $local);
            } else {
                $this->processFile($this->file);
            }
        }

        // Now process the multi-file sniffs, assuming there are
        // multiple files being sniffed.
        if (count($files) > 1 || is_dir($files[0]) === true) {
            foreach ($this->_tokenListeners['multifile'] as $listener) {
                // Set the name of the listener for error messages.
                $activeListener = get_class($listener);
                foreach ($this->files as $file) {
                    $file->setActiveListener($activeListener);
                }

                $listener->process($this->files);
            }
        }

    }//end process()


    /**
     * Gets installed sniffs in the coding standard being used.
     *
     * Traverses the standard directory for classes that implement the
     * PHP_CodeSniffer_Sniff interface asks them to register. Each of the
     * sniff's class names must be exact as the basename of the sniff file.
     *
     * Returns an array of sniff class names.
     *
     * @param string $standard The name of the coding standard we are checking.
     * @param array  $sniffs   The sniff names to restrict the allowed
     *                         listeners to.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If any of the tests failed in the
     *                                   registration process.
     */
    public function getTokenListeners($standard, array $sniffs=array())
    {
        if (is_dir($standard) === true) {
            // This is an absolute path to a custom standard.
            $this->standardDir = $standard;
            $standard          = basename($standard);
        } else {
            $this->standardDir = realpath(dirname(__FILE__).'/CodeSniffer/Standards/'.$standard);
            if (is_dir($this->standardDir) === false) {
                // This isn't looking good. Let's see if this
                // is a relative path to a custom standard.
                if (is_dir(realpath($this->_cwd.'/'.$standard)) === true) {
                    // This is a relative path to a custom standard.
                    $this->standardDir = realpath($this->_cwd.'/'.$standard);
                    $standard          = basename($standard);
                }
            }
        }

        $files = self::getSniffFiles($this->standardDir, $standard);

        if (empty($sniffs) === false) {
            // Convert the allowed sniffs to lower case so
            // they are easier to check.
            foreach ($sniffs as &$sniff) {
                $sniff = strtolower($sniff);
            }
        }

        $listeners = array();

        foreach ($files as $file) {
            // Work out where the position of /StandardName/Sniffs/... is
            // so we can determine what the class will be called.
            $sniffPos = strrpos($file, DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR);
            if ($sniffPos === false) {
                continue;
            }

            $slashPos = strrpos(substr($file, 0, $sniffPos), DIRECTORY_SEPARATOR);
            if ($slashPos === false) {
                continue;
            }

            $className = substr($file, ($slashPos + 1));
            $className = substr($className, 0, -4);
            $className = str_replace(DIRECTORY_SEPARATOR, '_', $className);

            include_once $file;

            // If they have specified a list of sniffs to restrict to, check
            // to see if this sniff is allowed.
            $allowed = in_array(strtolower($className), $sniffs);
            if (empty($sniffs) === false && $allowed === false) {
                continue;
            }

            $listeners[] = $className;

            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                echo "\tRegistered $className".PHP_EOL;
            }
        }//end foreach

        return $listeners;

    }//end getTokenListeners()


    /**
     * Sets installed sniffs in the coding standard being used.
     *
     * @param string $standard The name of the coding standard we are checking.
     * @param array  $sniffs   The sniff names to restrict the allowed
     *                         listeners to.
     *
     * @return null
     */
    public function setTokenListeners($standard, array $sniffs=array())
    {
        $this->listeners = $this->getTokenListeners($standard, $sniffs);

    }//end setTokenListeners()


    /**
     * Populates the array of PHP_CodeSniffer_Sniff's for this file.
     *
     * @return void
     */
    public function populateTokenListeners()
    {
        // Construct a list of listeners indexed by token being listened for.
        $this->_tokenListeners = array(
                                  'file'      => array(),
                                  'multifile' => array(),
                                 );

        foreach ($this->listeners as $listenerClass) {
            $listener = new $listenerClass();

            if (($listener instanceof PHP_CodeSniffer_Sniff) === true) {
                $tokens = $listener->register();
                if (is_array($tokens) === false) {
                    $msg = "Sniff $listenerClass register() method must return an array";
                    throw new PHP_CodeSniffer_Exception($msg);
                }

                foreach ($tokens as $token) {
                    if (isset($this->_tokenListeners['file'][$token]) === false) {
                        $this->_tokenListeners['file'][$token] = array();
                    }

                    if (in_array($listener, $this->_tokenListeners['file'][$token], true) === false) {
                        $this->_tokenListeners['file'][$token][] = $listener;
                    }
                }
            } else if (($listener instanceof PHP_CodeSniffer_MultiFileSniff) === true) {
                $this->_tokenListeners['multifile'][] = $listener;
            }
        }//end foreach

    }//end populateTokenListeners()


    /**
     * Return a list of sniffs that a coding standard has defined.
     *
     * Sniffs are found by recursing the standard directory and also by
     * asking the standard for included sniffs.
     *
     * @param string $dir      The directory where to look for the files.
     * @param string $standard The name of the coding standard. If NULL, no
     *                         included sniffs will be checked for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If an included or excluded sniff does
     *                                   not exist.
     */
    public static function getSniffFiles($dir, $standard=null)
    {
        $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        $ownSniffs      = array();
        $includedSniffs = array();
        $excludedSniffs = array();

        foreach ($di as $file) {
            $fileName = $file->getFilename();

            // Skip hidden files.
            if (substr($fileName, 0, 1) === '.') {
                continue;
            }

            // We are only interested in PHP and sniff files.
            $fileParts = explode('.', $fileName);
            if (array_pop($fileParts) !== 'php') {
                continue;
            }

            $basename = basename($fileName, '.php');
            if (substr($basename, -5) !== 'Sniff') {
                continue;
            }

            $ownSniffs[] = $file->getPathname();
        }//end foreach

        // Load the standard class and ask it for a list of external
        // sniffs to include in the standard.
        if ($standard !== null
            && is_file("$dir/{$standard}CodingStandard.php") === true
        ) {
            include_once "$dir/{$standard}CodingStandard.php";
            $standardClassName = "PHP_CodeSniffer_Standards_{$standard}_{$standard}CodingStandard";
            $standardClass     = new $standardClassName;

            $included = $standardClass->getIncludedSniffs();
            foreach ($included as $sniff) {
                if (is_dir($sniff) === true) {
                    // Trying to include from a custom standard.
                    $sniffDir = $sniff;
                    $sniff    = basename($sniff);
                } else if (is_file($sniff) === true) {
                    // Trying to include a custom sniff.
                    $sniffDir = $sniff;
                } else {
                    $sniffDir = realpath(dirname(__FILE__)."/CodeSniffer/Standards/$sniff");
                    if ($sniffDir === false) {
                        $error = "Included sniff $sniff does not exist";
                        throw new PHP_CodeSniffer_Exception($error);
                    }
                }

                if (is_dir($sniffDir) === true) {
                    if (self::isInstalledStandard($sniff) === true) {
                        // We are including a whole coding standard.
                        $includedSniffs = array_merge($includedSniffs, self::getSniffFiles($sniffDir, $sniff));
                    } else {
                        // We are including a whole directory of sniffs.
                        $includedSniffs = array_merge($includedSniffs, self::getSniffFiles($sniffDir));
                    }

                    // Remove sniff copies.
                    $includedSniffs = array_unique($includedSniffs);
                } else {
                    if (substr($sniffDir, -9) !== 'Sniff.php') {
                        $error = "Included sniff $sniff does not exist";
                        throw new PHP_CodeSniffer_Exception($error);
                    }

                    $includedSniffs[] = $sniffDir;
                }
            }//end foreach

            $excluded = $standardClass->getExcludedSniffs();
            foreach ($excluded as $sniff) {
                if (is_dir($sniff) === true) {
                    // Trying to exclude from a custom standard.
                    $sniffDir = $sniff;
                    $sniff    = basename($sniff);
                } else if (is_file($sniff) === true) {
                    // Trying to exclude a custom sniff.
                    $sniffDir = $sniff;
                } else {
                    $sniffDir = realpath(dirname(__FILE__)."/CodeSniffer/Standards/$sniff");
                    if ($sniffDir === false) {
                        $error = "Excluded sniff $sniff does not exist";
                        throw new PHP_CodeSniffer_Exception($error);
                    }
                }

                if (is_dir($sniffDir) === true) {
                    if (self::isInstalledStandard($sniff) === true) {
                        // We are excluding a whole coding standard.
                        $excludedSniffs = array_merge(
                            $excludedSniffs,
                            self::getSniffFiles($sniffDir, $sniff)
                        );
                    } else {
                        // We are excluding a whole directory of sniffs.
                        $excludedSniffs = array_merge(
                            $excludedSniffs,
                            self::getSniffFiles($sniffDir)
                        );
                    }
                } else {
                    if (substr($sniffDir, -9) !== 'Sniff.php') {
                        $error = "Excluded sniff $sniff does not exist";
                        throw new PHP_CodeSniffer_Exception($error);
                    }

                    $excludedSniffs[] = $sniffDir;
                }
            }//end foreach
        }//end if

        // Merge our own sniff list with our externally included
        // sniff list, but filter out any excluded sniffs.
        $files = array();
        foreach (array_merge($ownSniffs, $includedSniffs) as $sniff) {
            if (in_array($sniff, $excludedSniffs) === true) {
                continue;
            } else {
                $files[] = $sniff;
            }
        }

        return $files;

    }//end getSniffFiles()


    /**
     * Run the code sniffs over each file in a given directory.
     *
     * Recusively reads the specified directory and performs the PHP_CodeSniffer
     * sniffs on each source file found within the directories.
     *
     * @param string  $dir   The directory to process.
     * @param boolean $local If true, only process files in this directory, not
     *                       sub directories.
     *
     * @return void
     * @throws Exception If there was an error opening the directory.
     */
    public function processFiles($dir, $local=false)
    {
        try {
            if ($local === true) {
                $di = new DirectoryIterator($dir);
            } else {
                $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            }

            foreach ($di as $file) {
                $filePath = realpath($file->getPathname());
                if ($filePath === false) {
                    continue;
                }

                if (is_dir($filePath) === true) {
                    continue;
                }

                // Check that the file's extension is one we are checking.
                // Note that because we are doing a whole directory, we
                // are strict about checking the extension and we don't
                // let files with no extension through.
                $fileParts = explode('.', $file);
                $extension = array_pop($fileParts);
                if ($extension === $file) {
                    continue;
                }

                if (isset($this->allowedFileExtensions[$extension]) === false) {
                    continue;
                }

                $this->processFile($filePath);
            }//end foreach
        } catch (Exception $e) {
            $trace = $e->getTrace();

            $filename = $trace[0]['args'][0];
            if (is_numeric($filename) === true) {
                // See if we can find the PHP_CodeSniffer_File object.
                foreach ($trace as $data) {
                    if (isset($data['args'][0]) === true && ($data['args'][0] instanceof PHP_CodeSniffer_File) === true) {
                        $filename = $data['args'][0]->getFilename();
                    }
                }
            }

            $error = 'An error occurred during processing; checking has been aborted. The error message was: '.$e->getMessage();

            $phpcsFile = new PHP_CodeSniffer_File($filename, $this->listeners, $this->allowedFileExtensions);
            $this->addFile($phpcsFile);
            $phpcsFile->addError($error, null);
            return;
        }

    }//end processFiles()


    /**
     * Run the code sniffs over a single given file.
     *
     * Processes the file and runs the PHP_CodeSniffer sniffs to verify that it
     * conforms with the standard.
     *
     * @param string $file     The file to process.
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If the file could not be processed.
     * @see    _processFile()
     */
    public function processFile($file, $contents=null)
    {
        if ($contents === null && file_exists($file) === false) {
            throw new PHP_CodeSniffer_Exception("Source file $file does not exist");
        }

        // If the file's path matches one of our ignore patterns, skip it.
        foreach ($this->ignorePatterns as $pattern) {
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            $pattern = strtr($pattern, $replacements);
            if (preg_match("|{$pattern}|i", $file) === 1) {
                return;
            }
        }

        // Before we go and spend time tokenizing this file, just check
        // to see if there is a tag up top to indicate that the whole
        // file should be ignored. It must be on one of the first two lines.
        $firstContent = $contents;
        if ($contents === null) {
            $handle = fopen($file, 'r');
            if ($handle !== false) {
                $firstContent  = fgets($handle);
                $firstContent .= fgets($handle);
                fclose($handle);
            }
        }

        if (strpos($firstContent, '@codingStandardsIgnoreFile') !== false) {
            // We are ignoring the whole file.
            if (PHP_CODESNIFFER_VERBOSITY > 0) {
                echo 'Ignoring '.basename($file).PHP_EOL;
            }

            return;
        }

        $this->_processFile($file, $contents);

        if (PHP_CODESNIFFER_INTERACTIVE === false) {
            return;
        }

        /*
            Running interactively.
            Print the error report for the current file and then wait for user input.
        */

        $reporting = new PHP_CodeSniffer_Reporting();
        $cliValues = $this->cli->getCommandLineValues();

        // Get current violations and then clear the list to make sure
        // we only print violations for a single file each time.
        $numErrors = null;
        while ($numErrors !== 0) {
            $filesViolations = $this->getFilesErrors();
            $this->files = array();

            $numErrors = $reporting->printReport(
                $cliValues['report'],
                $filesViolations,
                $cliValues['showWarnings'],
                $cliValues['showSources'],
                '',
                $cliValues['reportWidth']
            );

            if ($numErrors === 0) {
                continue;
            }

            echo '<ENTER> to recheck, [s] to skip or [q] to quit : ';
            $input = fgets(STDIN);
            $input = trim($input);

            switch ($input) {
            case 's':
                break;
            case 'q':
                exit(0);
                break;
            default:
                // Repopulate the sniffs because some of them save their state
                // and only clear it when the file changes, but we are rechecking
                // the same file.
                $this->populateTokenListeners();
                $this->_processFile($file, $contents);
                break;
            }
        }//end while

    }//end processFile()


    /**
     * Process the sniffs for a single file.
     *
     * Does raw processing only. No interactive support or error checking.
     *
     * @param string $file     The file to process.
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     * @see    processFile()
     */
    private function _processFile($file, $contents)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $startTime = time();
            echo 'Processing '.basename($file).' ';
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

        $phpcsFile = new PHP_CodeSniffer_File(
            $file,
            $this->_tokenListeners['file'],
            $this->allowedFileExtensions
        );
        $this->addFile($phpcsFile);
        $phpcsFile->start($contents);

        // Clean up the test if we can to save memory. This can't be done if
        // we need to leave the files around for multi-file sniffs.
        if (PHP_CODESNIFFER_INTERACTIVE === false
            && empty($this->_tokenListeners['multifile']) === true
        ) {
            $phpcsFile->cleanUp();
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $timeTaken = (time() - $startTime);
            if ($timeTaken === 0) {
                echo 'DONE in < 1 second';
            } else if ($timeTaken === 1) {
                echo 'DONE in 1 second';
            } else {
                echo "DONE in $timeTaken seconds";
            }

            $errors   = $phpcsFile->getErrorCount();
            $warnings = $phpcsFile->getWarningCount();
            echo " ($errors errors, $warnings warnings)".PHP_EOL;
        }

    }//end _processFile()


    /**
     * Gives collected violations for reports.
     *
     * @return array
     */
    public function getFilesErrors()
    {
        $files = array();
        foreach ($this->files as $file) {
            $files[$file->getFilename()]
                = array(
                   'warnings'    => $file->getWarnings(),
                   'errors'      => $file->getErrors(),
                   'numWarnings' => $file->getWarningCount(),
                   'numErrors'   => $file->getErrorCount(),
                  );
        }

        return $files;

    }//end getFilesErrors()


    /**
     * Generates documentation for a coding standard.
     *
     * @param string $standard  The standard to generate docs for
     * @param array  $sniffs    A list of sniffs to limit the docs to.
     * @param string $generator The name of the generator class to use.
     *
     * @return void
     */
    public function generateDocs($standard, array $sniffs=array(), $generator='Text')
    {
        include_once 'PHP/CodeSniffer/DocGenerators/'.$generator.'.php';

        $class     = "PHP_CodeSniffer_DocGenerators_$generator";
        $generator = new $class($standard, $sniffs);

        $generator->generate();

    }//end generateDocs()


    /**
     * Returns the PHP_CodeSniffer file objects.
     *
     * @return array(PHP_CodeSniffer_File)
     */
    public function getFiles()
    {
        return $this->files;

    }//end getFiles()


    /**
     * Gets the array of PHP_CodeSniffer_Sniff's.
     *
     * @return array(PHP_CodeSniffer_Sniff)
     */
    public function getSniffs()
    {
        return $this->listeners;

    }//end getSniffs()


    /**
     * Gets the array of PHP_CodeSniffer_Sniff's indexed by token type.
     *
     * @return array()
     */
    public function getTokenSniffs()
    {
        return $this->_tokenListeners;

    }//end getTokenSniffs()


    /**
     * Takes a token produced from <code>token_get_all()</code> and produces a
     * more uniform token.
     *
     * Note that this method also resolves T_STRING tokens into more descrete
     * types, therefore there is no need to call resolveTstringToken()
     *
     * @param string|array $token The token to convert.
     *
     * @return array The new token.
     */
    public static function standardiseToken($token)
    {
        if (is_array($token) === false) {
            $newToken = self::resolveSimpleToken($token);
        } else {
            switch ($token[0]) {
            case T_STRING:
                // Some T_STRING tokens can be more specific.
                $newToken = self::resolveTstringToken($token);
                break;
            case T_CURLY_OPEN:
                $newToken            = array();
                $newToken['code']    = T_OPEN_CURLY_BRACKET;
                $newToken['content'] = $token[1];
                $newToken['type']    = 'T_OPEN_CURLY_BRACKET';
                break;
            default:
                $newToken            = array();
                $newToken['code']    = $token[0];
                $newToken['content'] = $token[1];
                $newToken['type']    = token_name($token[0]);
                break;
            }//end switch
        }//end if

        return $newToken;

    }//end standardiseToken()


    /**
     * Converts T_STRING tokens into more usable token names.
     *
     * The token should be produced using the token_get_all() function.
     * Currently, not all T_STRING tokens are converted.
     *
     * @param string|array $token The T_STRING token to convert as constructed
     *                            by token_get_all().
     *
     * @return array The new token.
     */
    public static function resolveTstringToken(array $token)
    {
        $newToken = array();
        switch (strtolower($token[1])) {
        case 'false':
            $newToken['type'] = 'T_FALSE';
            break;
        case 'true':
            $newToken['type'] = 'T_TRUE';
            break;
        case 'null':
            $newToken['type'] = 'T_NULL';
            break;
        case 'self':
            $newToken['type'] = 'T_SELF';
            break;
        case 'parent':
            $newToken['type'] = 'T_PARENT';
            break;
        default:
            $newToken['type'] = 'T_STRING';
            break;
        }

        $newToken['code']    = constant($newToken['type']);
        $newToken['content'] = $token[1];

        return $newToken;

    }//end resolveTstringToken()


    /**
     * Converts simple tokens into a format that conforms to complex tokens
     * produced by token_get_all().
     *
     * Simple tokens are tokens that are not in array form when produced from
     * token_get_all().
     *
     * @param string $token The simple token to convert.
     *
     * @return array The new token in array format.
     */
    public static function resolveSimpleToken($token)
    {
        $newToken = array();

        switch ($token) {
        case '{':
            $newToken['type'] = 'T_OPEN_CURLY_BRACKET';
            break;
        case '}':
            $newToken['type'] = 'T_CLOSE_CURLY_BRACKET';
            break;
        case '[':
            $newToken['type'] = 'T_OPEN_SQUARE_BRACKET';
            break;
        case ']':
            $newToken['type'] = 'T_CLOSE_SQUARE_BRACKET';
            break;
        case '(':
            $newToken['type'] = 'T_OPEN_PARENTHESIS';
            break;
        case ')':
            $newToken['type'] = 'T_CLOSE_PARENTHESIS';
            break;
        case ':':
            $newToken['type'] = 'T_COLON';
            break;
        case '.':
            $newToken['type'] = 'T_STRING_CONCAT';
            break;
        case '?':
            $newToken['type'] = 'T_INLINE_THEN';
            break;
        case ';':
            $newToken['type'] = 'T_SEMICOLON';
            break;
        case '=':
            $newToken['type'] = 'T_EQUAL';
            break;
        case '*':
            $newToken['type'] = 'T_MULTIPLY';
            break;
        case '/':
            $newToken['type'] = 'T_DIVIDE';
            break;
        case '+':
            $newToken['type'] = 'T_PLUS';
            break;
        case '-':
            $newToken['type'] = 'T_MINUS';
            break;
        case '%':
            $newToken['type'] = 'T_MODULUS';
            break;
        case '^':
            $newToken['type'] = 'T_POWER';
            break;
        case '&':
            $newToken['type'] = 'T_BITWISE_AND';
            break;
        case '|':
            $newToken['type'] = 'T_BITWISE_OR';
            break;
        case '<':
            $newToken['type'] = 'T_LESS_THAN';
            break;
        case '>':
            $newToken['type'] = 'T_GREATER_THAN';
            break;
        case '!':
            $newToken['type'] = 'T_BOOLEAN_NOT';
            break;
        case ',':
            $newToken['type'] = 'T_COMMA';
            break;
        case '@':
            $newToken['type'] = 'T_ASPERAND';
            break;
        default:
            $newToken['type'] = 'T_NONE';
            break;
        }//end switch

        $newToken['code']    = constant($newToken['type']);
        $newToken['content'] = $token;

        return $newToken;

    }//end resolveSimpleToken()


    /**
     * Returns true if the specified string is in the camel caps format.
     *
     * @param string  $string      The string the verify.
     * @param boolean $classFormat If true, check to see if the string is in the
     *                             class format. Class format strings must start
     *                             with a capital letter and contain no
     *                             underscores.
     * @param boolean $public      If true, the first character in the string
     *                             must be an a-z character. If false, the
     *                             character must be an underscore. This
     *                             argument is only applicable if $classFormat
     *                             is false.
     * @param boolean $strict      If true, the string must not have two captial
     *                             letters next to each other. If false, a
     *                             relaxed camel caps policy is used to allow
     *                             for acronyms.
     *
     * @return boolean
     */
    public static function isCamelCaps(
        $string,
        $classFormat=false,
        $public=true,
        $strict=true
    ) {
        // Check the first character first.
        if ($classFormat === false) {
            if ($public === false) {
                $legalFirstChar = '[_][a-z]';
            } else {
                $legalFirstChar = '[a-z]';
            }
        } else {
            $legalFirstChar = '[A-Z]';
        }

        if (preg_match("|^$legalFirstChar|", $string) === 0) {
            return false;
        }

        // Check that the name only contains legal characters.
        $legalChars = 'a-zA-Z0-9';
        if (preg_match("|[^$legalChars]|", substr($string, 1)) > 0) {
            return false;
        }

        if ($strict === true) {
            // Check that there are not two captial letters next to each other.
            $length          = strlen($string);
            $lastCharWasCaps = $classFormat;

            for ($i = 1; $i < $length; $i++) {
                $ascii = ord($string{$i});
                if ($ascii >= 48 && $ascii <= 57) {
                    // The character is a number, so it cant be a captial.
                    $isCaps = false;
                } else {
                    if (strtoupper($string{$i}) === $string{$i}) {
                        $isCaps = true;
                    } else {
                        $isCaps = false;
                    }
                }

                if ($isCaps === true && $lastCharWasCaps === true) {
                    return false;
                }

                $lastCharWasCaps = $isCaps;
            }
        }//end if

        return true;

    }//end isCamelCaps()


    /**
     * Returns true if the specified string is in the underscore caps format.
     *
     * @param string $string The string to verify.
     *
     * @return boolean
     */
    public static function isUnderscoreName($string)
    {
        // If there are space in the name, it can't be valid.
        if (strpos($string, ' ') !== false) {
            return false;
        }

        $validName = true;
        $nameBits  = explode('_', $string);

        if (preg_match('|^[A-Z]|', $string) === 0) {
            // Name does not begin with a capital letter.
            $validName = false;
        } else {
            foreach ($nameBits as $bit) {
                if ($bit{0} !== strtoupper($bit{0})) {
                    $validName = false;
                    break;
                }
            }
        }

        return $validName;

    }//end isUnderscoreName()


    /**
     * Returns a valid variable type for param/var tag.
     *
     * If type is not one of the standard type, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     *
     * @return string
     */
    public static function suggestType($varType)
    {
        if ($varType === '') {
            return '';
        }

        if (in_array($varType, self::$allowedTypes) === true) {
            return $varType;
        } else {
            $lowerVarType = strtolower($varType);
            switch ($lowerVarType) {
            case 'bool':
                return 'boolean';
            case 'double':
            case 'real':
                return 'float';
            case 'int':
                return 'integer';
            case 'array()':
                return 'array';
            }//end switch

            if (strpos($lowerVarType, 'array(') !== false) {
                // Valid array declaration:
                // array, array(type), array(type1 => type2).
                $matches = array();
                $pattern = '/^array\(\s*([^\s^=^>]*)(\s*=>\s*(.*))?\s*\)/i';
                if (preg_match($pattern, $varType, $matches) !== 0) {
                    $type1 = '';
                    if (isset($matches[1]) === true) {
                        $type1 = $matches[1];
                    }

                    $type2 = '';
                    if (isset($matches[3]) === true) {
                        $type2 = $matches[3];
                    }

                    $type1 = self::suggestType($type1);
                    $type2 = self::suggestType($type2);
                    if ($type2 !== '') {
                        $type2 = ' => '.$type2;
                    }

                    return "array($type1$type2)";
                } else {
                    return 'array';
                }//end if
            } else if (in_array($lowerVarType, self::$allowedTypes) === true) {
                // A valid type, but not lower cased.
                return $lowerVarType;
            } else {
                // Must be a custom type name.
                return $varType;
            }//end if
        }//end if

    }//end suggestType()


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
     *                                look in its default location.
     *
     * @return array
     * @see isInstalledStandard()
     */
    public static function getInstalledStandards(
        $includeGeneric=false,
        $standardsDir=''
    ) {
        $installedStandards = array();

        if ($standardsDir === '') {
            $standardsDir = dirname(__FILE__).'/CodeSniffer/Standards';
        }

        $di = new DirectoryIterator($standardsDir);
        foreach ($di as $file) {
            if ($file->isDir() === true && $file->isDot() === false) {
                $filename = $file->getFilename();

                // Ignore the special "Generic" standard.
                if ($includeGeneric === false && $filename === 'Generic') {
                    continue;
                }

                // Valid coding standard dirs include a standard class.
                $csFile = $file->getPathname()."/{$filename}CodingStandard.php";
                if (is_file($csFile) === true) {
                    // We found a coding standard directory.
                    $installedStandards[] = $filename;
                }
            }
        }

        return $installedStandards;

    }//end getInstalledStandards()


    /**
     * Determine if a standard is installed.
     *
     * Coding standards are directories located in the
     * CodeSniffer/Standards directory. Valid coding standards
     * include a Sniffs subdirectory.
     *
     * @param string $standard The name of the coding standard.
     *
     * @return boolean
     * @see getInstalledStandards()
     */
    public static function isInstalledStandard($standard)
    {
        $standardDir  = dirname(__FILE__);
        $standardDir .= '/CodeSniffer/Standards/'.$standard;
        if (is_file("$standardDir/{$standard}CodingStandard.php") === true) {
            return true;
        } else {
            // This could be a custom standard, installed outside our
            // standards directory.
            $standardFile = rtrim($standard, ' /\\').DIRECTORY_SEPARATOR.basename($standard).'CodingStandard.php';
            return (is_file($standardFile) === true);
        }

    }//end isInstalledStandard()


    /**
     * Get a single config value.
     *
     * Config data is stored in the data dir, in a file called
     * CodeSniffer.conf. It is a simple PHP array.
     *
     * @param string $key The name of the config value.
     *
     * @return string
     * @see setConfigData()
     * @see getAllConfigData()
     */
    public static function getConfigData($key)
    {
        $phpCodeSnifferConfig = self::getAllConfigData();

        if ($phpCodeSnifferConfig === null) {
            return null;
        }

        if (isset($phpCodeSnifferConfig[$key]) === false) {
            return null;
        }

        return $phpCodeSnifferConfig[$key];

    }//end getConfigData()


    /**
     * Set a single config value.
     *
     * Config data is stored in the data dir, in a file called
     * CodeSniffer.conf. It is a simple PHP array.
     *
     * @param string      $key   The name of the config value.
     * @param string|null $value The value to set. If null, the config
     *                           entry is deleted, reverting it to the
     *                           default value.
     * @param boolean     $temp  Set this config data temporarily for this
     *                           script run. This will not write the config
     *                           data to the config file.
     *
     * @return boolean
     * @see getConfigData()
     * @throws PHP_CodeSniffer_Exception If the config file can not be written.
     */
    public static function setConfigData($key, $value, $temp=false)
    {
        if ($temp === false) {
            $configFile = dirname(__FILE__).'/CodeSniffer.conf';
            if (is_file($configFile) === false) {
                $configFile = '@data_dir@/PHP_CodeSniffer/CodeSniffer.conf';
            }

            if (is_file($configFile) === true
                && is_writable($configFile) === false
            ) {
                $error = "Config file $configFile is not writable";
                throw new PHP_CodeSniffer_Exception($error);
            }
        }

        $phpCodeSnifferConfig = self::getAllConfigData();

        if ($value === null) {
            if (isset($phpCodeSnifferConfig[$key]) === true) {
                unset($phpCodeSnifferConfig[$key]);
            }
        } else {
            $phpCodeSnifferConfig[$key] = $value;
        }

        if ($temp === false) {
            $output  = '<'.'?php'."\n".' $phpCodeSnifferConfig = ';
            $output .= var_export($phpCodeSnifferConfig, true);
            $output .= "\n?".'>';

            if (file_put_contents($configFile, $output) === false) {
                return false;
            }
        }

        $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA'] = $phpCodeSnifferConfig;

        return true;

    }//end setConfigData()


    /**
     * Get all config data in an array.
     *
     * @return string
     * @see getConfigData()
     */
    public static function getAllConfigData()
    {
        if (isset($GLOBALS['PHP_CODESNIFFER_CONFIG_DATA']) === true) {
            return $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA'];
        }

        $configFile = dirname(__FILE__).'/CodeSniffer.conf';
        if (is_file($configFile) === false) {
            $configFile = '@data_dir@/PHP_CodeSniffer/CodeSniffer.conf';
        }

        if (is_file($configFile) === false) {
            return null;
        }

        include $configFile;
        $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA'] = $phpCodeSnifferConfig;
        return $GLOBALS['PHP_CODESNIFFER_CONFIG_DATA'];

    }//end getAllConfigData()


}//end class

?>
