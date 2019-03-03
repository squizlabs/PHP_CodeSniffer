<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicFunctionName() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\FunctionDeclarations;

use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;
use PHPUnit\Framework\TestCase;

class IsMagicFunctionNameTest extends TestCase
{


    /**
     * Test valid PHP magic function names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsMagicFunctionName
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicFunctionName
     *
     * @return void
     */
    public function testIsMagicFunctionName($name)
    {
        $this->assertTrue(FunctionDeclarations::isMagicFunctionName($name));

    }//end testIsMagicFunctionName()


    /**
     * Data provider.
     *
     * @see testIsMagicFunctionName()
     *
     * @return array
     */
    public function dataIsMagicFunctionName()
    {
        return [
            'lowercase' => ['__autoload'],
            'uppercase' => ['__AUTOLOAD'],
            'mixedcase' => ['__AutoLoad'],
        ];

    }//end dataIsMagicFunctionName()


    /**
     * Test non-magic function names.
     *
     * @param string $name The function name to test.
     *
     * @dataProvider dataIsMagicFunctionNameFalse
     * @covers       \PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::isMagicFunctionName
     *
     * @return void
     */
    public function testIsMagicFunctionNameFalse($name)
    {
        $this->assertFalse(FunctionDeclarations::isMagicFunctionName($name));

    }//end testIsMagicFunctionNameFalse()


    /**
     * Data provider.
     *
     * @see testIsMagicFunctionNameFalse()
     *
     * @return array
     */
    public function dataIsMagicFunctionNameFalse()
    {
        return [
            'no_underscore'           => ['noDoubleUnderscore'],
            'single_underscore'       => ['_autoload'],
            'triple_underscore'       => ['___autoload'],
            'not_magic_function_name' => ['__notAutoload'],
        ];

    }//end dataIsMagicFunctionNameFalse()


}//end class
