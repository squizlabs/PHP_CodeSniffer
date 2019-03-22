<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Comments::findPropertyComment() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Comments;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Comments;

class FindPropertyCommentTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a token which is not supported is passed.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findCommentAbove
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Comments::findCommentAbove(self::$phpcsFile, 100000));

    }//end testNonExistentToken()


    /**
     * Test receiving an expected exception when a non-variable token is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be an OO property
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findPropertyComment
     *
     * @return void
     */
    public function testNotAnOOPropertyTokenExceptionNotVar()
    {
        $stackPtr = self::$phpcsFile->findNext(T_CLASS, 0);
        $result   = Comments::findPropertyComment(self::$phpcsFile, $stackPtr);

    }//end testNotAnOOPropertyTokenExceptionNotVar()


    /**
     * Test receiving an expected exception when a variable token which is not an OO property is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be an OO property
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findPropertyComment
     *
     * @return void
     */
    public function testNotAnOOPropertyTokenExceptionGlobalVar()
    {
        $stackPtr = self::$phpcsFile->findNext(
            T_VARIABLE,
            0,
            null,
            false,
            '$notAProperty'
        );
        $result   = Comments::findPropertyComment(self::$phpcsFile, $stackPtr);

    }//end testNotAnOOPropertyTokenExceptionGlobalVar()


    /**
     * Test receiving an expected exception when a variable token which is a method parameter, not
     * a property, is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be an OO property
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findPropertyComment
     *
     * @return void
     */
    public function testNotAnOOPropertyTokenExceptionParam()
    {
        $stackPtr = self::$phpcsFile->findNext(
            T_VARIABLE,
            0,
            null,
            false,
            '$paramNotProperty'
        );
        $result   = Comments::findPropertyComment(self::$phpcsFile, $stackPtr);

    }//end testNotAnOOPropertyTokenExceptionParam()


    /**
     * Test correctly identifying a comment above an OO property T_VARIABLE token.
     *
     * @param string   $propertyName The name of the property for which to find the comment.
     * @param int|bool $expected     The expected function return value.
     *
     * @dataProvider dataFindPropertyComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findPropertyComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findCommentAbove
     *
     * @return void
     */
    public function testFindPropertyComment($propertyName, $expected)
    {
        $stackPtr = $this->getTargetToken($propertyName);

        // End token position values are set as offsets in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $result = Comments::findPropertyComment(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testFindPropertyComment()


    /**
     * Data provider.
     *
     * @see testFindPropertyComment()
     *
     * @return array
     */
    public function dataFindPropertyComment()
    {
        return [
            [
                '$propertyNoDocblock',
                false,
            ],
            [
                '$propertyStaticHadDocblock',
                -5,
            ],
            [
                '$propertyNoDocblockTrailingComment',
                false,
            ],
            [
                '$propertyMultiLineComment',
                -5,
            ],
            [
                '$propertyInlineComment',
                -4,
            ],
            [
                '$propertyHadDocblockAndWhitespace',
                -9,
            ],
            [
                '$propertyHasDocblockInterlacedComments',
                -14,
            ],
        ];

    }//end dataFindPropertyComment()


    /**
     * Helper method. Get the token pointer for a target token based on a specific property name.
     *
     * Overloading the parent method as we can't look for marker comments for these methods as they
     * would confuse the tests.
     *
     * @param string $propertyName The property name to look for.
     * @param null   $notUsed1     Parameter not used in this implementation of the method.
     * @param null   $notUsed2     Parameter not used in this implementation of the method.
     *
     * @return int
     */
    public function getTargetToken($propertyName, $notUsed1=null, $notUsed2=null)
    {
        $tokens = self::$phpcsFile->getTokens();
        $target = self::$phpcsFile->findPrevious(
            T_VARIABLE,
            (self::$phpcsFile->numTokens - 1),
            null,
            false,
            $propertyName
        );

        if ($target === false) {
            $this->assertFalse(true, 'Failed to find test target token for '.$propertyName);
        }

        return $target;

    }//end getTargetToken()


}//end class
