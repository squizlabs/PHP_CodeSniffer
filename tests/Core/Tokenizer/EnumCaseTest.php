<?php
/**
 * Tests converting enum "case" to T_ENUM_CASE.
 *
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class EnumCaseTest extends AbstractMethodUnitTest
{


    /**
     * Test that the enum "case" is converted to T_ENUM_CASE.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataEnumCases
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testEnumCases($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $enumCase = $this->getTargetToken($testMarker, [T_ENUM_CASE, T_CASE]);

        $this->assertSame(T_ENUM_CASE, $tokens[$enumCase]['code']);
        $this->assertSame('T_ENUM_CASE', $tokens[$enumCase]['type']);

        $this->assertArrayNotHasKey('scope_condition', $tokens[$enumCase], 'Scope condition is set');
        $this->assertArrayNotHasKey('scope_opener', $tokens[$enumCase], 'Scope opener is set');
        $this->assertArrayNotHasKey('scope_closer', $tokens[$enumCase], 'Scope closer is set');

    }//end testEnumCases()


    /**
     * Data provider.
     *
     * @see testEnumCases()
     *
     * @return array
     */
    public function dataEnumCases()
    {
        return [
            ['/* testPureEnumCase */'],
            ['/* testBackingIntegerEnumCase */'],
            ['/* testBackingStringEnumCase */'],
            ['/* testEnumCaseInComplexEnum */'],
            ['/* testEnumCaseIsCaseInsensitive */'],
            ['/* testEnumCaseAfterSwitch */'],
            ['/* testEnumCaseAfterSwitchWithEndSwitch */'],
        ];

    }//end dataEnumCases()


    /**
     * Test that "case" that is not enum case is still tokenized as `T_CASE`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataNotEnumCases
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testNotEnumCases($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $case = $this->getTargetToken($testMarker, [T_ENUM_CASE, T_CASE]);

        $this->assertSame(T_CASE, $tokens[$case]['code']);
        $this->assertSame('T_CASE', $tokens[$case]['type']);

        $this->assertArrayHasKey('scope_condition', $tokens[$case], 'Scope condition is not set');
        $this->assertArrayHasKey('scope_opener', $tokens[$case], 'Scope opener is not set');
        $this->assertArrayHasKey('scope_closer', $tokens[$case], 'Scope closer is not set');

    }//end testNotEnumCases()


    /**
     * Data provider.
     *
     * @see testNotEnumCases()
     *
     * @return array
     */
    public function dataNotEnumCases()
    {
        return [
            ['/* testCaseWithSemicolonIsNotEnumCase */'],
            ['/* testCaseWithConstantIsNotEnumCase */'],
            ['/* testCaseWithConstantAndIdenticalIsNotEnumCase */'],
            ['/* testCaseWithAssigmentToConstantIsNotEnumCase */'],
            ['/* testIsNotEnumCaseIsCaseInsensitive */'],
            ['/* testCaseInSwitchWhenCreatingEnumInSwitch1 */'],
            ['/* testCaseInSwitchWhenCreatingEnumInSwitch2 */'],
        ];

    }//end dataNotEnumCases()


    /**
     * Test that "case" that is not enum case is still tokenized as `T_CASE`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataKeywordAsEnumCaseNameShouldBeString
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testKeywordAsEnumCaseNameShouldBeString($testMarker)
    {
        $tokens = self::$phpcsFile->getTokens();

        $enumCaseName = $this->getTargetToken($testMarker, [T_STRING, T_INTERFACE, T_TRAIT, T_ENUM, T_FUNCTION, T_FALSE, T_DEFAULT, T_ARRAY]);

        $this->assertSame(T_STRING, $tokens[$enumCaseName]['code']);
        $this->assertSame('T_STRING', $tokens[$enumCaseName]['type']);

    }//end testKeywordAsEnumCaseNameShouldBeString()


    /**
     * Data provider.
     *
     * @see testKeywordAsEnumCaseNameShouldBeString()
     *
     * @return array
     */
    public function dataKeywordAsEnumCaseNameShouldBeString()
    {
        return [
            ['/* testKeywordAsEnumCaseNameShouldBeString1 */'],
            ['/* testKeywordAsEnumCaseNameShouldBeString2 */'],
            ['/* testKeywordAsEnumCaseNameShouldBeString3 */'],
            ['/* testKeywordAsEnumCaseNameShouldBeString4 */'],
        ];

    }//end dataKeywordAsEnumCaseNameShouldBeString()


}//end class
