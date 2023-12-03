<?php
/**
 * Tests that embedded variables and expressions in heredoc strings are tokenized
 * as one heredoc string token.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class HeredocStringTest extends AbstractMethodUnitTest
{


    /**
     * Test that heredoc strings contain the complete interpolated string.
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param string $expectedContent The expected content of the heredoc string.
     *
     * @dataProvider dataHeredocString
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testHeredocString($testMarker, $expectedContent)
    {
        $tokens = self::$phpcsFile->getTokens();

        $target = $this->getTargetToken($testMarker, T_HEREDOC);
        $this->assertSame($expectedContent."\n", $tokens[$target]['content']);

    }//end testHeredocString()


    /**
     * Test that heredoc strings contain the complete interpolated string when combined with other texts.
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param string $expectedContent The expected content of the heredoc string.
     *
     * @dataProvider dataHeredocString
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testHeredocStringWrapped($testMarker, $expectedContent)
    {
        $tokens = self::$phpcsFile->getTokens();

        $testMarker = substr($testMarker, 0, -3).'Wrapped */';
        $target     = $this->getTargetToken($testMarker, T_HEREDOC);
        $this->assertSame('Do '.$expectedContent." Something\n", $tokens[$target]['content']);

    }//end testHeredocStringWrapped()


    /**
     * Data provider.
     *
     * @see testHeredocString()
     *
     * @return array
     */
    public function dataHeredocString()
    {
        return [
            [
                'testMarker'      => '/* testSimple1 */',
                'expectedContent' => '$foo',
            ],
            [
                'testMarker'      => '/* testSimple2 */',
                'expectedContent' => '{$foo}',
            ],
            [
                'testMarker'      => '/* testSimple3 */',
                'expectedContent' => '${foo}',
            ],
            [
                'testMarker'      => '/* testDIM1 */',
                'expectedContent' => '$foo[bar]',
            ],
            [
                'testMarker'      => '/* testDIM2 */',
                'expectedContent' => '{$foo[\'bar\']}',
            ],
            [
                'testMarker'      => '/* testDIM3 */',
                'expectedContent' => '${foo[\'bar\']}',
            ],
            [
                'testMarker'      => '/* testProperty1 */',
                'expectedContent' => '$foo->bar',
            ],
            [
                'testMarker'      => '/* testProperty2 */',
                'expectedContent' => '{$foo->bar}',
            ],
            [
                'testMarker'      => '/* testMethod1 */',
                'expectedContent' => '{$foo->bar()}',
            ],
            [
                'testMarker'      => '/* testClosure1 */',
                'expectedContent' => '{$foo()}',
            ],
            [
                'testMarker'      => '/* testChain1 */',
                'expectedContent' => '{$foo[\'bar\']->baz()()}',
            ],
            [
                'testMarker'      => '/* testVariableVar1 */',
                'expectedContent' => '${$bar}',
            ],
            [
                'testMarker'      => '/* testVariableVar2 */',
                'expectedContent' => '${(foo)}',
            ],
            [
                'testMarker'      => '/* testVariableVar3 */',
                'expectedContent' => '${foo->bar}',
            ],
            [
                'testMarker'      => '/* testNested1 */',
                'expectedContent' => '${foo["${bar}"]}',
            ],
            [
                'testMarker'      => '/* testNested2 */',
                'expectedContent' => '${foo["${bar[\'baz\']}"]}',
            ],
            [
                'testMarker'      => '/* testNested3 */',
                'expectedContent' => '${foo->{$baz}}',
            ],
            [
                'testMarker'      => '/* testNested4 */',
                'expectedContent' => '${foo->{${\'a\'}}}',
            ],
            [
                'testMarker'      => '/* testNested5 */',
                'expectedContent' => '${foo->{"${\'a\'}"}}',
            ],
        ];

    }//end dataHeredocString()


}//end class
