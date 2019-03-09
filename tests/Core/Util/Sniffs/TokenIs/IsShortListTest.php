<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\TokenIs::isShortList() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\TokenIs;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\TokenIs;

class IsShortListTest extends AbstractMethodUnitTest
{


    /**
     * Test whether a T_OPEN_SHORT_ARRAY token is a short array or a short list.
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param bool             $expected    The expected boolean return value.
     * @param int|string|array $targetToken The token type(s) to test. Defaults to T_OPEN_SHORT_ARRAY.
     *
     * @dataProvider dataIsShortList
     * @covers       \PHP_CodeSniffer\Util\Sniffs\TokenIs::isShortList
     *
     * @return void
     */
    public function testIsShortList($testMarker, $expected, $targetToken=T_OPEN_SHORT_ARRAY)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);
        $result   = TokenIs::isShortList(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);

    }//end testIsShortList()


    /**
     * Data provider.
     *
     * @see testIsShortList()
     *
     * @return array
     */
    public function dataIsShortList()
    {
        return [
            [
                '/* testLongList */',
                false,
                \T_LIST,
            ],
            [
                '/* testArrayAssignment */',
                false,
                [
                    \T_OPEN_SHORT_ARRAY,
                    \T_OPEN_SQUARE_BRACKET,
                ],
            ],
            [
                '/* testNonNestedShortArray */',
                false,
            ],
            [
                '/* testNoAssignment */',
                false,
            ],
            [
                '/* testNestedNoAssignment */',
                false,
            ],
            [
                '/* testShortArrayInForeach */',
                false,
            ],
            [
                '/* testShortList */',
                true,
            ],
            [
                '/* testShortListDetectOnCloseBracket */',
                true,
                \T_CLOSE_SHORT_ARRAY,
            ],
            [
                '/* testShortListWithNesting */',
                true,
            ],
            [
                '/* testNestedShortList */',
                true,
            ],
            [
                '/* testShortListInForeach */',
                true,
            ],
            [
                '/* testShortListInForeachWithKey */',
                true,
            ],
            [
                '/* testShortListInForeachNested */',
                true,
            ],
            [
                '/* testMultiAssignShortlist */',
                true,
            ],
            [
                '/* testShortListWithKeys */',
                true,
            ],
            [
                '/* testShortListInForeachWithKeysDetectOnCloseBracket */',
                true,
                \T_CLOSE_SHORT_ARRAY,
            ],
            [
                '/* testNestedShortListEmpty */',
                true,
            ],
            [
                '/* testDeeplyNestedShortList */',
                true,
            ],
            [
                '/* testShortListWithNestingAndKeys */',
                true,
            ],
            [
                '/* testNestedShortListWithKeys_1 */',
                true,
            ],
            [
                '/* testNestedShortListWithKeys_2 */',
                true,
            ],
            [
                '/* testNestedShortListWithKeys_3 */',
                true,
            ],
            [
                '/* testShortListWithoutVars */',
                true,
            ],
            [
                '/* testShortListNestedLongList */',
                true,
            ],
            [
                '/* testNestedAnonClassWithTraitUseAs */',
                false,
            ],
            [
                '/* testParseError */',
                false,
            ],
            [
                '/* testLiveCoding */',
                false,
                [
                    \T_OPEN_SHORT_ARRAY,
                    \T_OPEN_SQUARE_BRACKET,
                ],
            ],
        ];

    }//end dataIsShortList()


}//end class
