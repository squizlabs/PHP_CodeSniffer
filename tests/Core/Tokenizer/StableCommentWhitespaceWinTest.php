<?php
/**
 * Tests the comment tokenization with Windows line endings.
 *
 * Basically the same as the StableCommentWhitespaceTest, but now for
 * Windows line endings.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Tokens;

class StableCommentWhitespaceWinTest extends AbstractMethodUnitTest
{


    /**
     * Test that comment tokenization with new lines at the end of the comment is stable.
     *
     * @param string $testMarker     The comment prefacing the test.
     * @param array  $expectedTokens The tokenization expected.
     *
     * @dataProvider dataCommentTokenization
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testCommentTokenization($testMarker, $expectedTokens)
    {
        $tokens  = self::$phpcsFile->getTokens();
        $comment = $this->getTargetToken($testMarker, Tokens::$commentTokens);

        foreach ($expectedTokens as $key => $tokenInfo) {
            $this->assertSame(constant($tokenInfo['type']), $tokens[$comment]['code']);
            $this->assertSame($tokenInfo['type'], $tokens[$comment]['type']);
            $this->assertSame($tokenInfo['content'], $tokens[$comment]['content']);

            ++$comment;
        }

    }//end testCommentTokenization()


    /**
     * Data provider.
     *
     * @see testCommentTokenization()
     *
     * @return array
     */
    public function dataCommentTokenization()
    {
        return [
            [
                '/* testSingleLineSlashComment */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineSlashCommentTrailing */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineSlashAnnotation */',
                [
                    [
                        'type'    => 'T_PHPCS_DISABLE',
                        'content' => '// phpcs:disable Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineSlashComment */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineSlashCommentWithIndent */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineSlashCommentWithAnnotationStart */',
                [
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineSlashCommentWithAnnotationMiddle */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// @phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment3
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineSlashCommentWithAnnotationEnd */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Comment2
',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineSlashCommentNoNewLineAtEnd */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '// Slash ',
                    ],
                    [
                        'type'    => 'T_CLOSE_TAG',
                        'content' => '?>
',
                    ],
                ],
            ],
            [
                '/* testCommentAtEndOfFile */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Comment',
                    ],
                ],
            ],
        ];

    }//end dataCommentTokenization()


}//end class
