<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Comments::findFunctionComment() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Comments;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Comments;
use PHP_CodeSniffer\Util\Tokens;

class FindFunctionCommentTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a token which is not supported is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_FUNCTION
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findFunctionComment
     *
     * @return void
     */
    public function testNotAFunctionTokenException()
    {
        $stackPtr = self::$phpcsFile->findNext(
            T_STRING,
            0,
            null,
            false,
            'functionNoDocblock'
        );
        $result   = Comments::findFunctionComment(self::$phpcsFile, $stackPtr);

    }//end testNotAFunctionTokenException()


    /**
     * Test correctly identifying a comment above a T_FUNCTION token.
     *
     * @param string   $functionName The name of the function for which to find the comment.
     * @param int|bool $expected     The expected function return value.
     *
     * @dataProvider dataFindFunctionComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findFunctionComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findCommentAbove
     *
     * @return void
     */
    public function testFindFunctionComment($functionName, $expected)
    {
        $stackPtr = $this->getTargetToken($functionName, T_FUNCTION);

        // End token position values are set as offsets in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $result = Comments::findFunctionComment(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testFindFunctionComment()


    /**
     * Data provider.
     *
     * @see testFindFunctionComment()
     *
     * @return array
     */
    public function dataFindFunctionComment()
    {
        return [
            [
                'functionNoDocblock',
                false,
            ],
            [
                'functionUnrelatedComment',
                false,
            ],
            [
                'functionHasComment_1',
                -1,
            ],
            [
                'functionHasComment_2',
                -1,
            ],
            [
                'functionHasComment_3',
                -2,
            ],
            [
                'functionHasDocblock',
                -2,
            ],
            [
                'methodNoDocblock_1',
                false,
            ],
            [
                'methodUnrelatedComment',
                false,
            ],
            [
                'methodHasComment_1',
                -4,
            ],
            [
                'methodHasComment_2',
                -6,
            ],
            [
                'methodHasComment_3',
                -5,
            ],
            [
                'methodHasComment_4',
                -7,
            ],
            [
                'methodHasDocblock_1',
                -5,
            ],
            [
                'methodHasDocblock_2',
                -7,
            ],
            [
                'methodHasDocblock_3',
                -20,
            ],
            [
                'methodHasDocblock_4',
                -14,
            ],
            [
                'methodNoDocblock_2',
                false,
            ],
        ];

    }//end dataFindFunctionComment()


    /**
     * Helper method. Get the token pointer for a target token based on a specific function name.
     *
     * Overloading the parent method as we can't look for marker comments for these methods as they
     * would confuse the tests.
     *
     * @param string           $functionName The function name to look for.
     * @param int|string|array $tokenType    The type of token(s) to look for.
     * @param null             $notUsed      Parameter not used in this implementation of the method.
     *
     * @return int
     */
    public function getTargetToken($functionName, $tokenType, $notUsed=null)
    {
        $tokens  = self::$phpcsFile->getTokens();
        $namePtr = self::$phpcsFile->findPrevious(
            T_STRING,
            (self::$phpcsFile->numTokens - 1),
            null,
            false,
            $functionName
        );
        $target  = self::$phpcsFile->findPrevious(Tokens::$emptyTokens, ($namePtr - 1), null, true);

        if ($target === false || $tokens[$target]['code'] !== $tokenType) {
            $this->assertFalse(true, 'Failed to find test target token for '.$functionName);
        }

        return $target;

    }//end getTargetToken()


}//end class
