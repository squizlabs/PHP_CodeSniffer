<?php
/**
 * Base class to use when testing utility methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHPUnit\Framework\TestCase;

abstract class AbstractMethodUnitTest extends TestCase
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * This allows child classes to overrule the default `inc` with, for instance,
     * `js` or `css` when applicable.
     *
     * @var string
     */
    protected static $fileExtension = 'inc';

    /**
     * The \PHP_CodeSniffer\Files\File object containing the parsed contents of the test case file.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    protected static $phpcsFile;


    /**
     * Initialize & tokenize \PHP_CodeSniffer\Files\File with code from the test case file.
     *
     * The test case file for a unit test class has to be in the same directory
     * directory and use the same file name as the test class, using the .inc extension.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        $config            = new Config();
        $config->standards = ['PSR1'];

        $ruleset = new Ruleset($config);

        // Default to a file with the same name as the test class. Extension is property based.
        $relativeCN     = str_replace(__NAMESPACE__, '', get_called_class());
        $relativePath   = str_replace('\\', DIRECTORY_SEPARATOR, $relativeCN);
        $pathToTestFile = realpath(__DIR__).$relativePath.'.'.static::$fileExtension;

        // Make sure the file gets parsed correctly based on the file type.
        $contents  = 'phpcs_input_file: '.$pathToTestFile.PHP_EOL;
        $contents .= file_get_contents($pathToTestFile);

        self::$phpcsFile = new DummyFile($contents, $ruleset, $config);
        self::$phpcsFile->process();

    }//end setUpBeforeClass()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::$phpcsFile = null;

    }//end tearDownAfterClass()


    /**
     * Get the token pointer for a target token based on a specific comment found on the line before.
     *
     * Note: the test delimiter comment MUST start with "/* test" to allow this function to
     * distinguish between comments used *in* a test and test delimiters.
     *
     * @param string           $commentString The delimiter comment to look for.
     * @param int|string|array $tokenType     The type of token(s) to look for.
     * @param string           $tokenContent  Optional. The token content for the target token.
     *
     * @return int
     */
    public function getTargetToken($commentString, $tokenType, $tokenContent=null)
    {
        $start   = (self::$phpcsFile->numTokens - 1);
        $comment = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            $commentString
        );

        $tokens = self::$phpcsFile->getTokens();
        $end    = ($start + 1);

        // Limit the token finding to between this and the next delimiter comment.
        for ($i = ($comment + 1); $i < $end; $i++) {
            if ($tokens[$i]['code'] !== T_COMMENT) {
                continue;
            }

            if (stripos($tokens[$i]['content'], '/* test') === 0) {
                $end = $i;
                break;
            }
        }

        $target = self::$phpcsFile->findNext(
            $tokenType,
            ($comment + 1),
            $end,
            false,
            $tokenContent
        );

        if ($target === false) {
            $msg = 'Failed to find test target token for comment string: '.$commentString;
            if ($tokenContent !== null) {
                $msg .= ' With token content: '.$tokenContent;
            }

            $this->assertFalse(true, $msg);
        }

        return $target;

    }//end getTargetToken()


}//end class
