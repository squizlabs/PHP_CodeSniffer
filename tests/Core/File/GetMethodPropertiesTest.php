<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:getMethodProperties method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class GetMethodPropertiesTest extends AbstractMethodUnitTest
{


    /**
     * Test a basic function.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testBasicFunction */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 2));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testBasicFunction()


    /**
     * Test a function with a return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testReturnFunction */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 2));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testReturnFunction()


    /**
     * Test a closure used as a function argument.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNestedClosure */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 1));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testNestedClosure()


    /**
     * Test a basic method.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testBasicMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 3));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testBasicMethod()


    /**
     * Test a private static method.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testPrivateStaticMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 7));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testPrivateStaticMethod()


    /**
     * Test a basic final method.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testFinalMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 7));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testFinalMethod()


    /**
     * Test a protected method with a return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testProtectedReturnMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 5));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testProtectedReturnMethod()


    /**
     * Test a public method with a return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testPublicReturnMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 5));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testPublicReturnMethod()


    /**
     * Test a public method with a nullable return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNullableReturnMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 5));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testNullableReturnMethod()


    /**
     * Test a public method with a nullable return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testMessyNullableReturnMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 5));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testMessyNullableReturnMethod()


    /**
     * Test a method with a namespaced return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testReturnNamespace */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 3));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testReturnNamespace()


    /**
     * Test a method with a messy namespaces return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testReturnMultilineNamespace */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 3));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testReturnMultilineNamespace()


    /**
     * Test a basic abstract method.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testAbstractMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 5));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testAbstractMethod()


    /**
     * Test an abstract method with a return type.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testAbstractReturnMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 7));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testAbstractReturnMethod()


    /**
     * Test a basic interface method.
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

        $start    = (self::$phpcsFile->numTokens - 1);
        $function = self::$phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testInterfaceMethod */'
        );

        $found = self::$phpcsFile->getMethodProperties(($function + 3));
        unset($found['return_type_token']);
        $this->assertSame($expected, $found);

    }//end testInterfaceMethod()


}//end class
