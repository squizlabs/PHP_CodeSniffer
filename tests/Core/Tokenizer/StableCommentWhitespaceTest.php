<?php
/**
 * Tests the comment tokenization.
 *
 * Comment have their own tokenization in PHPCS anyhow, including the PHPCS annotations.
 * However, as of PHP 8, the PHP native comment tokenization has changed.
 * Natively T_COMMENT tokens will no longer include a trailing newline.
 * PHPCS "forward-fills" the original tokenization to PHP 8.
 * This test file safeguards that.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Tokens;

class StableCommentWhitespaceTest extends AbstractMethodUnitTest
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
                '/* testSingleLineStarComment */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Single line star comment */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineStarCommentTrailing */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Comment */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineStarAnnotation */',
                [
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '/* phpcs:ignore Stnd.Cat */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineStarComment */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => ' * Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => ' * Comment3 */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineStarCommentWithIndent */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '         * Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '         * Comment3 */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineStarCommentWithAnnotationStart */',
                [
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '/* @phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => ' * Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => ' * Comment3 */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineStarCommentWithAnnotationMiddle */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Comment1
',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => ' * phpcs:ignore Stnd.Cat
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => ' * Comment3 */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineStarCommentWithAnnotationEnd */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => ' * Comment2
',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => ' * phpcs:ignore Stnd.Cat */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],

            [
                '/* testSingleLineDocblockComment */',
                [
                    [
                        'type'    => 'T_DOC_COMMENT_OPEN_TAG',
                        'content' => '/**',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_CLOSE_TAG',
                        'content' => '*/',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineDocblockCommentTrailing */',
                [
                    [
                        'type'    => 'T_DOC_COMMENT_OPEN_TAG',
                        'content' => '/**',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_CLOSE_TAG',
                        'content' => '*/',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineDocblockAnnotation */',
                [
                    [
                        'type'    => 'T_DOC_COMMENT_OPEN_TAG',
                        'content' => '/**',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => 'phpcs:ignore Stnd.Cat.Sniff ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_CLOSE_TAG',
                        'content' => '*/',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],

            [
                '/* testMultiLineDocblockComment */',
                [
                    [
                        'type'    => 'T_DOC_COMMENT_OPEN_TAG',
                        'content' => '/**',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment1',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment2',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_TAG',
                        'content' => '@tag',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_CLOSE_TAG',
                        'content' => '*/',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineDocblockCommentWithIndent */',
                [
                    [
                        'type'    => 'T_DOC_COMMENT_OPEN_TAG',
                        'content' => '/**',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '     ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment1',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '     ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment2',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '     ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '     ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_TAG',
                        'content' => '@tag',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '     ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_CLOSE_TAG',
                        'content' => '*/',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineDocblockCommentWithAnnotation */',
                [
                    [
                        'type'    => 'T_DOC_COMMENT_OPEN_TAG',
                        'content' => '/**',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => 'phpcs:ignore Stnd.Cat',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_TAG',
                        'content' => '@tag',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_CLOSE_TAG',
                        'content' => '*/',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testMultiLineDocblockCommentWithTagAnnotation */',
                [
                    [
                        'type'    => 'T_DOC_COMMENT_OPEN_TAG',
                        'content' => '/**',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '@phpcs:ignore Stnd.Cat',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STAR',
                        'content' => '*',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_TAG',
                        'content' => '@tag',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_STRING',
                        'content' => 'Comment',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_DOC_COMMENT_CLOSE_TAG',
                        'content' => '*/',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            [
                '/* testSingleLineHashComment */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment
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
                '/* testSingleLineHashCommentTrailing */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment
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
                '/* testMultiLineHashComment */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment1
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment2
',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment3
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
                '/* testMultiLineHashCommentWithIndent */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment1
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment2
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Comment3
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
                '/* testSingleLineHashCommentNoNewLineAtEnd */',
                [
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '# Hash ',
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
