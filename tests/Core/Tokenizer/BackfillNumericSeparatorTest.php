<?php
/**
 * Tests the backfilling of numeric seperators to PHP < 7.4.
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
     * Test that numbers using numeric seperators are tokenized correctly.
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
        $number = $this->getTargetToken($testData['marker'], $testData['type']);
        $this->assertSame($tokens[$number]['content'], $testData['value']);

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
        return [
            [
                [
                    'marker' => '/* testSimpleLNumber */',
                    'type'   => T_LNUMBER,
                    'value'  => '1_000_000_000',
                ],
            ],
            [
                [
                    'marker' => '/* testSimpleDNumber */',
                    'type'   => T_DNUMBER,
                    'value'  => '107_925_284.88',
                ],
            ],
            [
                [
                    'marker' => '/* testFloat */',
                    'type'   => T_DNUMBER,
                    'value'  => '6.674_083e-11',
                ],
            ],
            [
                [
                    'marker' => '/* testHex */',
                    'type'   => T_LNUMBER,
                    'value'  => '0xCAFE_F00D',
                ],
            ],
            [
                [
                    'marker' => '/* testHexMultiple */',
                    'type'   => T_LNUMBER,
                    'value'  => '0x42_72_6F_77_6E',
                ],
            ],
            [
                [
                    'marker' => '/* testBinary */',
                    'type'   => T_LNUMBER,
                    'value'  => '0b0101_1111',
                ],
            ],
            [
                [
                    'marker' => '/* testOctal */',
                    'type'   => T_LNUMBER,
                    'value'  => '0137_041',
                ],
            ],
        ];

    }//end dataTestBackfill()


}//end class
