<?php
/**
 * Tests the support of PHP 8.1 "enum" keyword.
 *
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class BackfillEnumTest extends AbstractMethodUnitTest
{


    /**
     * Test that the "enum" keyword is tokenized as such.
     *
     * @param string $testMarker   The comment which prefaces the target token in the test file.
     * @param string $testContent  The token content to look for.
     * @param int    $openerOffset Offset to find expected scope opener.
     * @param int    $closerOffset Offset to find expected scope closer.
     *
     * @dataProvider dataEnums
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testEnums($testMarker, $testContent, $openerOffset, $closerOffset)
    {
        $tokens = self::$phpcsFile->getTokens();

        $enum = $this->getTargetToken($testMarker, [T_ENUM, T_STRING], $testContent);

        $this->assertSame(T_ENUM, $tokens[$enum]['code']);
        $this->assertSame('T_ENUM', $tokens[$enum]['type']);

        $this->assertArrayHasKey('scope_condition', $tokens[$enum]);
        $this->assertArrayHasKey('scope_opener', $tokens[$enum]);
        $this->assertArrayHasKey('scope_closer', $tokens[$enum]);

        $this->assertSame($enum, $tokens[$enum]['scope_condition']);

        $scopeOpener = $tokens[$enum]['scope_opener'];
        $scopeCloser = $tokens[$enum]['scope_closer'];

        $expectedScopeOpener = ($enum + $openerOffset);
        $expectedScopeCloser = ($enum + $closerOffset);

        $this->assertSame($expectedScopeOpener, $scopeOpener);
        $this->assertArrayHasKey('scope_condition', $tokens[$scopeOpener]);
        $this->assertArrayHasKey('scope_opener', $tokens[$scopeOpener]);
        $this->assertArrayHasKey('scope_closer', $tokens[$scopeOpener]);
        $this->assertSame($enum, $tokens[$scopeOpener]['scope_condition']);
        $this->assertSame($scopeOpener, $tokens[$scopeOpener]['scope_opener']);
        $this->assertSame($scopeCloser, $tokens[$scopeOpener]['scope_closer']);

        $this->assertSame($expectedScopeCloser, $scopeCloser);
        $this->assertArrayHasKey('scope_condition', $tokens[$scopeCloser]);
        $this->assertArrayHasKey('scope_opener', $tokens[$scopeCloser]);
        $this->assertArrayHasKey('scope_closer', $tokens[$scopeCloser]);
        $this->assertSame($enum, $tokens[$scopeCloser]['scope_condition']);
        $this->assertSame($scopeOpener, $tokens[$scopeCloser]['scope_opener']);
        $this->assertSame($scopeCloser, $tokens[$scopeCloser]['scope_closer']);

    }//end testEnums()


    /**
     * Data provider.
     *
     * @see testEnums()
     *
     * @return array
     */
    public function dataEnums()
    {
        return [
            [
                '/* testPureEnum */',
                'enum',
                4,
                12,
            ],
            [
                '/* testBackedIntEnum */',
                'enum',
                7,
                29,
            ],
            [
                '/* testBackedStringEnum */',
                'enum',
                8,
                30,
            ],
            [
                '/* testComplexEnum */',
                'enum',
                11,
                72,
            ],
            [
                '/* testEnumWithEnumAsClassName */',
                'enum',
                6,
                7,
            ],
            [
                '/* testEnumIsCaseInsensitive */',
                'EnUm',
                4,
                5,
            ],
            [
                '/* testDeclarationContainingComment */',
                'enum',
                6,
                14,
            ],
        ];

    }//end dataEnums()


    /**
     * Test that "enum" when not used as the keyword is still tokenized as `T_STRING`.
     *
     * @param string $testMarker  The comment which prefaces the target token in the test file.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotEnums
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNotEnums($testMarker, $testContent)
    {
        $tokens = self::$phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, [T_ENUM, T_STRING], $testContent);
        $this->assertSame(T_STRING, $tokens[$target]['code']);
        $this->assertSame('T_STRING', $tokens[$target]['type']);

    }//end testNotEnums()


    /**
     * Data provider.
     *
     * @see testNotEnums()
     *
     * @return array
     */
    public function dataNotEnums()
    {
        return [
            [
                '/* testEnumAsClassNameAfterEnumKeyword */',
                'Enum',
            ],
            [
                '/* testEnumUsedAsClassName */',
                'Enum',
            ],
            [
                '/* testEnumUsedAsClassConstantName */',
                'ENUM',
            ],
            [
                '/* testEnumUsedAsMethodName */',
                'enum',
            ],
            [
                '/* testEnumUsedAsPropertyName */',
                'enum',
            ],
            [
                '/* testEnumUsedAsFunctionName */',
                'enum',
            ],
            [
                '/* testEnumUsedAsEnumName */',
                'Enum',
            ],
            [
                '/* testEnumUsedAsNamespaceName */',
                'Enum',
            ],
            [
                '/* testEnumUsedAsPartOfNamespaceName */',
                'Enum',
            ],
            [
                '/* testEnumUsedInObjectInitialization */',
                'Enum',
            ],
            [
                '/* testEnumAsFunctionCall */',
                'enum',
            ],
            [
                '/* testEnumAsFunctionCallWithNamespace */',
                'enum',
            ],
            [
                '/* testClassConstantFetchWithEnumAsClassName */',
                'Enum',
            ],
            [
                '/* testClassConstantFetchWithEnumAsConstantName */',
                'ENUM',
            ],
            [
                '/* testParseErrorMissingName */',
                'enum',
            ],
            [
                '/* testParseErrorLiveCoding */',
                'enum',
            ],
        ];

    }//end dataNotEnums()


}//end class
