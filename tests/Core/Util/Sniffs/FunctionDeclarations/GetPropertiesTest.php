<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties() method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\FunctionDeclarations;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;

class GetPropertiesTest extends AbstractMethodUnitTest
{


    /**
     * Test receiving an expected exception when a non function token is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\RuntimeException
     * @expectedExceptionMessage $stackPtr must be of type T_FUNCTION or T_CLOSURE
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testNotAFunctionException()
    {
        $interface = $this->getTargetToken('/* testNotAFunction */', T_INTERFACE);
        $result    = FunctionDeclarations::getProperties(self::$phpcsFile, $interface);

    }//end testNotAFunctionException()


    /**
     * Test a basic function.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testBasicFunction()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBasicFunction()


    /**
     * Test a function with a return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testReturnFunction()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'array',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnFunction()


    /**
     * Test a closure used as a function argument.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testNestedClosure()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => 'int',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNestedClosure()


    /**
     * Test a basic method.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testBasicMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testBasicMethod()


    /**
     * Test a private static method.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testPrivateStaticMethod()
    {
        $expected = [
            'scope'                => 'private',
            'scope_specified'      => true,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => true,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPrivateStaticMethod()


    /**
     * Test a basic final method.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testFinalMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => true,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testFinalMethod()


    /**
     * Test a protected method with a return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testProtectedReturnMethod()
    {
        $expected = [
            'scope'                => 'protected',
            'scope_specified'      => true,
            'return_type'          => 'int',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testProtectedReturnMethod()


    /**
     * Test a public method with a return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testPublicReturnMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => 'array',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testPublicReturnMethod()


    /**
     * Test a public method with a nullable return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testNullableReturnMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '?array',
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testNullableReturnMethod()


    /**
     * Test a public method with a nullable return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testMessyNullableReturnMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => true,
            'return_type'          => '?array',
            'nullable_return_type' => true,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testMessyNullableReturnMethod()


    /**
     * Test a method with a namespaced return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testReturnNamespace()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '\MyNamespace\MyClass',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnNamespace()


    /**
     * Test a method with a messy namespaces return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testReturnMultilineNamespace()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '\MyNamespace\MyClass\Foo',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => true,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testReturnMultilineNamespace()


    /**
     * Test a basic abstract method.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testAbstractMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => true,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testAbstractMethod()


    /**
     * Test an abstract method with a return type.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testAbstractReturnMethod()
    {
        $expected = [
            'scope'                => 'protected',
            'scope_specified'      => true,
            'return_type'          => 'bool',
            'nullable_return_type' => false,
            'is_abstract'          => true,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testAbstractReturnMethod()


    /**
     * Test a basic interface method.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::getProperties
     *
     * @return void
     */
    public function testInterfaceMethod()
    {
        $expected = [
            'scope'                => 'public',
            'scope_specified'      => false,
            'return_type'          => '',
            'nullable_return_type' => false,
            'is_abstract'          => false,
            'is_final'             => false,
            'is_static'            => false,
            'has_body'             => false,
        ];

        $this->getPropertiesTestHelper('/* '.__FUNCTION__.' */', $expected);

    }//end testInterfaceMethod()


    /**
     * Test helper.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected function output.
     *
     * @return void
     */
    private function getPropertiesTestHelper($testMarker, $expected)
    {
        $function = $this->getTargetToken($testMarker, [T_FUNCTION, T_CLOSURE]);
        $found    = FunctionDeclarations::getProperties(self::$phpcsFile, $function);
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end getPropertiesTestHelper()


}//end class
