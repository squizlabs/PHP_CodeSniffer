<?php
/**
 * Tests for the \PHP_CodeSniffer\Ruleset class.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;

class RuleInclusionTest extends TestCase
{

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    protected static $ruleset;

    /**
     * Path to the ruleset file.
     *
     * @var string
     */
    private static $standard = '';

    /**
     * The original content of the ruleset.
     *
     * @var string
     */
    private static $contents = '';


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
     * Initialize the config and ruleset objects based on the `RuleInclusionTest.xml` ruleset file.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        if ($GLOBALS['PHP_CODESNIFFER_PEAR'] === true) {
            // This test will be skipped.
            return;
        }

        $standard       = __DIR__.'/'.basename(__FILE__, '.php').'.xml';
        self::$standard = $standard;

        // On-the-fly adjust the ruleset test file to be able to test
        // sniffs included with relative paths.
        $contents       = file_get_contents($standard);
        self::$contents = $contents;

        $repoRootDir = basename(dirname(dirname(dirname(__DIR__))));

        $newPath = $repoRootDir;
        if (DIRECTORY_SEPARATOR === '\\') {
            $newPath = str_replace('\\', '/', $repoRootDir);
        }

        $adjusted = str_replace('%path_root_dir%', $newPath, $contents);

        if (file_put_contents($standard, $adjusted) === false) {
            self::markTestSkipped('On the fly ruleset adjustment failed');
        }

        $config        = new Config(["--standard=$standard"]);
        self::$ruleset = new Ruleset($config);

    }//end setUpBeforeClass()


    /**
     * Reset ruleset file.
     *
     * @return void
     */
    public function tearDown()
    {
        file_put_contents(self::$standard, self::$contents);

    }//end tearDown()


    /**
     * Test that sniffs are registered.
     *
     * @return void
     */
    public function testHasSniffCodes()
    {
        $this->assertObjectHasAttribute('sniffCodes', self::$ruleset);
        $this->assertCount(48, self::$ruleset->sniffCodes);

    }//end testHasSniffCodes()


    /**
     * Test that sniffs are correctly registered, independently of the syntax used to include the sniff.
     *
     * @param string $key   Expected array key.
     * @param string $value Expected array value.
     *
     * @dataProvider dataRegisteredSniffCodes
     *
     * @return void
     */
    public function testRegisteredSniffCodes($key, $value)
    {
        $this->assertArrayHasKey($key, self::$ruleset->sniffCodes);
        $this->assertSame($value, self::$ruleset->sniffCodes[$key]);

    }//end testRegisteredSniffCodes()


    /**
     * Data provider.
     *
     * @see self::testRegisteredSniffCodes()
     *
     * @return array
     */
    public function dataRegisteredSniffCodes()
    {
        return [
            [
                'PSR2.Classes.ClassDeclaration',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff',
            ],
            [
                'PSR2.Classes.PropertyDeclaration',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff',
            ],
            [
                'PSR2.ControlStructures.ControlStructureSpacing',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ControlStructureSpacingSniff',
            ],
            [
                'PSR2.ControlStructures.ElseIfDeclaration',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ElseIfDeclarationSniff',
            ],
            [
                'PSR2.ControlStructures.SwitchDeclaration',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff',
            ],
            [
                'PSR2.Files.ClosingTag',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff',
            ],
            [
                'PSR2.Files.EndFileNewline',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff',
            ],
            [
                'PSR2.Methods.FunctionCallSignature',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionCallSignatureSniff',
            ],
            [
                'PSR2.Methods.FunctionClosingBrace',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionClosingBraceSniff',
            ],
            [
                'PSR2.Methods.MethodDeclaration',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff',
            ],
            [
                'PSR2.Namespaces.NamespaceDeclaration',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff',
            ],
            [
                'PSR2.Namespaces.UseDeclaration',
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff',
            ],
            [
                'PSR1.Classes.ClassDeclaration',
                'PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff',
            ],
            [
                'PSR1.Files.SideEffects',
                'PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff',
            ],
            [
                'PSR1.Methods.CamelCapsMethodName',
                'PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff',
            ],
            [
                'Generic.PHP.DisallowAlternativePHPTags',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowAlternativePHPTagsSniff',
            ],
            [
                'Generic.PHP.DisallowShortOpenTag',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff',
            ],
            [
                'Generic.Files.ByteOrderMark',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff',
            ],
            [
                'Squiz.Classes.ValidClassName',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff',
            ],
            [
                'Generic.NamingConventions.UpperCaseConstantName',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff',
            ],
            [
                'Generic.Files.LineEndings',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff',
            ],
            [
                'Generic.Files.LineLength',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff',
            ],
            [
                'Squiz.WhiteSpace.SuperfluousWhitespace',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff',
            ],
            [
                'Generic.Formatting.DisallowMultipleStatements',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff',
            ],
            [
                'Generic.WhiteSpace.ScopeIndent',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff',
            ],
            [
                'Generic.WhiteSpace.DisallowTabIndent',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff',
            ],
            [
                'Generic.PHP.LowerCaseKeyword',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff',
            ],
            [
                'Generic.PHP.LowerCaseConstant',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff',
            ],
            [
                'Squiz.Scope.MethodScope',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\MethodScopeSniff',
            ],
            [
                'Squiz.WhiteSpace.ScopeKeywordSpacing',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeKeywordSpacingSniff',
            ],
            [
                'Squiz.Functions.FunctionDeclaration',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationSniff',
            ],
            [
                'Squiz.Functions.LowercaseFunctionKeywords',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\LowercaseFunctionKeywordsSniff',
            ],
            [
                'Squiz.Functions.FunctionDeclarationArgumentSpacing',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff',
            ],
            [
                'PEAR.Functions.ValidDefaultValue',
                'PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\ValidDefaultValueSniff',
            ],
            [
                'Squiz.Functions.MultiLineFunctionDeclaration',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff',
            ],
            [
                'Generic.Functions.FunctionCallArgumentSpacing',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff',
            ],
            [
                'Squiz.ControlStructures.ControlSignature',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ControlSignatureSniff',
            ],
            [
                'Squiz.WhiteSpace.ControlStructureSpacing',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ControlStructureSpacingSniff',
            ],
            [
                'Squiz.WhiteSpace.ScopeClosingBrace',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ScopeClosingBraceSniff',
            ],
            [
                'Squiz.ControlStructures.ForEachLoopDeclaration',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ForEachLoopDeclarationSniff',
            ],
            [
                'Squiz.ControlStructures.ForLoopDeclaration',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\ForLoopDeclarationSniff',
            ],
            [
                'Squiz.ControlStructures.LowercaseDeclaration',
                'PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures\LowercaseDeclarationSniff',
            ],
            [
                'Generic.ControlStructures.InlineControlStructure',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures\InlineControlStructureSniff',
            ],
            [
                'PSR12.Operators.OperatorSpacing',
                'PHP_CodeSniffer\Standards\PSR12\Sniffs\Operators\OperatorSpacingSniff',
            ],
            [
                'Generic.Arrays.ArrayIndent',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff',
            ],
            [
                'Generic.Metrics.CyclomaticComplexity',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff',
            ],
            [
                'Generic.NamingConventions.CamelCapsFunctionName',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\CamelCapsFunctionNameSniff',
            ],
            [
                'Generic.Metrics.NestingLevel',
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\NestingLevelSniff',
            ],
        ];

    }//end dataRegisteredSniffCodes()


    /**
     * Test that setting properties for standards, categories, sniffs works for all supported rule
     * inclusion methods.
     *
     * @param string $sniffClass    The name of the sniff class.
     * @param string $propertyName  The name of the changed property.
     * @param mixed  $expectedValue The value expected for the property.
     *
     * @dataProvider dataSettingProperties
     *
     * @return void
     */
    public function testSettingProperties($sniffClass, $propertyName, $expectedValue)
    {
        $this->assertObjectHasAttribute('sniffs', self::$ruleset);
        $this->assertArrayHasKey($sniffClass, self::$ruleset->sniffs);
        $this->assertObjectHasAttribute($propertyName, self::$ruleset->sniffs[$sniffClass]);

        $actualValue = self::$ruleset->sniffs[$sniffClass]->$propertyName;
        $this->assertSame($expectedValue, $actualValue);

    }//end testSettingProperties()


    /**
     * Data provider.
     *
     * @see self::testSettingProperties()
     *
     * @return array
     */
    public function dataSettingProperties()
    {
        return [
            'Set property for complete standard: PSR2 ClassDeclaration'                                  => [
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff',
                'indent',
                '20',
            ],
            'Set property for complete standard: PSR2 SwitchDeclaration'                                 => [
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff',
                'indent',
                '20',
            ],
            'Set property for complete standard: PSR2 FunctionCallSignature'                             => [
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionCallSignatureSniff',
                'indent',
                '20',
            ],
            'Set property for complete category: PSR12 OperatorSpacing'                                  => [
                'PHP_CodeSniffer\Standards\PSR12\Sniffs\Operators\OperatorSpacingSniff',
                'ignoreSpacingBeforeAssignments',
                false,
            ],
            'Set property for individual sniff: Generic ArrayIndent'                                     => [
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff',
                'indent',
                '2',
            ],
            'Set property for individual sniff using sniff file inclusion: Generic LineLength'           => [
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff',
                'lineLimit',
                '10',
            ],
            'Set property for individual sniff using sniff file inclusion: CamelCapsFunctionName'        => [
                'PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\CamelCapsFunctionNameSniff',
                'strict',
                false,
            ],
            'Set property for individual sniff via included ruleset: NestingLevel - nestingLevel'        => [
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\NestingLevelSniff',
                'nestingLevel',
                '2',
            ],
            'Set property for all sniffs in an included ruleset: NestingLevel - absoluteNestingLevel'    => [
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\NestingLevelSniff',
                'absoluteNestingLevel',
                true,
            ],

            // Testing that setting a property at error code level does *not* work.
            'Set property for error code will not change the sniff property value: CyclomaticComplexity' => [
                'PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff',
                'complexity',
                10,
            ],
        ];

    }//end dataSettingProperties()


    /**
     * Test that setting properties for standards, categories on sniffs which don't support the property will
     * silently ignore the property and not set it.
     *
     * @param string $sniffClass   The name of the sniff class.
     * @param string $propertyName The name of the property which should not be set.
     *
     * @dataProvider dataSettingInvalidPropertiesOnStandardsAndCategoriesSilentlyFails
     *
     * @return void
     */
    public function testSettingInvalidPropertiesOnStandardsAndCategoriesSilentlyFails($sniffClass, $propertyName)
    {
        $this->assertObjectHasAttribute('sniffs', self::$ruleset, 'Ruleset does not have property sniffs');
        $this->assertArrayHasKey($sniffClass, self::$ruleset->sniffs, 'Sniff class '.$sniffClass.' not listed in registered sniffs');

        $sniffObject = self::$ruleset->sniffs[$sniffClass];
        $this->assertObjectNotHasAttribute($propertyName, $sniffObject, 'Property '.$propertyName.' registered for sniff '.$sniffClass.' which does not support it');

    }//end testSettingInvalidPropertiesOnStandardsAndCategoriesSilentlyFails()


    /**
     * Data provider.
     *
     * @see self::testSettingInvalidPropertiesOnStandardsAndCategoriesSilentlyFails()
     *
     * @return array
     */
    public function dataSettingInvalidPropertiesOnStandardsAndCategoriesSilentlyFails()
    {
        return [
            'Set property for complete standard: PSR2 ClassDeclaration'      => [
                'PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff',
                'setforallsniffs',
            ],
            'Set property for complete standard: PSR2 FunctionCallSignature' => [
                'PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionCallSignatureSniff',
                'setforallsniffs',
            ],
            'Set property for complete category: PSR12 OperatorSpacing'      => [
                'PHP_CodeSniffer\Standards\PSR12\Sniffs\Operators\OperatorSpacingSniff',
                'setforallincategory',
            ],
        ];

    }//end dataSettingInvalidPropertiesOnStandardsAndCategoriesSilentlyFails()


}//end class
