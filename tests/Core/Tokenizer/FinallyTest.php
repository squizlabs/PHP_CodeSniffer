<?php
/**
 * Tests the tokenization of the finally keyword.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class FinallyTest extends AbstractMethodUnitTest
{


    /**
     * Test that the finally keyword is tokenized as such.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataFinallyKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testFinallyKeyword($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, [T_FINALLY, T_STRING]);
        $this->assertSame(T_FINALLY, $tokens[$target]['code']);
        $this->assertSame('T_FINALLY', $tokens[$target]['type']);

    }//end testFinallyKeyword()


    /**
     * Data provider.
     *
     * @see testFinallyKeyword()
     *
     * @return array
     */
    public function dataFinallyKeyword()
    {
        return [
            ['/* testTryCatchFinally */'],
            ['/* testTryFinallyCatch */'],
            ['/* testTryFinally */'],
        ];

    }//end dataFinallyKeyword()


    /**
     * Test that 'finally' when not used as the reserved keyword is tokenized as `T_STRING`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataFinallyNonKeyword
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testFinallyNonKeyword($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, [T_FINALLY, T_STRING]);
        $this->assertSame(T_STRING, $tokens[$target]['code']);
        $this->assertSame('T_STRING', $tokens[$target]['type']);

    }//end testFinallyNonKeyword()


    /**
     * Data provider.
     *
     * @see testFinallyNonKeyword()
     *
     * @return array
     */
    public function dataFinallyNonKeyword()
    {
        return [
            ['/* testFinallyUsedAsClassConstantName */'],
            ['/* testFinallyUsedAsMethodName */'],
            ['/* testFinallyUsedAsPropertyName */'],
        ];

    }//end dataFinallyNonKeyword()


}//end class
