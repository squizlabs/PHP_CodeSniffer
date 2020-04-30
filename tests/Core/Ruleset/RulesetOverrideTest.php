<?php
/**
 * Tests that rulesets override eachother correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;

class RulesetOverrideTest extends TestCase
{

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    protected static $ruleset;


    /**
     * Initialize the config and ruleset objects based on our ruleset file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $standard      = __DIR__.'/RulesetOverrideTest.xml';
        $config        = new Config(["--standard=$standard"]);
        self::$ruleset = new Ruleset($config);

    }//end setUpBeforeClass()


    /**
     * Test that config values are overridden correctly.
     *
     * @return void
     */
    public function testConfigValues()
    {
        $value = Config::getConfigData('configOverrideTestValue1');
        $this->assertSame('included', $value);

        $value = Config::getConfigData('configOverrideTestValue2');
        $this->assertSame('modified', $value);

    }//end testConfigValues()


    /**
     * Test that ini values are overridden correctly.
     *
     * @return void
     */
    public function testIniValues()
    {
        $value = ini_get('highlight.string');
        $this->assertSame('included', $value);

        $value = ini_get('highlight.keyword');
        $this->assertSame('modified', $value);

    }//end testIniValues()


    /**
     * Test that arg values are overridden correctly.
     *
     * @return void
     */
    public function testArgValues()
    {
        $config = self::$ruleset->getConfig();
        $this->assertSame(false, $config->colors);
        $this->assertSame(80, $config->reportWidth);

    }//end testArgValues()


}//end class
