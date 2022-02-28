<?php
/**
 * Tests the backfilling of numeric separators to PHP < 7.4.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class BackfillNumericSeparatorTest extends AbstractMethodUnitTest
{


    /**
     * Test that numbers using numeric separators are tokenized correctly.
     *
     * @param array $testData The data required for the specific test case.
     *
     * @dataProvider dataTestBackfill
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testBackfill($testData)
    {
        $tokens = self::$phpcsFile->getTokens();
        $number = $this->getTargetToken($testData['marker'], [T_LNUMBER, T_DNUMBER]);

        $this->assertSame(constant($testData['type']), $tokens[$number]['code']);
        $this->assertSame($testData['type'], $tokens[$number]['type']);
        $this->assertSame($testData['value'], $tokens[$number]['content']);

    }//end testBackfill()


    /**
     * Data provider.
     *
     * @see testBackfill()
     *
     * @return array
     */
    public function dataTestBackfill()
    {
        $testHexType = 'T_LNUMBER';
        if (PHP_INT_MAX < 0xCAFEF00D) {
            $testHexType = 'T_DNUMBER';
        }

        $testHexMultipleType = 'T_LNUMBER';
        if (PHP_INT_MAX < 0x42726F776E) {
            $testHexMultipleType = 'T_DNUMBER';
        }

        $testIntMoreThanMaxType = 'T_LNUMBER';
        if (PHP_INT_MAX < 10223372036854775807) {
            $testIntMoreThanMaxType = 'T_DNUMBER';
        }

        return [
            [
                [
                    'marker' => '/* testSimpleLNumber */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '1_000_000_000',
                ],
            ],
            [
                [
                    'marker' => '/* testSimpleDNumber */',
                    'type'   => 'T_DNUMBER',
                    'value'  => '107_925_284.88',
                ],
            ],
            [
                [
                    'marker' => '/* testFloat */',
                    'type'   => 'T_DNUMBER',
                    'value'  => '6.674_083e-11',
                ],
            ],
            [
                [
                    'marker' => '/* testFloat2 */',
                    'type'   => 'T_DNUMBER',
                    'value'  => '6.674_083e+11',
                ],
            ],
            [
                [
                    'marker' => '/* testFloat3 */',
                    'type'   => 'T_DNUMBER',
                    'value'  => '1_2.3_4e1_23',
                ],
            ],
            [
                [
                    'marker' => '/* testHex */',
                    'type'   => $testHexType,
                    'value'  => '0xCAFE_F00D',
                ],
            ],
            [
                [
                    'marker' => '/* testHexMultiple */',
                    'type'   => $testHexMultipleType,
                    'value'  => '0x42_72_6F_77_6E',
                ],
            ],
            [
                [
                    'marker' => '/* testHexInt */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0x42_72_6F',
                ],
            ],
            [
                [
                    'marker' => '/* testBinary */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0b0101_1111',
                ],
            ],
            [
                [
                    'marker' => '/* testOctal */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0137_041',
                ],
            ],
            [
                [
                    'marker' => '/* testExplicitOctal */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0o137_041',
                ],
            ],
            [
                [
                    'marker' => '/* testExplicitOctalCapitalised */',
                    'type'   => 'T_LNUMBER',
                    'value'  => '0O137_041',
                ],
            ],
            [
                [
                    'marker' => '/* testIntMoreThanMax */',
                    'type'   => $testIntMoreThanMaxType,
                    'value'  => '10_223_372_036_854_775_807',
                ],
            ],
        ];

    }//end dataTestBackfill()


    /**
     * Test that numbers using numeric separators which are considered parse errors and/or
     * which aren't relevant to the backfill, do not incorrectly trigger the backfill anyway.
     *
     * @param string $testMarker     The comment which prefaces the target token in the test file.
     * @param array  $expectedTokens The token type and content of the expected token sequence.
     *
     * @dataProvider dataNoBackfill
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNoBackfill($testMarker, $expectedTokens)
    {
        $tokens = self::$phpcsFile->getTokens();
        $number = $this->getTargetToken($testMarker, [T_LNUMBER, T_DNUMBER]);

        foreach ($expectedTokens as $key => $expectedToken) {
            $i = ($number + $key);
            $this->assertSame($expectedToken['code'], $tokens[$i]['code']);
            $this->assertSame($expectedToken['content'], $tokens[$i]['content']);
        }

    }//end testNoBackfill()


    /**
     * Data provider.
     *
     * @see testBackfill()
     *
     * @return array
     */
    public function dataNoBackfill()
    {
        return [
            [
                '/* testInvalid1 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '100',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => '_',
                    ],
                ],
            ],
            [
                '/* testInvalid2 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '1',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => '__1',
                    ],
                ],
            ],
            [
                '/* testInvalid3 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '1',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => '_',
                    ],
                    [
                        'code'    => T_DNUMBER,
                        'content' => '.0',
                    ],
                ],
            ],
            [
                '/* testInvalid4 */',
                [
                    [
                        'code'    => T_DNUMBER,
                        'content' => '1.',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => '_0',
                    ],
                ],
            ],
            [
                '/* testInvalid5 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '0',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => 'x_123',
                    ],
                ],
            ],
            [
                '/* testInvalid6 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '0',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => 'b_101',
                    ],
                ],
            ],
            [
                '/* testInvalid7 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '1',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => '_e2',
                    ],
                ],
            ],
            [
                '/* testInvalid8 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '1',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => 'e_2',
                    ],
                ],
            ],
            [
                '/* testInvalid9 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '107_925_284',
                    ],
                    [
                        'code'    => T_WHITESPACE,
                        'content' => ' ',
                    ],
                    [
                        'code'    => T_DNUMBER,
                        'content' => '.88',
                    ],
                ],
            ],
            [
                '/* testInvalid10 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '107_925_284',
                    ],
                    [
                        'code'    => T_COMMENT,
                        'content' => '/*comment*/',
                    ],
                    [
                        'code'    => T_DNUMBER,
                        'content' => '.88',
                    ],
                ],
            ],
            [
                '/* testInvalid11 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '0',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => 'o_137',
                    ],
                ],
            ],
            [
                '/* testInvalid12 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '0',
                    ],
                    [
                        'code'    => T_STRING,
                        'content' => 'O_41',
                    ],
                ],
            ],
            [
                '/* testCalc1 */',
                [
                    [
                        'code'    => T_LNUMBER,
                        'content' => '667_083',
                    ],
                    [
                        'code'    => T_WHITESPACE,
                        'content' => ' ',
                    ],
                    [
                        'code'    => T_MINUS,
                        'content' => '-',
                    ],
                    [
                        'code'    => T_WHITESPACE,
                        'content' => ' ',
                    ],
                    [
                        'code'    => T_LNUMBER,
                        'content' => '11',
                    ],
                ],
            ],
            [
                '/* test Calc2 */',
                [
                    [
                        'code'    => T_DNUMBER,
                        'content' => '6.674_08e3',
                    ],
                    [
                        'code'    => T_WHITESPACE,
                        'content' => ' ',
                    ],
                    [
                        'code'    => T_PLUS,
                        'content' => '+',
                    ],
                    [
                        'code'    => T_WHITESPACE,
                        'content' => ' ',
                    ],
                    [
                        'code'    => T_LNUMBER,
                        'content' => '11',
                    ],
                ],
            ],
        ];

    }//end dataNoBackfill()


}//end class
