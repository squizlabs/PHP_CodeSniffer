<?php
/**
 * An abstract class that all sniff unit tests must extend.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * An abstract class that all sniff unit tests must extend.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings that are not found, or
 * warnings and errors that are not expected, are considered test failures.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class AbstractSniffUnitTest extends PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer object used for testing.
     *
     * @var PHP_CodeSniffer
     */
    protected static $phpcs = null;


    /**
     * Sets up this unit test.
     *
     * @return void
     */
    protected function setUp()
    {
        if (self::$phpcs === null) {
            self::$phpcs = new PHP_CodeSniffer();
        }

    }//end setUp()


    /**
     * Should this test be skipped for some reason.
     *
     * @return void
     */
    protected function shouldSkipTest()
    {
        return false;

    }//end shouldSkipTest()


    /**
     * Tests the extending classes Sniff class.
     *
     * @return void
     * @throws PHPUnit_Framework_Error
     */
    protected final function runTest()
    {
        // Skip this test if we can't run in this environment.
        if ($this->shouldSkipTest() === true) {
            $this->markTestSkipped();
        }

        // The basis for determining file locations.
        $basename = substr(get_class($this), 0, -8);

        // The name of the coding standard we are testing.
        $standardName = substr($basename, 0, strpos($basename, '_'));

        // The class name of the sniff we are testing.
        $sniffClass = str_replace('_Tests_', '_Sniffs_', $basename).'Sniff';

        if (is_file(dirname(__FILE__).'/../../CodeSniffer.php') === true) {
            // We have not been installed.
            $standardsDir = realpath(dirname(__FILE__).'/../../CodeSniffer/Standards');
            $testFileBase = $standardsDir.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $basename).'UnitTest.';
        } else {
            // The name of the dummy file we are testing.
            $testFileBase = dirname(__FILE__).DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $basename).'UnitTest.';
        }

        // Get a list of all test files to check. These will have the same base
        // name but different extensions. We ignore the .php file as it is the
        // class.
        $testFiles = array();

        $dir = substr($testFileBase, 0, strrpos($testFileBase, DIRECTORY_SEPARATOR));
        $di  = new DirectoryIterator($dir);

        foreach ($di as $file) {
            $path = $file->getPathname();
            if (substr($path, 0, strlen($testFileBase)) === $testFileBase) {
                if ($path !== $testFileBase.'php') {
                    $testFiles[] = $path;
                }
            }
        }

        // Get them in order. This is particularly important for multi-file sniffs.
        sort($testFiles);

        $failureMessages = array();
        $multiFileSniff  = false;
        foreach ($testFiles as $testFile) {
            try {
                self::$phpcs->process(array(), $standardName, array($sniffClass));
                self::$phpcs->setIgnorePatterns(array());
                self::$phpcs->processFile($testFile);
                self::$phpcs->processMulti();
            } catch (Exception $e) {
                $this->fail('An unexpected exception has been caught: '.$e->getMessage());
            }

            // After processing a file, check if the sniff was actually
            // a multi-file sniff (i.e., had no individual file sniffs).
            // If it is, we can skip checking of the other files and
            // do a single multi-file check.
            $sniffs = self::$phpcs->getTokenSniffs();
            if (empty($sniffs['file']) === true) {
                $multiFileSniff = true;
                break;
            }

            $files = self::$phpcs->getFiles();
            if (empty($files) === true) {
                // File was skipped for some reason.
                echo "Skipped: $testFile\n";
                $this->markTestSkipped();
            }

            $file = array_pop($files);

            $failures        = $this->generateFailureMessages($file);
            $failureMessages = array_merge($failureMessages, $failures);
        }//end foreach

        if ($multiFileSniff === true) {
            try {
                self::$phpcs->process(array(), $standardName, array($sniffClass));
                self::$phpcs->setIgnorePatterns(array());
                foreach ($testFiles as $testFile) {
                    self::$phpcs->processFile($testFile);
                }

                self::$phpcs->processMulti();
            } catch (Exception $e) {
                $this->fail('An unexpected exception has been caught: '.$e->getMessage());
            }

            $files = self::$phpcs->getFiles();
            if (empty($files) === true) {
                // File was skipped for some reason.
                $this->markTestSkipped();
            } else {
                foreach ($files as $file) {
                    $failures        = $this->generateFailureMessages($file);
                    $failureMessages = array_merge($failureMessages, $failures);
                }
            }
        }//end if

        if (empty($failureMessages) === false) {
            $this->fail(implode(PHP_EOL, $failureMessages));
        }

    }//end testSniff()


    /**
     * Generate a list of test failures for a given sniffed file.
     *
     * @param PHP_CodeSniffer_File $file The file being tested.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception
     */
    public function generateFailureMessages(PHP_CodeSniffer_File $file)
    {
        $testFile = $file->getFilename();

        $foundErrors      = $file->getErrors();
        $foundWarnings    = $file->getWarnings();
        $expectedErrors   = $this->getErrorList(basename($testFile));
        $expectedWarnings = $this->getWarningList(basename($testFile));

        if (is_array($expectedErrors) === false) {
            throw new PHP_CodeSniffer_Exception('getErrorList() must return an array');
        }

        if (is_array($expectedWarnings) === false) {
            throw new PHP_CodeSniffer_Exception('getWarningList() must return an array');
        }

        /*
         We merge errors and warnings together to make it easier
         to iterate over them and produce the errors string. In this way,
         we can report on errors and warnings in the same line even though
         it's not really structured to allow that.
        */

        $allProblems     = array();
        $failureMessages = array();

        foreach ($foundErrors as $line => $lineErrors) {
            foreach ($lineErrors as $column => $errors) {
                if (isset($allProblems[$line]) === false) {
                    $allProblems[$line] = array(
                                           'expected_errors'   => 0,
                                           'expected_warnings' => 0,
                                           'found_errors'      => array(),
                                           'found_warnings'    => array(),
                                          );
                }

                $foundErrorsTemp = array();
                foreach ($allProblems[$line]['found_errors'] as $foundError) {
                    $foundErrorsTemp[] = $foundError;
                }

                $errorsTemp = array();
                foreach ($errors as $foundError) {
                    $errorsTemp[] = $foundError['message'];
                }

                $allProblems[$line]['found_errors'] = array_merge($foundErrorsTemp, $errorsTemp);
            }

            if (isset($expectedErrors[$line]) === true) {
                $allProblems[$line]['expected_errors'] = $expectedErrors[$line];
            } else {
                $allProblems[$line]['expected_errors'] = 0;
            }

            unset($expectedErrors[$line]);
        }//end foreach

        foreach ($expectedErrors as $line => $numErrors) {
            if (isset($allProblems[$line]) === false) {
                $allProblems[$line] = array(
                                       'expected_errors'   => 0,
                                       'expected_warnings' => 0,
                                       'found_errors'      => array(),
                                       'found_warnings'    => array(),
                                      );
            }

            $allProblems[$line]['expected_errors'] = $numErrors;
        }

        foreach ($foundWarnings as $line => $lineWarnings) {
            foreach ($lineWarnings as $column => $warnings) {
                if (isset($allProblems[$line]) === false) {
                    $allProblems[$line] = array(
                                           'expected_errors'   => 0,
                                           'expected_warnings' => 0,
                                           'found_errors'      => array(),
                                           'found_warnings'    => array(),
                                          );
                }

                $foundWarningsTemp = array();
                foreach ($allProblems[$line]['found_warnings'] as $foundWarning) {
                    $foundWarningsTemp[] = $foundWarning;
                }

                $warningsTemp = array();
                foreach ($warnings as $warning) {
                    $warningsTemp[] = $warning['message'];
                }

                $allProblems[$line]['found_warnings'] = array_merge($foundWarningsTemp, $warningsTemp);
            }

            if (isset($expectedWarnings[$line]) === true) {
                $allProblems[$line]['expected_warnings'] = $expectedWarnings[$line];
            } else {
                $allProblems[$line]['expected_warnings'] = 0;
            }

            unset($expectedWarnings[$line]);
        }//end foreach

        foreach ($expectedWarnings as $line => $numWarnings) {
            if (isset($allProblems[$line]) === false) {
                $allProblems[$line] = array(
                                       'expected_errors'   => 0,
                                       'expected_warnings' => 0,
                                       'found_errors'      => array(),
                                       'found_warnings'    => array(),
                                      );
            }

            $allProblems[$line]['expected_warnings'] = $numWarnings;
        }

        // Order the messages by line number.
        ksort($allProblems);

        foreach ($allProblems as $line => $problems) {
            $numErrors        = count($problems['found_errors']);
            $numWarnings      = count($problems['found_warnings']);
            $expectedErrors   = $problems['expected_errors'];
            $expectedWarnings = $problems['expected_warnings'];

            $errors      = '';
            $foundString = '';

            if ($expectedErrors !== $numErrors || $expectedWarnings !== $numWarnings) {
                $lineMessage     = "[LINE $line]";
                $expectedMessage = 'Expected ';
                $foundMessage    = 'in '.basename($testFile).' but found ';

                if ($expectedErrors !== $numErrors) {
                    $expectedMessage .= "$expectedErrors error(s)";
                    $foundMessage    .= "$numErrors error(s)";
                    if ($numErrors !== 0) {
                        $foundString .= 'error(s)';
                        $errors      .= implode(PHP_EOL.' -> ', $problems['found_errors']);
                    }

                    if ($expectedWarnings !== $numWarnings) {
                        $expectedMessage .= ' and ';
                        $foundMessage    .= ' and ';
                        if ($numWarnings !== 0) {
                            if ($foundString !== '') {
                                $foundString .= ' and ';
                            }
                        }
                    }
                }

                if ($expectedWarnings !== $numWarnings) {
                    $expectedMessage .= "$expectedWarnings warning(s)";
                    $foundMessage    .= "$numWarnings warning(s)";
                    if ($numWarnings !== 0) {
                        $foundString .= 'warning(s)';
                        if (empty($errors) === false) {
                            $errors .= PHP_EOL.' -> ';
                        }

                        $errors .= implode(PHP_EOL.' -> ', $problems['found_warnings']);
                    }
                }

                $fullMessage = "$lineMessage $expectedMessage $foundMessage.";
                if ($errors !== '') {
                    $fullMessage .= " The $foundString found were:".PHP_EOL." -> $errors";
                }

                $failureMessages[] = $fullMessage;
            }//end if
        }//end foreach

        return $failureMessages;

    }//end generateFailureMessages()


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    protected abstract function getErrorList();


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    protected abstract function getWarningList();


}//end class

?>
