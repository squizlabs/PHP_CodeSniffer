<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\TextStrings::getCompleteTextString() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\TextStrings;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\TextStrings;

class GetCompleteTextStringTest extends AbstractMethodUnitTest
{

    /**
     * Token types to target for these tests.
     *
     * @var array
     */
    private $targets = [
        T_START_HEREDOC,
        T_START_NOWDOC,
        T_CONSTANT_ENCAPSED_STRING,
        T_DOUBLE_QUOTED_STRING,
    ];


    /**
     * Test receiving an expected exception when a non text string is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING or T_DOUBLE_QUOTED_STRING
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\TextStrings::getCompleteTextString
     *
     * @return void
     */
    public function testNotATextStringException()
    {
        $next   = $this->getTargetToken('/* testNotATextString */', T_RETURN);
        $result = TextStrings::getCompleteTextString(self::$phpcsFile, $next);

    }//end testNotATextStringException()


    /**
     * Test receiving an expected exception when a text string token is not the first token
     * of a multi-line text string.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be the start of the text string
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\TextStrings::getCompleteTextString
     *
     * @return void
     */
    public function testNotFirstTextStringException()
    {
        $next   = $this->getTargetToken(
            '/* testNotFirstTextStringToken */',
            T_CONSTANT_ENCAPSED_STRING,
            'second line
'
        );
        $result = TextStrings::getCompleteTextString(self::$phpcsFile, $next);

    }//end testNotFirstTextStringException()


    /**
     * Test correctly retrieving the contents of a (potentially) multi-line text string.
     *
     * @param string $testMarker         The comment which prefaces the target token in the test file.
     * @param string $expected           The expected function return value.
     * @param string $expectedWithQuotes The expected function return value when $stripQuotes is set to "false".
     *
     * @dataProvider dataGetCompleteTextString
     * @covers       \PHP_CodeSniffer\Util\Sniffs\TextStrings::getCompleteTextString
     *
     * @return void
     */
    public function testGetCompleteTextString($testMarker, $expected, $expectedWithQuotes)
    {
        $stackPtr = $this->getTargetToken($testMarker, $this->targets);

        $result = TextStrings::getCompleteTextString(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

        $result = TextStrings::getCompleteTextString(self::$phpcsFile, $stackPtr, false);
        $this->assertSame($expectedWithQuotes, $result);

    }//end testGetCompleteTextString()


    /**
     * Data provider.
     *
     * @see testGetCompleteTextString()
     *
     * @return array
     */
    public function dataGetCompleteTextString()
    {
        return [
            [
                '/* testSingleLineConstantEncapsedString */',
                'single line text string',
                "'single line text string'",
            ],
            [
                '/* testMultiLineConstantEncapsedString */',
                'first line
second line
third line
fourth line',
                '"first line
second line
third line
fourth line"',
            ],
            [
                '/* testSingleLineDoubleQuotedString */',
                'single $line text string',
                '"single $line text string"',
            ],
            [
                '/* testMultiLineDoubleQuotedString */',
                'first line
second $line
third line
fourth line',
                '"first line
second $line
third line
fourth line"',
            ],
            [
                '/* testHeredocString */',
                'first line
second $line
third line
fourth line
',
                'first line
second $line
third line
fourth line
',
            ],
            [
                '/* testNowdocString */',
                'first line
second line
third line
fourth line
',
                'first line
second line
third line
fourth line
',
            ],
        ];

    }//end dataGetCompleteTextString()


}//end class
