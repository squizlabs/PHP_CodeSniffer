<?php
/**
 * Tests for PHP_CodeSniffer config.
 *
 * @author  Willem Stuursma-Ruwen <willem@stuursma.name>
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{


    /**
     * Test that if no custom path for the PHP executable is set, the PHP_BINARY constant's value is returned.
     *
     * @issue  2085
     * @return void
     */
    public function testGetPhpExecutablePathReturnsPhpBinaryIfNoCustomPathSet()
    {
        Config::setConfigData("php_path", null);
        $this->assertEquals(PHP_BINARY, Config::getExecutablePath("php"));

    }//end testGetPhpExecutablePathReturnsPhpBinaryIfNoCustomPathSet()


    /**
     * Test that if a custom path for the PHP executable is set, this is respected.
     *
     * @return void
     */
    public function testGetPhpExecutablePathReturnsCustomPathIfConfigured()
    {
        Config::setConfigData("php_path", "/Applications/XAMPP/bin/php-7.0.13");
        $this->assertEquals("/Applications/XAMPP/bin/php-7.0.13", Config::getExecutablePath("php"));

    }//end testGetPhpExecutablePathReturnsCustomPathIfConfigured()


    /**
     * Clean up any config changes made by the test.
     *
     * @return void
     */
    protected function tearDown()
    {
        Config::setConfigData("php_path", null);
        parent::tearDown();

    }//end tearDown()


}//end class
