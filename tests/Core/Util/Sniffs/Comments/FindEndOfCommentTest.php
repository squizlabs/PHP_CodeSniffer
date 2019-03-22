<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Comments::findEndOfComment() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Comments;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Comments;

class FindEndOfCommentTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a token which is not supported is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_COMMENT or T_DOC_COMMENT_OPEN_TAG
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findEndOfComment
     *
     * @return void
     */
    public function testNotACommentException()
    {
        $stackPtr = self::$phpcsFile->findNext(T_ECHO, 0);
        $result   = Comments::findEndOfComment(self::$phpcsFile, $stackPtr);

    }//end testNotACommentException()


    /**
     * Test receiving an expected exception when an inline comment token which is
     * not the *start* of the inline comment is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must point to the start of a comment
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findEndOfComment
     *
     * @return void
     */
    public function testNotStartOfAnInlineCommentException()
    {
        $stackPtr = self::$phpcsFile->findNext(
            T_COMMENT,
            0,
            null,
            false,
            '//line 2
'
        );
        $result   = Comments::findEndOfComment(self::$phpcsFile, $stackPtr);

    }//end testNotStartOfAnInlineCommentException()


    /**
     * Test receiving an expected exception when an inline comment token which is
     * not the *start* of a block comment is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must point to the start of a comment
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findEndOfComment
     *
     * @return void
     */
    public function testNotStartOfABlockCommentException()
    {
        $stackPtr = self::$phpcsFile->findPrevious(
            T_COMMENT,
            (self::$phpcsFile->numTokens - 1),
            null,
            false,
            '     * line 2
'
        );
        $result   = Comments::findEndOfComment(self::$phpcsFile, $stackPtr);

    }//end testNotStartOfABlockCommentException()


    /**
     * Test receiving an expected exception when an inline comment token which is
     * not the *start* of a block comment is passed and the comment contents starts with //.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must point to the start of a comment
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findEndOfComment
     *
     * @return void
     */
    public function testMixedInlineBlockCommentException()
    {
        $stackPtr = $this->getTargetToken(
            "'testBlockCommentSequence_13'",
            null,
            '    // line 2
'
        );
        $result   = Comments::findEndOfComment(self::$phpcsFile, $stackPtr);

    }//end testMixedInlineBlockCommentException()


    /**
     * Test correctly identifying the end of an inline or block comment.
     *
     * @param string      $delimiter    The text string delimiter to use to find the
     *                                  start of the comment.
     * @param int         $expected     The expected function return value as an offset
     *                                  from the start of the comment.
     * @param string|null $tokenContent Optional. Specific content to get the correct
     *                                  comment token.
     *
     * @dataProvider dataFindEndOfComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findEndOfComment
     *
     * @return void
     */
    public function testFindEndOfComment($delimiter, $expected, $tokenContent=null)
    {
        $stackPtr = $this->getTargetToken($delimiter, null, $tokenContent);

        // Expected end token position values are set as offsets in relation to
        // the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        $expected += $stackPtr;

        $result = Comments::findEndOfComment(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testFindEndOfComment()


    /**
     * Data provider.
     *
     * @see testFindEndOfComment()
     *
     * @return array
     */
    public function dataFindEndOfComment()
    {
        return [
            // Docblock.
            [
                "'testDocblockSequence'",
                8,
            ],

            // Inline comments.
            [
                "'testInlineCommentSequence_1'",
                0,
            ],
            [
                "'testInlineCommentSequence_2'",
                6,
            ],
            [
                "'testInlineCommentSequence_3'",
                4,
            ],
            [
                "'testInlineCommentSequence_4'",
                4,
            ],
            [
                "'testInlineCommentSequence_5'",
                4,
            ],
            [
                "'testInlineCommentSequence_6'",
                4,
            ],
            [
                "'testInlineCommentSequence_7'",
                0,
            ],
            [
                "'testInlineCommentSequence_7'",
                0,
                '// This starts a new inline comment
',
            ],
            [
                "'testInlineCommentSequence_8'",
                0,
                '/* Stand-alone block comment.*/',
            ],
            [
                "'testInlineCommentSequence_8'",
                0,
                '// Stand-alone inline trailing comment.
',
            ],
            [
                "'testInlineCommentSequence_8'",
                0,
                '// This starts a new inline comment
',
            ],
            [
                "'testInlineCommentSequence_9'",
                0,
                '// Stand alone inline comment.
',
            ],
            [
                "'testInlineCommentSequence_9'",
                0,
                '// Another stand alone inline comment.
',
            ],
            [
                "'testInlineCommentSequence_10'",
                2,
            ],
            [
                "'testInlineCommentSequence_10'",
                0,
                '# Perl-style comment not belonging to the sequence.
',
            ],
            [
                "'testInlineCommentSequence_11'",
                2,
            ],
            [
                "'testInlineCommentSequence_11'",
                0,
                '// Slash-style comment not belonging to the sequence.
',
            ],
            [
                "'testInlineCommentSequence_12'",
                4,
            ],

            // Block comments.
            [
                "'testBlockCommentSequence_1'",
                0,
            ],
            [
                "'testBlockCommentSequence_2'",
                0,
            ],
            [
                "'testBlockCommentSequence_3'",
                2,
            ],
            [
                "'testBlockCommentSequence_4'",
                4,
            ],
            [
                "'testBlockCommentSequence_5'",
                4,
            ],
            [
                "'testBlockCommentSequence_6'",
                5,
            ],
            [
                "'testBlockCommentSequence_7'",
                4,
            ],
            [
                "'testBlockCommentSequence_8'",
                3,
            ],
            [
                "'testBlockCommentSequence_9'",
                3,
            ],
            [
                "'testBlockCommentSequence_10'",
                2,
            ],
            [
                "'testBlockCommentSequence_11'",
                2,
            ],
            [
                "'testBlockCommentSequence_12'",
                0,
            ],
            [
                "'testBlockCommentSequence_13'",
                4,
            ],
            [
                "'testBlockCommentSequence_last'",
                3,
            ],
        ];

    }//end dataFindEndOfComment()


    /**
     * Helper method. Get the token pointer for a target token based on a specific text string.
     *
     * Overloading the parent method as we can't look for marker comments for these methods as they
     * would confuse the tests.
     *
     * @param string      $delimiter    The text string content to look for.
     * @param null        $notUsed      Parameter not used in this implementation of the method.
     * @param string|null $tokenContent Optional. Specific content to get the correct
     *                                  comment token.
     *
     * @return int
     */
    public function getTargetToken($delimiter, $notUsed, $tokenContent=null)
    {
        $tokens       = self::$phpcsFile->getTokens();
        $delimiterPtr = self::$phpcsFile->findPrevious(
            T_CONSTANT_ENCAPSED_STRING,
            (self::$phpcsFile->numTokens - 1),
            null,
            false,
            $delimiter
        );

        $target = self::$phpcsFile->findNext(
            [
                T_COMMENT,
                T_DOC_COMMENT_OPEN_TAG,
            ],
            ($delimiterPtr + 1),
            null,
            false,
            $tokenContent
        );

        if ($target === false) {
            $msg = 'Failed to find test target token for '.$delimiter;
            if (isset($tokenContent) === true) {
                $msg .= ' with content '.$tokenContent;
            }

            $this->assertFalse(true, $msg);
        }

        return $target;

    }//end getTargetToken()


}//end class
