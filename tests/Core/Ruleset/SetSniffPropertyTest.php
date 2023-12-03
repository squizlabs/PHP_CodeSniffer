<?php
/**
 * Tests for the handling of properties being set via the ruleset.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2022 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;

/**
 * These tests specifically focus on the changes made to work around the PHP 8.2 dynamic properties deprecation.
 *
 * @covers \PHP_CodeSniffer\Ruleset::setSniffProperty
 */
class SetSniffPropertyTest extends TestCase
{


    /**
     * Initialize the test.
     *
     * @return void
     */
    public function setUp()
    {
        if ($GLOBALS['PHP_CODESNIFFER_PEAR'] === true) {
            // PEAR installs test and sniff files into different locations
            // so these tests will not pass as they directly reference files
            // by relative location.
            $this->markTestSkipped('Test cannot run from a PEAR install');
        }

    }//end setUp()


    /**
     * Test that setting a property via the ruleset works in all situations which allow for it.
     *
     * @param string $name Name of the test. Used for the sniff name, the ruleset file name etc.
     *
     * @dataProvider dataSniffPropertiesGetSetWhenAllowed
     *
     * @return void
     */
    public function testSniffPropertiesGetSetWhenAllowed($name)
    {
        $sniffCode  = "Fixtures.Category.{$name}";
        $sniffClass = 'Fixtures\Sniffs\Category\\'.$name.'Sniff';
        $properties = [
            'arbitrarystring' => 'arbitraryvalue',
            'arbitraryarray'  => [
                'mykey'    => 'myvalue',
                'otherkey' => 'othervalue',
            ],
        ];

        // Set up the ruleset.
        $standard = __DIR__."/{$name}Test.xml";
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        // Verify that the sniff has been registered.
        $this->assertGreaterThan(0, count($ruleset->sniffCodes), 'No sniff codes registered');

        // Verify that our target sniff has been registered.
        $this->assertArrayHasKey($sniffCode, $ruleset->sniffCodes, 'Target sniff not registered');
        $this->assertSame($sniffClass, $ruleset->sniffCodes[$sniffCode], 'Target sniff not registered with the correct class');

        // Test that the property as declared in the ruleset has been set on the sniff.
        $this->assertArrayHasKey($sniffClass, $ruleset->sniffs, 'Sniff class not listed in registered sniffs');

        $sniffObject = $ruleset->sniffs[$sniffClass];
        foreach ($properties as $name => $expectedValue) {
            $this->assertSame($expectedValue, $sniffObject->$name, 'Property value not set to expected value');
        }

    }//end testSniffPropertiesGetSetWhenAllowed()


    /**
     * Data provider.
     *
     * @see self::testSniffPropertiesGetSetWhenAllowed()
     *
     * @return array
     */
    public function dataSniffPropertiesGetSetWhenAllowed()
    {
        return [
            'Property allowed as explicitly declared'            => ['SetPropertyAllowedAsDeclared'],
            'Property allowed as sniff extends stdClass'         => ['SetPropertyAllowedViaStdClass'],
            'Property allowed as sniff has magic __set() method' => ['SetPropertyAllowedViaMagicMethod'],
        ];

    }//end dataSniffPropertiesGetSetWhenAllowed()


    /**
     * Test that setting a property for a category will apply it correctly to those sniffs which support the
     * property, but won't apply it to sniffs which don't.
     *
     * Note: this test intentionally uses the `PEAR.Functions` category as two sniffs in that category
     * have a public property with the same name (`indent`) and one sniff doesn't, which makes it a great
     * test case for this.
     *
     * @return void
     */
    public function testSetPropertyAppliesPropertyToMultipleSniffsInCategory()
    {
        $propertyName  = 'indent';
        $expectedValue = '10';

        // Set up the ruleset.
        $standard = __DIR__.'/SetPropertyAppliesPropertyToMultipleSniffsInCategoryTest.xml';
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        // Test that the two sniffs which support the property have received the value.
        $sniffClass = 'PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\FunctionCallSignatureSniff';
        $this->assertArrayHasKey($sniffClass, $ruleset->sniffs, 'Sniff class '.$sniffClass.' not listed in registered sniffs');
        $sniffObject = $ruleset->sniffs[$sniffClass];
        $this->assertSame($expectedValue, $sniffObject->$propertyName, 'Property value not set to expected value for '.$sniffClass);

        $sniffClass = 'PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\FunctionDeclarationSniff';
        $this->assertArrayHasKey($sniffClass, $ruleset->sniffs, 'Sniff class '.$sniffClass.' not listed in registered sniffs');
        $sniffObject = $ruleset->sniffs[$sniffClass];
        $this->assertSame($expectedValue, $sniffObject->$propertyName, 'Property value not set to expected value for '.$sniffClass);

        // Test that the property doesn't get set for the one sniff which doesn't support the property.
        $sniffClass = 'PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\ValidDefaultValueSniff';
        $this->assertArrayHasKey($sniffClass, $ruleset->sniffs, 'Sniff class '.$sniffClass.' not listed in registered sniffs');
        $sniffObject = $ruleset->sniffs[$sniffClass];
        $this->assertObjectNotHasAttribute($propertyName, $sniffObject, 'Property registered for sniff '.$sniffClass.' which does not support it');

    }//end testSetPropertyAppliesPropertyToMultipleSniffsInCategory()


