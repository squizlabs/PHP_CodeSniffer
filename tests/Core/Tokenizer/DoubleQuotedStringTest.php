<?php
/**
 * Tests that embedded variables and expressions in double quoted strings are tokenized
 * as one double quoted string token.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class DoubleQuotedStringTest extends AbstractMethodUnitTest
{


    /**
     * Test that double quoted strings contain the complete string.
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param string $expectedContent The expected content of the double quoted string.
     *
     * @dataProvider dataDoubleQuotedString
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testDoubleQuotedString($testMarker, $expectedContent)
    {
        $tokens = self::$phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, T_DOUBLE_QUOTED_STRING);
        $this->assertSame($expectedContent, $tokens[$target]['content']);

    }//end testDoubleQuotedString()


    /**
     * Data provider.
     *
     * @see testDoubleQuotedString()
     *
     * @return array
     */
    public function dataDoubleQuotedString()
    {
        return [
            [
                'testMarker'      => '/* testSimple1 */',
                'expectedContent' => '"$foo"',
            ],
            [
                'testMarker'      => '/* testSimple2 */',
                'expectedContent' => '"{$foo}"',
            ],
            [
                'testMarker'      => '/* testSimple3 */',
                'expectedContent' => '"${foo}"',
            ],
            [
                'testMarker'      => '/* testDIM1 */',
                'expectedContent' => '"$foo[bar]"',
            ],
            [
                'testMarker'      => '/* testDIM2 */',
                'expectedContent' => '"{$foo[\'bar\']}"',
            ],
            [
                'testMarker'      => '/* testDIM3 */',
                'expectedContent' => '"${foo[\'bar\']}"',
            ],
            [
                'testMarker'      => '/* testProperty1 */',
                'expectedContent' => '"$foo->bar"',
            ],
            [
                'testMarker'      => '/* testProperty2 */',
                'expectedContent' => '"{$foo->bar}"',
            ],
            [
                'testMarker'      => '/* testMethod1 */',
                'expectedContent' => '"{$foo->bar()}"',
            ],
            [
                'testMarker'      => '/* testClosure1 */',
                'expectedContent' => '"{$foo()}"',
            ],
            [
                'testMarker'      => '/* testChain1 */',
                'expectedContent' => '"{$foo[\'bar\']->baz()()}"',
            ],
            [
                'testMarker'      => '/* testVariableVar1 */',
                'expectedContent' => '"${$bar}"',
            ],
            [
                'testMarker'      => '/* testVariableVar2 */',
                'expectedContent' => '"${(foo)}"',
            ],
            [
                'testMarker'      => '/* testVariableVar3 */',
                'expectedContent' => '"${foo->bar}"',
            ],
            [
                'testMarker'      => '/* testNested1 */',
                'expectedContent' => '"${foo["${bar}"]}"',
            ],
            [
                'testMarker'      => '/* testNested2 */',
                'expectedContent' => '"${foo["${bar[\'baz\']}"]}"',
            ],
            [
                'testMarker'      => '/* testNested3 */',
                'expectedContent' => '"${foo->{$baz}}"',
            ],
            [
                'testMarker'      => '/* testNested4 */',
                'expectedContent' => '"${foo->{${\'a\'}}}"',
            ],
            [
                'testMarker'      => '/* testNested5 */',
                'expectedContent' => '"${foo->{"${\'a\'}"}}"',
            ],
            [
                'testMarker'      => '/* testParseError */',
                'expectedContent' => '"${foo["${bar
',
            ],
        ];

    }//end dataDoubleQuotedString()


}//end class
