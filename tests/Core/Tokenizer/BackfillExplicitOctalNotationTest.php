<?php
/**
 * Tests the tokenization of explicit octal notation to PHP < 8.1.
 *
 * @author    Mark Baker <mark@demon-angel.eu>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class BackfillExplicitOctalNotationTest extends AbstractMethodUnitTest
{


    /**
     * Test that explicitly-defined octal values are tokenized as a single number and not as a number and a string.
     *
     * @param string     $marker      The comment which prefaces the target token in the test file.
     * @param string     $value       The expected content of the token
     * @param int|string $nextToken   The expected next token.
     * @param string     $nextContent The expected content of the next token.
     *
     * @dataProvider dataExplicitOctalNotation
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testExplicitOctalNotation($marker, $value, $nextToken, $nextContent)
    {
        $tokens = self::$phpcsFile->getTokens();

        $number = $this->getTargetToken($marker, [T_LNUMBER]);

        $this->assertSame($value, $tokens[$number]['content'], 'Content of integer token does not match expectation');

        $this->assertSame($nextToken, $tokens[($number + 1)]['code'], 'Next token is not the expected type, but '.$tokens[($number + 1)]['type']);
        $this->assertSame($nextContent, $tokens[($number + 1)]['content'], 'Next token did not have the expected contents');

    }//end testExplicitOctalNotation()


    /**
     * Data provider.
     *
     * @see testExplicitOctalNotation()
     *
     * @return array
     */
    public function dataExplicitOctalNotation()
    {
        return [
            [
                'marker'      => '/* testExplicitOctal */',
                'value'       => '0o137041',
                'nextToken'   => T_SEMICOLON,
                'nextContent' => ';',
            ],
            [
                'marker'      => '/* testExplicitOctalCapitalised */',
                'value'       => '0O137041',
                'nextToken'   => T_SEMICOLON,
                'nextContent' => ';',
            ],
            [
                'marker'      => '/* testExplicitOctalWithNumericSeparator */',
                'value'       => '0o137_041',
                'nextToken'   => T_SEMICOLON,
                'nextContent' => ';',
            ],
            [
                'marker'      => '/* testInvalid1 */',
                'value'       => '0',
                'nextToken'   => T_STRING,
                'nextContent' => 'o_137',
            ],
            [
                'marker'      => '/* testInvalid2 */',
                'value'       => '0',
                'nextToken'   => T_STRING,
                'nextContent' => 'O_41',
            ],
            [
                'marker'      => '/* testInvalid3 */',
                'value'       => '0',
                'nextToken'   => T_STRING,
                'nextContent' => 'o91',
            ],
            [
                'marker'      => '/* testInvalid4 */',
                'value'       => '0O2',
                'nextToken'   => T_LNUMBER,
                'nextContent' => '82',
            ],
            [
                'marker'      => '/* testInvalid5 */',
                'value'       => '0o2',
                'nextToken'   => T_LNUMBER,
                'nextContent' => '8_2',
            ],
            [
                'marker'      => '/* testInvalid6 */',
                'value'       => '0o2',
                'nextToken'   => T_STRING,
                'nextContent' => '_82',
            ],
            [
                'marker'      => '/* testInvalid7 */',
                'value'       => '0',
                'nextToken'   => T_STRING,
                'nextContent' => 'o',
            ],
        ];

    }//end dataExplicitOctalNotation()


}//end class
