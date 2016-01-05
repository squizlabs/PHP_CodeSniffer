<?php
/**
 * Tests for the PHP_CodeSniffer:isCamelCaps method.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tests for the PHP_CodeSniffer:isCamelCaps method.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_IsCamelCapsTest extends PHPUnit_Framework_TestCase
{


    /**
     * Test valid public function/method names.
     *
     * @return void
     */
    public function testValidNotClassFormatPublic()
    {
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('thisIsCamelCaps', false, true, true));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('thisISCamelCaps', false, true, false));

    }//end testValidNotClassFormatPublic()


    /**
     * Test invalid public function/method names.
     *
     * @return void
     */
    public function testInvalidNotClassFormatPublic()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_thisIsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('thisISCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('ThisIsCamelCaps', false, true, true));

        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('3thisIsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('*thisIsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('-thisIsCamelCaps', false, true, true));

        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this*IsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this-IsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this_IsCamelCaps', false, true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('this_is_camel_caps', false, true, true));

    }//end testInvalidNotClassFormatPublic()


    /**
     * Test valid private method names.
     *
     * @return void
     */
    public function testValidNotClassFormatPrivate()
    {
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('_thisIsCamelCaps', false, false, true));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('_thisISCamelCaps', false, false, false));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('_i18N', false, false, true));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('_i18n', false, false, true));

    }//end testValidNotClassFormatPrivate()


    /**
     * Test invalid private method names.
     *
     * @return void
     */
    public function testInvalidNotClassFormatPrivate()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_thisISCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_ThisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('__thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('__thisISCamelCaps', false, false, false));

        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('3thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('*thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('-thisIsCamelCaps', false, false, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_this_is_camel_caps', false, false, true));

    }//end testInvalidNotClassFormatPrivate()


    /**
     * Test valid class names.
     *
     * @return void
     */
    public function testValidClassFormatPublic()
    {
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('ThisIsCamelCaps', true, true, true));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('ThisISCamelCaps', true, true, false));
        $this->assertTrue(PHP_CodeSniffer::isCamelCaps('This3IsCamelCaps', true, true, false));

    }//end testValidClassFormatPublic()


    /**
     * Test invalid class names.
     *
     * @return void
     */
    public function testInvalidClassFormat()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('thisIsCamelCaps', true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('This-IsCamelCaps', true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('This_Is_Camel_Caps', true));

    }//end testInvalidClassFormat()


    /**
     * Test invalid class names with the private flag set.
     *
     * Note that the private flag is ignored if the class format
     * flag is set, so these names are all invalid.
     *
     * @return void
     */
    public function testInvalidClassFormatPrivate()
    {
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_ThisIsCamelCaps', true, true));
        $this->assertFalse(PHP_CodeSniffer::isCamelCaps('_ThisIsCamelCaps', true, false));

    }//end testInvalidClassFormatPrivate()


}//end class

?>
