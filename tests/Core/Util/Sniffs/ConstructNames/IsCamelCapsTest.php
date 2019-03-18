<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps() method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ConstructNames;

use PHP_CodeSniffer\Util\Sniffs\ConstructNames;
use PHPUnit\Framework\TestCase;

class IsCamelCapsTest extends TestCase
{


    /**
     * Test valid public function/method names.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps
     *
     * @return void
     */
    public function testValidNotClassFormatPublic()
    {
        $this->assertTrue(ConstructNames::isCamelCaps('thisIsCamelCaps', false, true, true));
        $this->assertTrue(ConstructNames::isCamelCaps('thisISCamelCaps', false, true, false));

    }//end testValidNotClassFormatPublic()


    /**
     * Test invalid public function/method names.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps
     *
     * @return void
     */
    public function testInvalidNotClassFormatPublic()
    {
        $this->assertFalse(ConstructNames::isCamelCaps('_thisIsCamelCaps', false, true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('thisISCamelCaps', false, true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('ThisIsCamelCaps', false, true, true));

        $this->assertFalse(ConstructNames::isCamelCaps('3thisIsCamelCaps', false, true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('*thisIsCamelCaps', false, true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('-thisIsCamelCaps', false, true, true));

        $this->assertFalse(ConstructNames::isCamelCaps('this*IsCamelCaps', false, true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('this-IsCamelCaps', false, true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('this_IsCamelCaps', false, true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('this_is_camel_caps', false, true, true));

    }//end testInvalidNotClassFormatPublic()


    /**
     * Test valid private method names.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps
     *
     * @return void
     */
    public function testValidNotClassFormatPrivate()
    {
        $this->assertTrue(ConstructNames::isCamelCaps('_thisIsCamelCaps', false, false, true));
        $this->assertTrue(ConstructNames::isCamelCaps('_thisISCamelCaps', false, false, false));
        $this->assertTrue(ConstructNames::isCamelCaps('_i18N', false, false, true));
        $this->assertTrue(ConstructNames::isCamelCaps('_i18n', false, false, true));

    }//end testValidNotClassFormatPrivate()


    /**
     * Test invalid private method names.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps
     *
     * @return void
     */
    public function testInvalidNotClassFormatPrivate()
    {
        $this->assertFalse(ConstructNames::isCamelCaps('thisIsCamelCaps', false, false, true));
        $this->assertFalse(ConstructNames::isCamelCaps('_thisISCamelCaps', false, false, true));
        $this->assertFalse(ConstructNames::isCamelCaps('_ThisIsCamelCaps', false, false, true));
        $this->assertFalse(ConstructNames::isCamelCaps('__thisIsCamelCaps', false, false, true));
        $this->assertFalse(ConstructNames::isCamelCaps('__thisISCamelCaps', false, false, false));

        $this->assertFalse(ConstructNames::isCamelCaps('3thisIsCamelCaps', false, false, true));
        $this->assertFalse(ConstructNames::isCamelCaps('*thisIsCamelCaps', false, false, true));
        $this->assertFalse(ConstructNames::isCamelCaps('-thisIsCamelCaps', false, false, true));
        $this->assertFalse(ConstructNames::isCamelCaps('_this_is_camel_caps', false, false, true));

    }//end testInvalidNotClassFormatPrivate()


    /**
     * Test valid class names.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps
     *
     * @return void
     */
    public function testValidClassFormatPublic()
    {
        $this->assertTrue(ConstructNames::isCamelCaps('ThisIsCamelCaps', true, true, true));
        $this->assertTrue(ConstructNames::isCamelCaps('ThisISCamelCaps', true, true, false));
        $this->assertTrue(ConstructNames::isCamelCaps('This3IsCamelCaps', true, true, false));

    }//end testValidClassFormatPublic()


    /**
     * Test invalid class names.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps
     *
     * @return void
     */
    public function testInvalidClassFormat()
    {
        $this->assertFalse(ConstructNames::isCamelCaps('thisIsCamelCaps', true));
        $this->assertFalse(ConstructNames::isCamelCaps('This-IsCamelCaps', true));
        $this->assertFalse(ConstructNames::isCamelCaps('This_Is_Camel_Caps', true));

    }//end testInvalidClassFormat()


    /**
     * Test invalid class names with the private flag set.
     *
     * Note that the private flag is ignored if the class format
     * flag is set, so these names are all invalid.
     *
     * @covers \PHP_CodeSniffer\Util\Sniffs\ConstructNames::isCamelCaps
     *
     * @return void
     */
    public function testInvalidClassFormatPrivate()
    {
        $this->assertFalse(ConstructNames::isCamelCaps('_ThisIsCamelCaps', true, true));
        $this->assertFalse(ConstructNames::isCamelCaps('_ThisIsCamelCaps', true, false));

    }//end testInvalidClassFormatPrivate()


}//end class
