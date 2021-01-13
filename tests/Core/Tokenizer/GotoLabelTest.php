<?php
/**
 * Tests the tokenization of goto declarations and statements.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class GotoLabelTest extends AbstractMethodUnitTest
{


    /**
     * Verify that the label in a goto statement is tokenized as T_STRING.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to expect.
     *
     * @dataProvider dataGotoStatement
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testGotoStatement($testMarker, $testContent)
    {
        $tokens = self::$phpcsFile->getTokens();

        $label = $this->getTargetToken($testMarker, T_STRING);

        $this->assertInternalType('int', $label);
        $this->assertSame($testContent, $tokens[$label]['content']);

    }//end testGotoStatement()


    /**
     * Data provider.
     *
     * @see testGotoStatement()
     *
     * @return array
     */
    public function dataGotoStatement()
    {
        return [
            [
                '/* testGotoStatement */',
                'marker',
            ],
            [
                '/* testGotoStatementInLoop */',
                'end',
            ],
        ];

    }//end dataGotoStatement()


    /**
     * Verify that the label in a goto declaration is tokenized as T_GOTO_LABEL.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to expect.
     *
     * @dataProvider dataGotoDeclaration
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testGotoDeclaration($testMarker, $testContent)
    {
        $tokens = self::$phpcsFile->getTokens();

        $label = $this->getTargetToken($testMarker, T_GOTO_LABEL);

        $this->assertInternalType('int', $label);
        $this->assertSame($testContent, $tokens[$label]['content']);

    }//end testGotoDeclaration()


    /**
     * Data provider.
     *
     * @see testGotoDeclaration()
     *
     * @return array
     */
    public function dataGotoDeclaration()
    {
        return [
            [
                '/* testGotoDeclaration */',
                'marker:',
            ],
            [
                '/* testGotoDeclarationOutsideLoop */',
                'end:',
            ],
        ];

    }//end dataGotoDeclaration()


    /**
     * Verify that the constant used in a switch - case statement is not confused with a goto label.
     *
     * @param string $testMarker  The comment prefacing the target token.
     * @param string $testContent The token content to expect.
     *
     * @dataProvider dataNotAGotoDeclaration
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNotAGotoDeclaration($testMarker, $testContent)
    {
        $tokens = self::$phpcsFile->getTokens();
        $target = $this->getTargetToken($testMarker, [T_GOTO_LABEL, T_STRING], $testContent);

        $this->assertSame(T_STRING, $tokens[$target]['code']);
        $this->assertSame('T_STRING', $tokens[$target]['type']);

    }//end testNotAGotoDeclaration()


    /**
     * Data provider.
     *
     * @see testNotAGotoDeclaration()
     *
     * @return array
     */
    public function dataNotAGotoDeclaration()
    {
        return [
            [
                '/* testNotGotoDeclarationGlobalConstant */',
                'CONSTANT',
            ],
            [
                '/* testNotGotoDeclarationNamespacedConstant */',
                'CONSTANT',
            ],
            [
                '/* testNotGotoDeclarationClassConstant */',
                'CONSTANT',
            ],
            [
                '/* testNotGotoDeclarationClassProperty */',
                'property',
            ],
            [
                '/* testNotGotoDeclarationGlobalConstantInTernary */',
                'CONST_A',
            ],
            [
                '/* testNotGotoDeclarationGlobalConstantInTernary */',
                'CONST_B',
            ],
        ];

    }//end dataNotAGotoDeclaration()


}//end class
