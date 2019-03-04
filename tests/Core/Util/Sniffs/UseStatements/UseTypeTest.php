<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\UseStatements::isImportUse(),
 * \PHP_CodeSniffer\Util\Sniffs\UseStatements::isTraitUse(),
 * \PHP_CodeSniffer\Util\Sniffs\UseStatements::isClosureUse()
 * and \PHP_CodeSniffer\Util\Sniffs\UseStatements::getType() methods.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\UseStatements;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\UseStatements;

class UseTypeTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when passing a non-existent token pointer.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_USE
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\UseStatements::getType
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = UseStatements::getType(self::$phpcsFile, 100000);

    }//end testNonExistentToken()


    /**
     * Test receiving an expected exception when passing a non T_USE token.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_USE
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\UseStatements::getType
     *
     * @return void
     */
    public function testNonUseToken()
    {
        $result = UseStatements::getType(self::$phpcsFile, 0);

    }//end testNonUseToken()


    /**
     * Test correctly identifying whether a T_USE token is used as a closure use statement.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @dataProvider dataUseType
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::isClosureUse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::getType
     *
     * @return void
     */
    public function testIsClosureUse($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_USE);

        $result = UseStatements::isClosureUse(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['closure'], $result);

    }//end testIsClosureUse()


    /**
     * Test correctly identifying whether a T_USE token is used as an import use statement.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @dataProvider dataUseType
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::isImportUse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::getType
     *
     * @return void
     */
    public function testIsImportUse($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_USE);

        $result = UseStatements::isImportUse(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['import'], $result);

    }//end testIsImportUse()


    /**
     * Test correctly identifying whether a T_USE token is used as a trait import use statement.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @dataProvider dataUseType
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::isTraitUse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\UseStatements::getType
     *
     * @return void
     */
    public function testIsTraitUse($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, T_USE);

        $result = UseStatements::isTraitUse(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['trait'], $result, 'isTraitUseStatement() test failed');

    }//end testIsTraitUse()


    /**
     * Data provider.
     *
     * @see testIsClosureUse()
     * @see testIsImportUse()
     * @see testIsTraitUse()
     *
     * @return array
     */
    public function dataUseType()
    {
        return [
            [
                '/* testUseImport1 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            [
                '/* testUseImport2 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            [
                '/* testUseImport3 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            [
                '/* testUseImport4 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            [
                '/* testClosureUse */',
                [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            [
                '/* testUseTrait */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            [
                '/* testClosureUseNestedInClass */',
                [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            [
                '/* testUseTraitInNestedAnonClass */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            [
                '/* testUseTraitInTrait */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            [
                '/* testClosureUseNestedInTrait */',
                [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            [
                '/* testUseTraitInInterface */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            [
                '/* testLiveCoding */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
        ];

    }//end dataUseType()


}//end class
