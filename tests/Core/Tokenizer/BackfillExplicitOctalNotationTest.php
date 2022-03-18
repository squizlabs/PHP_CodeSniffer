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
     * @param array $testData The data required for the specific test case.
     *
     * @dataProvider dataExplicitOctalNotation
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testExplicitOctalNotation($testData)
    {
        $tokens = self::$phpcsFile->getTokens();

        $number = $this->getTargetToken($testData['marker'], [T_LNUMBER]);

        $this->assertSame(constant($testData['type']), $tokens[$number]['code']);
        $this->assertSame($testData['type'], $tokens[$number]['type']);
        $this->assertSame($testData['value'], $tokens[$number]['content']);

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
                [
                    'marker' => '/* testExplicitOctal */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0o137041',
                ],
            ],
            [
                [
                    'marker' => '/* testExplicitOctalCapitalised */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0O137041',
                ],
            ],
            [
                [
                    'marker' => '/* testExplicitOctalWithNumericSeparator */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0o137_041',
                ],
            ],
            [
                [
                    'marker' => '/* testInvalid1 */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0',
                ],
            ],
            [
                [
                    'marker' => '/* testInvalid2 */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0',
                ],
            ],
        ];

    }//end dataExplicitOctalNotation()


}//end class