    /**
     * Test that attempting to set a non-existent property directly on a sniff will throw an error
     * when the sniff does not explicitly declare the property, extends stdClass or has magic methods.
     *
     * @return void
     */
    public function testSetPropertyThrowsErrorOnInvalidProperty()
    {
        $exceptionClass = 'PHP_CodeSniffer\Exceptions\RuntimeException';
        $exceptionMsg   = 'Ruleset invalid. Property "indentation" does not exist on sniff Generic.Arrays.ArrayIndent';
        if (method_exists($this, 'expectException') === true) {
            $this->expectException($exceptionClass);
            $this->expectExceptionMessage($exceptionMsg);
        } else {
            // PHPUnit < 5.2.0.
            $this->setExpectedException($exceptionClass, $exceptionMsg);
        }

        // Set up the ruleset.
        $standard = __DIR__.'/SetPropertyThrowsErrorOnInvalidPropertyTest.xml';
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

    }//end testSetPropertyThrowsErrorOnInvalidProperty()


    /**
     * Test that attempting to set a non-existent property directly on a sniff will throw an error
     * when the sniff does not explicitly declare the property, extends stdClass or has magic methods,
     * even though the sniff has the PHP 8.2 `#[AllowDynamicProperties]` attribute set.
     *
     * @return void
     */
    public function testSetPropertyThrowsErrorWhenPropertyOnlyAllowedViaAttribute()
    {
        $exceptionClass = 'PHP_CodeSniffer\Exceptions\RuntimeException';
        $exceptionMsg   = 'Ruleset invalid. Property "arbitrarystring" does not exist on sniff Fixtures.Category.SetPropertyNotAllowedViaAttribute';
        if (method_exists($this, 'expectException') === true) {
            $this->expectException($exceptionClass);
            $this->expectExceptionMessage($exceptionMsg);
        } else {
            // PHPUnit < 5.2.0.
            $this->setExpectedException($exceptionClass, $exceptionMsg);
        }

        // Set up the ruleset.
        $standard = __DIR__.'/SetPropertyNotAllowedViaAttributeTest.xml';
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

    }//end testSetPropertyThrowsErrorWhenPropertyOnlyAllowedViaAttribute()


    /**
     * Test that attempting to set a non-existent property on a sniff when the property directive is
     * for the whole standard, does not yield an error.
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testSetPropertyDoesNotThrowErrorOnInvalidPropertyWhenSetForStandard()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/SetPropertyDoesNotThrowErrorOnInvalidPropertyWhenSetForStandardTest.xml';
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

    }//end testSetPropertyDoesNotThrowErrorOnInvalidPropertyWhenSetForStandard()


    /**
     * Test that attempting to set a non-existent property on a sniff when the property directive is
     * for a whole category, does not yield an error.
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testSetPropertyDoesNotThrowErrorOnInvalidPropertyWhenSetForCategory()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/SetPropertyDoesNotThrowErrorOnInvalidPropertyWhenSetForCategoryTest.xml';
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

    }//end testSetPropertyDoesNotThrowErrorOnInvalidPropertyWhenSetForCategory()


    /**
     * Test that setting a property via a direct call to the Ruleset::setSniffProperty() method
     * sets the property correctly when using the new $settings array format.
     *
     * @return void
     */
    public function testDirectCallWithNewArrayFormatSetsProperty()
    {
        $name       = 'SetPropertyAllowedAsDeclared';
        $sniffCode  = "Fixtures.Category.{$name}";
        $sniffClass = 'Fixtures\Sniffs\Category\\'.$name.'Sniff';

        // Set up the ruleset.
        $standard = __DIR__."/{$name}Test.xml";
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $propertyName  = 'arbitrarystring';
        $propertyValue = 'new value';

        $ruleset->setSniffProperty(
            $sniffClass,
            $propertyName,
            [
                'scope' => 'sniff',
                'value' => $propertyValue,
            ]
        );

        // Verify that the sniff has been registered.
        $this->assertGreaterThan(0, count($ruleset->sniffCodes), 'No sniff codes registered');

        // Verify that our target sniff has been registered.
        $this->assertArrayHasKey($sniffCode, $ruleset->sniffCodes, 'Target sniff not registered');
        $this->assertSame($sniffClass, $ruleset->sniffCodes[$sniffCode], 'Target sniff not registered with the correct class');

        // Test that the property as declared in the ruleset has been set on the sniff.
        $this->assertArrayHasKey($sniffClass, $ruleset->sniffs, 'Sniff class not listed in registered sniffs');

        $sniffObject = $ruleset->sniffs[$sniffClass];
        $this->assertSame($propertyValue, $sniffObject->$propertyName, 'Property value not set to expected value');

    }//end testDirectCallWithNewArrayFormatSetsProperty()


