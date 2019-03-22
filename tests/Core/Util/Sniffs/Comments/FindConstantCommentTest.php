<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Comments::findConstantComment() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Comments;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Comments;
use PHP_CodeSniffer\Util\Tokens;

class FindConstantCommentTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a token which is not supported is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_CONST
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findConstantComment
     *
     * @return void
     */
    public function testNotAConstTokenException()
    {
        $stackPtr = self::$phpcsFile->findNext(
            T_STRING,
            0,
            null,
            false,
            'GLOBAL_CONST_NO_DOCBLOCK'
        );
        $result   = Comments::findConstantComment(self::$phpcsFile, $stackPtr);

    }//end testNotAConstTokenException()


    /**
     * Test correctly identifying a comment above a T_CONST token.
     *
     * @param string   $constName The name of the constant for which to find the comment.
     * @param int|bool $expected  The expected function return value.
     *
     * @dataProvider dataFindConstantComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findConstantComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findCommentAbove
     *
     * @return void
     */
    public function testFindConstantComment($constName, $expected)
    {
        $stackPtr = $this->getTargetToken($constName, T_CONST);

        // End token position values are set as offsets in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $result = Comments::findConstantComment(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testFindConstantComment()


    /**
     * Data provider.
     *
     * @see testFindConstantComment()
     *
     * @return array
     */
    public function dataFindConstantComment()
    {
        return [
            [
                'GLOBAL_CONST_NO_DOCBLOCK',
                false,
            ],
            [
                'GLOBAL_CONST_UNRELATED_COMMENT',
                false,
            ],
            [
                'GLOBAL_CONST_HAS_COMMENT_1',
                -1,
            ],
            [
                'GLOBAL_CONST_HAS_COMMENT_2',
                -1,
            ],
            [
                'GLOBAL_CONST_HAS_COMMENT_3',
                -2,
            ],
            [
                'GLOBAL_CONST_HAS_DOCBLOCK',
                -2,
            ],
            [
                'CLASS_CONST_NO_DOCBLOCK',
                false,
            ],
            [
                'CLASS_CONST_UNRELATED_COMMENT',
                false,
            ],
            [
                'CLASS_CONST_HAS_COMMENT_1',
                -2,
            ],
            [
                'CLASS_CONST_HAS_COMMENT_2',
                -2,
            ],
            [
                'CLASS_CONST_HAS_COMMENT_3',
                -3,
            ],
            [
                'CLASS_CONST_HAS_COMMENT_4',
                -3,
            ],
            [
                'CLASS_CONST_HAS_DOCBLOCK_1',
                -3,
            ],
            [
                'CLASS_CONST_HAS_DOCBLOCK_2',
                -7,
            ],
            [
                'CLASS_CONST_HAS_DOCBLOCK_3',
                -7,
            ],
            [
                'CLASS_CONST_HAS_DOCBLOCK_4',
                -7,
            ],
            [
                'CLASS_CONST_HAS_DOCBLOCK_5',
                -14,
            ],
        ];

    }//end dataFindConstantComment()


    /**
     * Helper method. Get the token pointer for a target token based on a specific constant name.
     *
     * Overloading the parent method as we can't look for marker comments for these methods as they
     * would confuse the tests.
     *
     * @param string           $constName The constant name to look for.
     * @param int|string|array $tokenType The type of token(s) to look for.
     * @param null             $notUsed   Parameter not used in this implementation of the method.
     *
     * @return int
     */
    public function getTargetToken($constName, $tokenType, $notUsed=null)
    {
        $tokens  = self::$phpcsFile->getTokens();
        $namePtr = self::$phpcsFile->findPrevious(
            T_STRING,
            (self::$phpcsFile->numTokens - 1),
            null,
            false,
            $constName
        );
        $target  = self::$phpcsFile->findPrevious(Tokens::$emptyTokens, ($namePtr - 1), null, true);

        if ($target === false || $tokens[$target]['code'] !== $tokenType) {
            $this->assertFalse(true, 'Failed to find test target token for '.$constName);
        }

        return $target;

    }//end getTargetToken()


}//end class
