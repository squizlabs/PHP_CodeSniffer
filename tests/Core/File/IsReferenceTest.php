<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:isReference method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class IsReferenceTest extends AbstractMethodUnitTest
{


    /**
     * Test a class that extends another.
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataIsReference
     *
     * @return void
     */
    public function testIsReference($identifier, $expected)
    {
        $start      = (self::$phpcsFile->numTokens - 1);
        $delim      = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            $identifier
        );
        $bitwiseAnd = self::$phpcsFile->findNext(T_BITWISE_AND, ($delim + 1));

        $result = self::$phpcsFile->isReference($bitwiseAnd);
        $this->assertSame($expected, $result);

    }//end testIsReference()


    /**
     * Data provider for the IsReference test.
     *
     * @see testIsReference()
     *
     * @return array
     */
    public function dataIsReference()
    {
        return [
            [
                '/* bitwiseAndA */',
                false,
            ],
            [
                '/* bitwiseAndB */',
                false,
            ],
            [
                '/* bitwiseAndC */',
                false,
            ],
            [
                '/* bitwiseAndD */',
                false,
            ],
            [
                '/* bitwiseAndE */',
                false,
            ],
            [
                '/* bitwiseAndF */',
                false,
            ],
            [
                '/* bitwiseAndG */',
                false,
            ],
            [
                '/* bitwiseAndH */',
                false,
            ],
            [
                '/* bitwiseAndI */',
                false,
            ],
            [
                '/* functionReturnByReference */',
                true,
            ],
            [
                '/* functionPassByReferenceA */',
                true,
            ],
            [
                '/* functionPassByReferenceB */',
                true,
            ],
            [
                '/* functionPassByReferenceC */',
                true,
            ],
            [
                '/* functionPassByReferenceD */',
                true,
            ],
            [
                '/* functionPassByReferenceE */',
                true,
            ],
            [
                '/* functionPassByReferenceF */',
                true,
            ],
            [
                '/* functionPassByReferenceG */',
                true,
            ],
            [
                '/* foreachValueByReference */',
                true,
            ],
            [
                '/* foreachKeyByReference */',
                true,
            ],
            [
                '/* arrayValueByReferenceA */',
                true,
            ],
            [
                '/* arrayValueByReferenceB */',
                true,
            ],
            [
                '/* arrayValueByReferenceC */',
                true,
            ],
            [
                '/* arrayValueByReferenceD */',
                true,
            ],
            [
                '/* arrayValueByReferenceE */',
                true,
            ],
            [
                '/* arrayValueByReferenceF */',
                true,
            ],
            [
                '/* arrayValueByReferenceG */',
                true,
            ],
            [
                '/* arrayValueByReferenceH */',
                true,
            ],
            [
                '/* assignByReferenceA */',
                true,
            ],
            [
                '/* assignByReferenceB */',
                true,
            ],
            [
                '/* assignByReferenceC */',
                true,
            ],
            [
                '/* assignByReferenceD */',
                true,
            ],
            [
                '/* assignByReferenceE */',
                true,
            ],
            [
                '/* passByReferenceA */',
                true,
            ],
            [
                '/* passByReferenceB */',
                true,
            ],
            [
                '/* passByReferenceC */',
                true,
            ],
            [
                '/* passByReferenceD */',
                true,
            ],
            [
                '/* passByReferenceE */',
                true,
            ],
            [
                '/* passByReferenceF */',
                true,
            ],
            [
                '/* passByReferenceG */',
                true,
            ],
            [
                '/* passByReferenceH */',
                true,
            ],
            [
                '/* passByReferenceI */',
                true,
            ],
            [
                '/* passByReferenceJ */',
                true,
            ],
            [
                '/* newByReferenceA */',
                true,
            ],
            [
                '/* newByReferenceB */',
                true,
            ],
            [
                '/* useByReference */',
                true,
            ],
        ];

    }//end dataIsReference()


}//end class
