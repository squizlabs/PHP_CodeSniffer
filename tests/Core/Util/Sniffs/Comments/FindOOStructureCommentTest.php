<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\Comments::findOOStructureComment() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\Comments;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\Comments;
use PHP_CodeSniffer\Util\Tokens;

class FindOOStructureCommentTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a token which is not supported is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be a class, interface or trait token
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\Comments::findOOStructureComment
     *
     * @return void
     */
    public function testNotAnOOStructureTokenException()
    {
        $stackPtr = self::$phpcsFile->findNext(T_ANON_CLASS, 0);
        $result   = Comments::findOOStructureComment(self::$phpcsFile, $stackPtr);

    }//end testNotAnOOStructureTokenException()


    /**
     * Test correctly identifying a comment above an OO structure token.
     *
     * @param string           $OOName    The name of the OO structure for which to find the comment.
     * @param int|bool         $expected  The expected function return value.
     * @param int|string|array $tokenType Optional. The type of token(s) to look for.
     *
     * @dataProvider dataFindOOStructureComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findOOStructureComment
     * @covers       \PHP_CodeSniffer\Util\Sniffs\Comments::findCommentAbove
     *
     * @return void
     */
    public function testFindOOStructureComment($OOName, $expected, $tokenType=T_CLASS)
    {
        $stackPtr = $this->getTargetToken($OOName, $tokenType);

        // End token position values are set as offsets in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $result = Comments::findOOStructureComment(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);

    }//end testFindOOStructureComment()


    /**
     * Data provider.
     *
     * @see testFindOOStructureComment()
     *
     * @return array
     */
    public function dataFindOOStructureComment()
    {
        return [
            [
                'ClassNoDocblock',
                false,
            ],
            [
                'ClassNoDocblockTrailingCommentPrevious',
                false,
            ],
            [
                'ClassComment_1',
                -1,
            ],
            [
                'ClassComment_2',
                -2,
            ],
            [
                'ClassDocblock_1',
                -2,
            ],
            [
                'ClassDocblock_2',
                -7,
            ],
            [
                'ClassDocblock_3',
                -11,
            ],
            [
                'ClassNoDocblockWithInterlacedComments',
                false,
            ],
            [
                'InterfaceNoDocblock',
                false,
                T_INTERFACE,
            ],
            [
                'InterfaceComment',
                -2,
                T_INTERFACE,
            ],
            [
                'InterfaceDocblock',
                -2,
                T_INTERFACE,
            ],
            [
                'TraitNoDocblock',
                false,
                T_TRAIT,
            ],
            [
                'TraitComment',
                -1,
                T_TRAIT,
            ],
            [
                'TraitDocblock',
                -2,
                T_TRAIT,
            ],
            [
                'TraitParseError',
                false,
                T_TRAIT,
            ],
        ];

    }//end dataFindOOStructureComment()


    /**
     * Helper method. Get the token pointer for a target token based on a specific function name.
     *
     * Overloading the parent method as we can't look for marker comments for these methods as they
     * would confuse the tests.
     *
     * @param string           $OOName    The OO Structure name to look for.
     * @param int|string|array $tokenType The type of token(s) to look for.
     * @param null             $notUsed   Parameter not used in this implementation of the method.
     *
     * @return int
     */
    public function getTargetToken($OOName, $tokenType, $notUsed=null)
    {
        $tokens  = self::$phpcsFile->getTokens();
        $namePtr = self::$phpcsFile->findPrevious(
            T_STRING,
            (self::$phpcsFile->numTokens - 1),
            null,
            false,
            $OOName
        );
        $target  = self::$phpcsFile->findPrevious(Tokens::$emptyTokens, ($namePtr - 1), null, true);

        if ($target === false || $tokens[$target]['code'] !== $tokenType) {
            $this->assertFalse(true, 'Failed to find test target token for '.$OOName);
        }

        return $target;

    }//end getTargetToken()


}//end class
