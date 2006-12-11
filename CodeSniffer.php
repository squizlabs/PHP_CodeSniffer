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

require_once 'PHP/CodeSniffer/File.php';
require_once 'PHP/CodeSniffer/Tokens.php';
require_once 'PHP/CodeSniffer/Sniff.php';
require_once 'PHP/CodeSniffer/Exception.php';

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
    private $_file = array();

    /**
     * The directory where to search for tests.
     *
     * @var string
     */
    private $_standardDir = '';

    /**
     * The files that have been processed.
     *
     * @var array(PHP_CodeSniffer_FILE)
     */
    private $_files = array();

    /**
     * The listeners array.
     *
     * @var array(PHP_CodeSniffer_Sniff)
     */
    private $_listeners = array();

    /**
     * A cache of classes found within sniff files.
     *
     * @var array
     */
    private $_classCache = array();


    /**
     * An array of PHP file extensions.
     *
     * @var array
     */
    private static $_validPhpExtensions = array(
                                           'php',
                                           'inc',
                                          );


    /**
     * Constructs a PHP_CodeSniffer object.
     *
     * @param int $verbosity The verbosity level.
     *                       1: Print progress information.
     *                       2: Print developer debug information.
     *
     * @see process()
     */
    public function __construct($verbosity=0)
    {
        define('PHP_CODESNIFFER_VERBOSITY', $verbosity);

    }//end __construct()


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
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If files or standard are invalid.
     */
    public function process($files, $standard, array $sniffs=array())
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

        $this->_standardDir = dirname(__FILE__).'/CodeSniffer/Standards/'.$standard;

        // Reset the members.
        $this->_listeners = array();
        $this->_files     = array();

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo 'Registering sniffs... ';
        }

        $this->_registerTokenListeners($standard, $sniffs);
        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo "DONE\n";
        }

        foreach ($files as $file) {
            $this->_file = $file;
            if (is_dir($this->_file) === true) {
                $this->_processFiles($this->_file);
            } else {
                $this->_processFile($this->_file);
            }
        }

    }//end process()


    //-- REGISTERING TOKEN LISTENERS --//


    /**
     * Registers installed sniffs in the coding standard being used.
     *
     * Traverses the standard directory for classes that implement the
     * PHP_CodeSniffer_Sniff interface asks them to register. Each of the
     * sniff's class names must be exact as the basename of the sniff file.
     *
     * @param string $standard The name of the coding standard we are checking.
     * @param array  $sniffs   The sniff names to restrict the allowed
     *                         listeners to.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If any of the tests failed in the
     *                                   registration process.
     */
    private function _registerTokenListeners($standard, array $sniffs=array())
    {
        $files = $this->_getSniffFiles($this->_standardDir);

        if (empty($sniffs) === false) {
            // Convert the allowed sniffs to lower case so
            // that its easier to check.
            foreach ($sniffs as &$sniff) {
                $sniff = strtolower($sniff);
            }
        }

        foreach ($files as $file) {

            $fileParts = explode('.', $file);
            // We are only interested in php files.
            if (array_pop($fileParts) !== 'php') {
                continue;
            }

            $basename = basename($file, '.php');

            // We are only interested in Sniff files.
            if (substr($basename, -5) !== 'Sniff') {
                continue;
            }

            if (isset($this->_classCache[$file]) === false) {
                // Determine what classes this file contains.
                $currentClasses = get_declared_classes();
                include_once $file;
                $this->_classCache[$file] = array_diff(get_declared_classes(), $currentClasses);
            }

            $newClasses = $this->_classCache[$file];

            foreach ($newClasses as $className) {

                // Only include sniffs that are in our coding standard.
                // We know those sniffs because their class anem starts
                // with [STANDARD]_
                if (preg_match("|^${standard}_|", $className) === 0) {
                    continue;
                }

                $rfClass = new ReflectionClass($className);
                if ($rfClass->implementsInterface('PHP_CodeSniffer_Sniff') === false) {
                    // It's not a test so lets continue.
                    unset($rfClass);
                    continue;
                }

                // If they have specified a list of sniffs to restrict to, check
                // to see if this sniff is allowed.
                if (empty($sniffs) === false && in_array(strtolower($className), $sniffs) === false) {
                    continue;
                }

                if ($rfClass->isAbstract() === true) {
                    // Cannot instantiate abstract classes.
                    unset($rfClass);
                    continue;
                }

                $listener = new $className();
                $tokens   = $listener->register();

                if (is_array($tokens) === false) {
                    $msg = 'Sniff '.$className.' register method must return an array';
                    throw new PHP_CodeSniffer_Exception($msg);
                }

                $this->_addTokenListener($listener, $tokens);
            }//end foreach
        }//end foreach

    }//end _registerTokenListeners()


    /**
     * Adds a listener to the token stack that listens to the specific tokens.
     *
     * When PHP_CodeSniffer encounters on the the tokens specified in $tokens,
     * it invokes the process method of the sniff.
     *
     * @param PHP_CodeSniffer_Sniff $listener The listener to add to the
     *                                        listener stack.
     * @param array(int)            $tokens   The token types the listener
     *                                        wishes to listen to.
     *
     * @return void
     */
    private function _addTokenListener(PHP_CodeSniffer_Sniff $listener, array $tokens)
    {
        foreach ($tokens as $token) {
            if (isset($this->_listeners[$token]) === false) {
                $this->_listeners[$token] = array();
            }

            if (in_array($listener, $this->_listeners[$token]) === false) {
                $this->_listeners[$token][] = $listener;
            }
        }

    }//end _addTokenListener()


    /**
     * Recursively read a specified directory and return sniff files within.
     *
     * @param string $dir The directory where to look for the files.
     *
     * @return array
     * @throws Exception If there was an error opening the directory.
     */
    private function _getSniffFiles($dir)
    {
        $di    = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $files = array();

        foreach ($di as $file) {
            $fileParts   = explode('.', $file);
            $currFileExt = array_pop($fileParts);

            if ($currFileExt === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;

    }//end _getSniffFiles()


    //-- PROCESSING SOURCE FILES --//


    /**
     * Run the code sniffs over each file in a given directory.
     *
     * Recusively reads the specified directory and performs the PHP_CodeSniffer
     * sniffs on each source file found within the directories.
     *
     * @param string $dir The directory to process.
     *
     * @return void
     * @throws Exception If there was an error opening the directory.
     */
    private function _processFiles($dir)
    {
        $di = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($di as $file) {
            $filePath = $file->getPathname();

            $fileParts = explode('.', $filePath);
            $extension = array_pop($fileParts);

            // We are only interested in php files.
            if (in_array($extension, self::$_validPhpExtensions) === false) {
                continue;
            }

            $this->_processFile($filePath);
        }

    }//end _processFiles()


    /**
     * Run the code sniffs over a signle given file.
     *
     * Processes the file and runs the PHP_CodeSniffer sniffs to verify that it
     * conforms with the standard.
     *
     * @param string $file The file to process.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If the file could not be processed.
     */
    private function _processFile($file)
    {
        if (file_exists($file) === false) {
            throw new PHP_CodeSniffer_Exception('Source file '.$file.' does not exist');
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $startTime = time();
            echo 'Processing '.basename($file).' ';
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo "\n";
            }
        }

        $phpcsFile = new PHP_CodeSniffer_File($file, $this->_listeners);
        $this->_files[] = $phpcsFile;
        $phpcsFile->start();

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            $timeTaken = time() - $startTime;
            if ($timeTaken === 0) {
                echo "DONE in < 1 second\n";
            } else if ($timeTaken === 1) {
                echo "DONE in 1 second\n";
            } else {
                echo "DONE in $timeTaken seconds\n";
            }
        }

    }//end _processFile()


    /**
     * Prints all errors and warnings for each file processed.
     *
     * Errors and warnings are displayed together, grouped by file.
     *
     * @param boolean $showWarnings Show warnings as well as errors.
     *
     * @return void
     */
    public function printErrorReport($showWarnings=true)
    {
        foreach ($this->_files as $file) {
            $warnings    = $file->getWarnings();
            $errors      = $file->getErrors();
            $numWarnings = $file->getWarningCount();
            $numErrors   = $file->getErrorCount();
            $filename    = $file->getFilename();

            if ($numErrors === 0 && $numWarnings === 0) {
                // Prefect score!
                continue;
            }

            if ($numErrors === 0 && $showWarnings === false) {
                // Prefect score! (sort of)
                continue;
            }

            // Merge errors and warnings.
            foreach ($errors as $line => $lineErrors) {
                $newErrors = array();
                foreach ($errors[$line] as $message) {
                    $newErrors[] = 'ERROR: '.$message;
                }

                $errors[$line] = $newErrors;
            }

            if ($showWarnings === true) {
                foreach ($warnings as $line => $lineWarnings) {
                    $newWarnings = array();
                    foreach ($lineWarnings as $message) {
                        $newWarnings[] = 'WARNING: '.$message;
                    }

                    if (isset($errors[$line]) === true) {
                        $errors[$line] = array_merge($newWarnings, $newErrors);
                    } else {
                        $errors[$line] = $newWarnings;
                    }
                }
            }

            ksort($errors);

            echo "\nFILE: ";
            if (strlen($filename) <= 71) {
                echo $filename;
            } else {
                echo '...'.substr($filename, strlen($filename) - 71);
            }

            echo "\n";
            echo str_repeat('-', 80)."\n";
            $numLines = count($errors);
            echo "FOUND $numErrors ERROR(S) ";

            if ($showWarnings) {
                echo "AND $numWarnings WARNING(S) ";
            }

            echo "AFFECTING $numLines LINE(S)\n";
            echo str_repeat('-', 80)."\n";

            foreach ($errors as $line => $lineErrors) {
                foreach ($lineErrors as $error) {
                    echo '[LINE '.$line.'] '.$error."\n";
                }
            }

            echo str_repeat('-', 80)."\n\n";

        }//end foreach

    }//end printErrorReport()


    /**
     * Prints a summary of errors and warnings for each file processed.
     *
     * If verbose output is enabled, results are shown for all files, even if
     * they have no errors or warnings. If verbose output is disabled, we only
     * show files that have at least one warning or error.
     *
     * @param boolean $showWarnings Show warnings as well as errors.
     *
     * @return void
     */
    public function printErrorReportSummary($showWarnings=true)
    {
        $errorFiles = array();

        foreach ($this->_files as $file) {
            $numWarnings = $file->getWarningCount();
            $numErrors   = $file->getErrorCount();
            $filename    = $file->getFilename();

            // If verbose output is enabled, we show the results for all files,
            // but if not, we only show files that had errors or warnings.
            if (PHP_CODESNIFFER_VERBOSITY > 0 || $numErrors > 0 || ($numWarnings > 0 && $showWarnings === true)) {
                $errorFiles[$filename] = array(
                                          'warnings' => $numWarnings,
                                          'errors'   => $numErrors,
                                         );
            }
        }

        if (empty($errorFiles) === true) {
            // Nothing to print.
            return;
        }

        echo "\nPHP CODE SNIFFER REPORT SUMMARY\n";
        echo str_repeat('-', 80)."\n";
        if ($showWarnings) {
            echo 'FILE'.str_repeat(' ', 60)."ERRORS  WARNINGS\n";
        } else {
            echo 'FILE'.str_repeat(' ', 70)."ERRORS\n";
        }

        echo str_repeat('-', 80)."\n";

        $totalErrors   = 0;
        $totalWarnings = 0;
        $totalFiles    = 0;

        foreach ($errorFiles as $file => $errors) {
            if ($showWarnings) {
                $padding = (62 - strlen($file));
            } else {
                $padding = (72 - strlen($file));
            }

            if ($padding < 0) {
                $file    = '...'.substr($file, ($padding * -1) + 3);
                $padding = 0;
            }

            echo $file.str_repeat(' ', $padding).'  ';
            echo $errors['errors'];
            if ($showWarnings) {
                echo str_repeat(' ', 8 - strlen((string) $errors['errors']));
                echo $errors['warnings'];
            }

            echo "\n";

            $totalErrors   += $errors['errors'];
            $totalWarnings += $errors['warnings'];
            $totalFiles++;
        }//end foreach

        echo str_repeat('-', 80)."\n";
        echo "A TOTAL OF $totalErrors ERROR(S) ";
        if ($showWarnings) {
            echo "AND $totalWarnings WARNING(S) ";
        }

        echo "WERE FOUND IN $totalFiles FILE(S)\n";
        echo str_repeat('-', 80)."\n\n";

    }//end printErrorReportSummary()


    /**
     * Returns the PHP_CodeSniffer file objects.
     *
     * @return array(PHP_CodeSniffer_File)
     */
    public function getFiles()
    {
        return $this->_files;

    }//end getFiles()


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
            // Some T_STRING tokens can be more specific.
            if ($token[0] === T_STRING) {
                $newToken = self::resolveTstringToken($token);
            } else {
                $newToken            = array();
                $newToken['code']    = $token[0];
                $newToken['content'] = $token[1];
                $newToken['type']    = token_name($token[0]);
            }
        }

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
            $newToken['type'] = 'T_FALSE';
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
    public static function isCamelCaps($string, $classFormat=false, $public=true, $strict=true)
    {
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
        if ($classFormat === false) {
            $legalChars = 'a-zA-Z0-9';
        } else {
            $legalChars =  'a-zA-Z';
        }

        if (preg_match("|[^$legalChars]|", substr($string, 1)) > 0) {
            return false;
        }

        if ($strict) {
            // Check that there are not two captial letters next to each other.
            $length          = strlen($string);
            $lastCharWasCaps = ($classFormat === false) ? false : true;

            for ($i = 1; $i < $length; $i++) {
                $isCaps = (strtoupper($string{$i}) === $string{$i}) ? true : false;
                if ($isCaps === true && $lastCharWasCaps === true) {
                    return false;
                }

                $lastCharWasCaps = $isCaps;
            }
        }

        return true;

    }//end isCamelCaps()


    /**
     * Returns true if the specified string is in the underscore caps format.
     *
     * @param string $string The string the verify.
     *
     * @return boolean
     */
    public static function isUnderscoreName($string)
    {
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


    //-- STANDARDS --//


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
     *
     * @return array
     * @see isInstalledStandard()
     */
    public static function getInstalledStandards($includeGeneric=false)
    {
        $installedStandards = array();
        $standardsDir       = dirname(__FILE__).'/CodeSniffer/Standards';

        $di = new DirectoryIterator($standardsDir);
        foreach ($di as $file) {
            if ($file->isDir() === true && $file->isDot() === false) {
                // Ignore the special "Generic" standard.
                if ($includeGeneric === false && $file->getFilename() === 'Generic') {
                    continue;
                }

                // Valid coding standard dirs include a "Sniffs"
                // subdirectory, so check for it.
                if (is_dir($file->getPathname().'/Sniffs') === true) {
                    // We found a coding standard directory.
                    $installedStandards[] = $file->getFilename();
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
        $standardDir = dirname(__FILE__).'/CodeSniffer/Standards/'.$standard.'/Sniffs';
        return (is_dir($standardDir) === true);

    }//end isInstalledStandard()


}//end class

?>
