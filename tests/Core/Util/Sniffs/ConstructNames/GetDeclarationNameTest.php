<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ConstructNames::getDeclarationName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ConstructNames;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\ConstructNames;

class GetDeclarationNameTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage Token type "T_ECHO" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::getDeclarationName
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $interface = $this->getTargetToken('/* testInvalidTokenPassed */', T_ECHO);
        $result    = ConstructNames::getDeclarationName(self::$phpcsFile, $interface);

    }//end testInvalidTokenPassed()


    /**
     * Test receiving "null" when passed an anonymous construct.
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @dataProvider dataGetDeclarationNameNull
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ConstructNames::getDeclarationName
     *
     * @return void
     */
    public function testGetDeclarationNameNull($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ConstructNames::getDeclarationName(self::$phpcsFile, $target);
        $this->assertNull($result);

    }//end testGetDeclarationNameNull()


    /**
     * Data provider for the GetDeclarationNameNull test.
     *
     * @see testGetDeclarationNameNull()
     *
     * @return array
     */
    public function dataGetDeclarationNameNull()
    {
        return [
            [
                '/* testClosure */',
                T_CLOSURE,
            ],
            [
                '/* testAnonClass */',
                T_ANON_CLASS,
            ],
        ];

    }//end dataGetDeclarationNameNull()


    /**
     * Test retrieving the name of a function or OO structure.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   Expected function output.
     *
     * @dataProvider dataGetDeclarationName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\ConstructNames::getDeclarationName
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $expected)
    {
        $target = $this->getTargetToken($testMarker, [T_CLASS, T_INTERFACE, T_TRAIT, T_FUNCTION]);
        $result = ConstructNames::getDeclarationName(self::$phpcsFile, $target);
        $this->assertSame($expected, $result);

    }//end testGetDeclarationName()


    /**
     * Data provider for the GetDeclarationName test.
     *
     * @see testGetDeclarationName()
     *
     * @return array
     */
    public function dataGetDeclarationName()
    {
        return [
            [
                '/* testFunction */',
                'functionName',
            ],
            [
                '/* testClass */',
                'ClassName',
            ],
            [
                '/* testMethod */',
                'methodName',
            ],
            [
                '/* testAbstractMethod */',
                'abstractMethodName',
            ],
            [
                '/* testExtendedClass */',
                'ExtendedClass',
            ],
            [
                '/* testInterface */',
                'InterfaceName',
            ],
            [
                '/* testTrait */',
                'TraitName',
            ],
            [
                '/* testClassWithCommentsAndNewLines */',
                'ClassWithCommentsAndNewLines',
            ],
            [
                '/* testClassWithNumber */',
                'ClassWith1Number',
            ],
            [
                '/* testInterfaceWithNumbers */',
                'InterfaceWith12345Numbers',
            ],
            [
                '/* testTraitStartingWithNumber */',
                '5InvalidNameStartingWithNumber',
            ],
            [
                '/* testClassEndingWithNumber */',
                'ValidNameEndingWithNumber5',
            ],
            [
                '/* testInterfaceFullyNumeric */',
                '12345',
            ],
            [
                '/* testMissingInterfaceName */',
                '',
            ],
            [
                '/* testLiveCoding */',
                '',
            ],
        ];

    }//end dataGetDeclarationName()


}//end class