    /**
     * Test that setting a property via a direct call to the Ruleset::setSniffProperty() method
     * sets the property correctly when using the old $settings array format.
     *
     * Tested by silencing the deprecation notice as otherwise the test would fail on the deprecation notice.
     *
     * @param mixed $propertyValue Value for the property to set.
     *
     * @dataProvider dataDirectCallWithOldArrayFormatSetsProperty
     *
     * @return void
     */
    public function testDirectCallWithOldArrayFormatSetsProperty($propertyValue)
    {
        $name       = 'SetPropertyAllowedAsDeclared';
        $sniffCode  = "Fixtures.Category.{$name}";
        $sniffClass = 'Fixtures\Sniffs\Category\\'.$name.'Sniff';

        // Set up the ruleset.
        $standard = __DIR__."/{$name}Test.xml";
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $propertyName = 'arbitrarystring';

        @$ruleset->setSniffProperty(
            $sniffClass,
            $propertyName,
            $propertyValue
        );

        // Verify that the sniff has been registered.
        $this->assertGreaterThan(0, count($ruleset->sniffCodes), 'No sniff codes registered');

        // Verify that our target sniff has been registered.
        $this->assertArrayHasKey($sniffCode, $ruleset->sniffCodes, 'Target sniff not registered');
        $this->assertSame($sniffClass, $ruleset->sniffCodes[$sniffCode], 'Target sniff not registered with the correct class');

        // Test that the property as declared in the ruleset has been set on the sniff.
        $this->assertArrayHasKey($sniffClass, $ruleset->sniffs, 'Sniff class not listed in registered sniffs');

        $sniffObject = $ruleset->sniffs[$sniffClass];
        $this->assertSame($propertyValue, $sniffObject->$propertyName, 'Property value not set to expected value');

    }//end testDirectCallWithOldArrayFormatSetsProperty()


    /**
     * Data provider.
     *
     * @see self::testDirectCallWithOldArrayFormatSetsProperty()
     *
     * @return array
     */
    public function dataDirectCallWithOldArrayFormatSetsProperty()
    {
        return [
            'Property value is not an array (boolean)'                       => [false],
            'Property value is not an array (string)'                        => ['a string'],
            'Property value is an empty array'                               => [[]],
            'Property value is an array without keys'                        => [
                [
                    'value',
                    false,
                ],
            ],
            'Property value is an array without the "scope" or "value" keys' => [
                [
                    'key1' => 'value',
                    'key2' => false,
                ],
            ],
            'Property value is an array without the "scope" key'             => [
                [
                    'key1'  => 'value',
                    'value' => true,
                ],
            ],
            'Property value is an array without the "value" key'             => [
                [
                    'scope' => 'value',
                    'key2'  => 1234,
                ],
            ],
        ];

    }//end dataDirectCallWithOldArrayFormatSetsProperty()


    /**
     * Test that setting a property via a direct call to the Ruleset::setSniffProperty() method
     * throws a deprecation notice when using the old $settings array format.
     *
     * Note: as PHPUnit stops as soon as it sees the deprecation notice, the setting of the property
     * value is not tested here.
     *
     * @return void
     */
    public function testDirectCallWithOldArrayFormatThrowsDeprecationNotice()
    {
        $exceptionClass = 'PHPUnit\Framework\Error\Deprecated';
        if (class_exists($exceptionClass) === false) {
            $exceptionClass = 'PHPUnit_Framework_Error_Deprecated';
        }

        $exceptionMsg = 'the format of the $settings parameter has changed from (mixed) $value to array(\'scope\' => \'sniff|standard\', \'value\' => $value). Please update your integration code. See PR #3629 for more information.';

        if (method_exists($this, 'expectException') === true) {
            $this->expectException($exceptionClass);
            $this->expectExceptionMessage($exceptionMsg);
        } else {
            // PHPUnit < 5.2.0.
            $this->setExpectedException($exceptionClass, $exceptionMsg);
        }

        $name       = 'SetPropertyAllowedAsDeclared';
        $sniffCode  = "Fixtures.Category.{$name}";
        $sniffClass = 'Fixtures\Sniffs\Category\\'.$name.'Sniff';

        // Set up the ruleset.
        $standard = __DIR__."/{$name}Test.xml";
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $propertyName = 'arbitrarystring';

        $ruleset->setSniffProperty(
            $sniffClass,
            'arbitrarystring',
            ['key' => 'value']
        );

    }//end testDirectCallWithOldArrayFormatThrowsDeprecationNotice()


}//end class
