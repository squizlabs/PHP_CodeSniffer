<?php
/**
 * Tests the backfilling of the T_FN token to PHP < 7.4.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class BackfillFnTokenTest extends AbstractMethodUnitTest
{


    /**
     * Data provider.
     *
     * @see testBackill()
     *
     * @return array
     */
    public function dataBackfill()
    {
        return [
            ['/* testStandard */'],
            ['/* testMixedCase */'],
            ['/* testWhitespace */'],
            ['/* testComment */'],
            ['/* testFunctionName */'],
        ];

    }//end dataBackfill()


    /**
     * Test that anonymous class tokens without parenthesis do not get assigned a parenthesis owner.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataBackfill
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::processAdditional
     *
     * @return void
     */
    public function testBackfill($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $token = $this->getTargetToken($testMarker, T_FN);
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$token]), 'Parenthesis owner is not set');
        $this->assertTrue(array_key_exists('parenthesis_opener', $tokens[$token]), 'Parenthesis opener is not set');
        $this->assertTrue(array_key_exists('parenthesis_closer', $tokens[$token]), 'Parenthesis closer is not set');
        $this->assertSame($tokens[$token]['parenthesis_owner'], $token, 'Parenthesis owner is not the T_FN token');

        $opener = $tokens[$token]['parenthesis_opener'];
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$opener]), 'Opening parenthesis owner is not set');
        $this->assertSame($tokens[$opener]['parenthesis_owner'], $token, 'Opening parenthesis owner is not the T_FN token');

        $closer = $tokens[$token]['parenthesis_closer'];
        $this->assertTrue(array_key_exists('parenthesis_owner', $tokens[$closer]), 'Closing parenthesis owner is not set');
        $this->assertSame($tokens[$closer]['parenthesis_owner'], $token, 'Closing parenthesis owner is not the T_FN token');

    }//end testBackfill()


}//end class
