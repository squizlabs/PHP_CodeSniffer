<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ConstructNames::getDeclarationName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ConstructNames;

use PHP_CodeSniffer\Util\Sniffs\ConstructNames;

class GetDeclarationNameJSTest extends GetDeclarationNameTest
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * @var string
     */
    protected static $fileExtension = 'js';


    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage Token type "T_STRING" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::getDeclarationName
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $print  = $this->getTargetToken('/* testInvalidTokenPassed */', T_STRING);
        $result = ConstructNames::getDeclarationName(self::$phpcsFile, $print);

    }//end testInvalidTokenPassed()


    /**
     * Data provider.
     *
     * @see GetDeclarationNameTest::testGetDeclarationNameNull()
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
        ];

    }//end dataGetDeclarationNameNull()


    /**
     * Data provider.
     *
     * @see GetDeclarationNameTest::testGetDeclarationName()
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
                '/* testFunctionUnicode */',
                'π',
            ],
        ];

    }//end dataGetDeclarationName()


}//end class
