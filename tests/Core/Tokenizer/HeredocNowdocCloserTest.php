<?php
/**
 * Tests the tokenization of goto declarations and statements.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Heredoc/nowdoc closer token test.
 *
 * @requires PHP 7.3
 */
class HeredocNowdocCloserTest extends AbstractMethodUnitTest
{


    /**
     * Initialize & tokenize \PHP_CodeSniffer\Files\File with code from the test case file.
     *
     * {@internal This is a near duplicate of the original method. Only difference is that
     * tab replacement is enabled for this test.}
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        $config            = new Config();
        $config->standards = ['PSR1'];
        $config->tabWidth  = 4;

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
     * Verify that leading (indent) whitespace in a heredoc/nowdoc closer token get the tab replacement treatment.
     *
     * @param string $testMarker The comment prefacing the target token.
     * @param array  $expected   Expectations for the token array.
     *
     * @dataProvider dataHeredocNowdocCloserTabReplacement
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testHeredocNowdocCloserTabReplacement($testMarker, $expected)
    {
        $tokens = self::$phpcsFile->getTokens();

        $closer = $this->getTargetToken($testMarker, [T_END_HEREDOC, T_END_NOWDOC]);

        foreach ($expected as $key => $value) {
            if ($key === 'orig_content' && $value === null) {
                $this->assertArrayNotHasKey($key, $tokens[$closer], "Unexpected 'orig_content' key found in the token array.");
                continue;
            }

            $this->assertArrayHasKey($key, $tokens[$closer], "Key $key not found in the token array.");
            $this->assertSame($value, $tokens[$closer][$key], "Value for key $key does not match expectation.");
        }

    }//end testHeredocNowdocCloserTabReplacement()


    /**
     * Data provider.
     *
     * @see testHeredocNowdocCloserTabReplacement()
     *
     * @return array
     */
    public function dataHeredocNowdocCloserTabReplacement()
    {
        return [
            [
                'testMarker' => '/* testHeredocCloserNoIndent */',
                'expected'   => [
                    'length'       => 3,
                    'content'      => 'EOD',
                    'orig_content' => null,
                ],
            ],
            [
                'testMarker' => '/* testNowdocCloserNoIndent */',
                'expected'   => [
                    'length'       => 3,
                    'content'      => 'EOD',
                    'orig_content' => null,
                ],
            ],
            [
                'testMarker' => '/* testHeredocCloserSpaceIndent */',
                'expected'   => [
                    'length'       => 7,
                    'content'      => '    END',
                    'orig_content' => null,
                ],
            ],
            [
                'testMarker' => '/* testNowdocCloserSpaceIndent */',
                'expected'   => [
                    'length'       => 8,
                    'content'      => '     END',
                    'orig_content' => null,
                ],
            ],
            [
                'testMarker' => '/* testHeredocCloserTabIndent */',
                'expected'   => [
                    'length'       => 8,
                    'content'      => '     END',
                    'orig_content' => '	 END',
                ],
            ],
            [
                'testMarker' => '/* testNowdocCloserTabIndent */',
                'expected'   => [
                    'length'       => 7,
                    'content'      => '    END',
                    'orig_content' => '	END',
                ],
            ],
        ];

    }//end dataHeredocNowdocCloserTabReplacement()


}//end class
