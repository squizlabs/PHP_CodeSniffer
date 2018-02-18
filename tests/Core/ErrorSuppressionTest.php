<?php
/**
 * Tests for PHP_CodeSniffer error suppression tags.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHPUnit\Framework\TestCase;

class ErrorSuppressionTest extends TestCase
{


    /**
     * Test suppressing a single error.
     *
     * @return void
     */
    public function testSuppressError()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with inline comment suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with multi-line inline comment suppression, tab-indented.
        $content = '<?php '.PHP_EOL."\t".'// For reasons'.PHP_EOL."\t".'// phpcs:disable'.PHP_EOL."\t".'$var = FALSE;'.PHP_EOL."\t".'// phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with inline @ comment suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with inline comment suppression mixed case.
        $content = '<?php '.PHP_EOL.'// PHPCS:Disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// pHPcs:enabLE';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with inline comment suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with block comment suppression.
        $content = '<?php '.PHP_EOL.'/* phpcs:disable */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/* phpcs:enable */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with multi-line block comment suppression.
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.' phpcs:disable'.PHP_EOL.' */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/*'.PHP_EOL.' phpcs:enable'.PHP_EOL.' */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with multi-line block comment suppression, each line starred.
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.' * phpcs:disable'.PHP_EOL.' */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/*'.PHP_EOL.' * phpcs:enable'.PHP_EOL.' */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with multi-line block comment suppression, tab-indented.
        $content = '<?php '.PHP_EOL."\t".'/*'.PHP_EOL."\t".' * phpcs:disable'.PHP_EOL."\t".' */'.PHP_EOL."\t".'$var = FALSE;'.PHP_EOL."\t".'/*'.PHP_EOL.' * phpcs:enable'.PHP_EOL.' */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with block comment suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/* @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/* @codingStandardsIgnoreEnd */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with multi-line block comment suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.' @codingStandardsIgnoreStart'.PHP_EOL.' */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/*'.PHP_EOL.' @codingStandardsIgnoreEnd'.PHP_EOL.' */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'/** phpcs:disable */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** phpcs:enable */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** @codingStandardsIgnoreEnd */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

    }//end testSuppressError()


    /**
     * Test suppressing 1 out of 2 errors.
     *
     * @return void
     */
    public function testSuppressSomeErrors()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// phpcs:enable'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with @ suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @phpcs:enable'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with a PHPDoc block suppression.
        $content = '<?php '.PHP_EOL.'/** phpcs:disable */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** phpcs:enable */'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with a PHPDoc block suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** @codingStandardsIgnoreEnd */'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

    }//end testSuppressSomeErrors()


    /**
     * Test suppressing a single warning.
     *
     * @return void
     */
    public function testSuppressWarning()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.Commenting.Todo'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with @ suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:disable'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// @phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'/** phpcs:disable */'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'/** phpcs:enable */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a docblock suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'/** @codingStandardsIgnoreEnd */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

    }//end testSuppressWarning()


    /**
     * Test suppressing a single error using a single line ignore.
     *
     * @return void
     */
    public function testSuppressLine()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);

        // Process with suppression on line before.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

         // Process with @ suppression on line before.
        $content = '<?php '.PHP_EOL.'// @phpcs:ignore'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with suppression on line before.
        $content = '<?php '.PHP_EOL.'/* phpcs:ignore */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

         // Process with @ suppression on line before.
        $content = '<?php '.PHP_EOL.'/* @phpcs:ignore */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with suppression on line before (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreLine'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with suppression on same line.
        $content = '<?php '.PHP_EOL.'$var = FALSE; // phpcs:ignore'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with @ suppression on same line.
        $content = '<?php '.PHP_EOL.'$var = FALSE; // @phpcs:ignore'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

        // Process with suppression on same line (deprecated syntax).
        $content = '<?php '.PHP_EOL.'$var = FALSE; // @codingStandardsIgnoreLine'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);

    }//end testSuppressLine()


    /**
     * Test that using a single line ignore does not interfere with other suppressions.
     *
     * @return void
     */
    public function testNestedSuppressLine()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

        $ruleset = new Ruleset($config);

        // Process with disable/enable suppression and no single line suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with disable/enable @ suppression and no single line suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with disable/enable suppression and no single line suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with line suppression nested within disable/enable suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable'.PHP_EOL.'// phpcs:ignore'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with line @ suppression nested within disable/enable @ suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:disable'.PHP_EOL.'// @phpcs:ignore'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with line suppression nested within disable/enable suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'// @codingStandardsIgnoreLine'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

    }//end testNestedSuppressLine()


    /**
     * Test suppressing a scope opener.
     *
     * @return void
     */
    public function testSuppressScope()
    {
        $config            = new Config();
        $config->standards = ['PEAR'];
        $config->sniffs    = ['PEAR.NamingConventions.ValidVariableName'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'function myFunction() {'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//phpcs:disable'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//phpcs:enable'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//@phpcs:disable'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//@phpcs:enable'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//@codingStandardsIgnoreStart'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//@codingStandardsIgnoreEnd'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'/** phpcs:disable */'.PHP_EOL.'function myFunction() {'.PHP_EOL.'/** phpcs:enable */'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock @ suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'/** @phpcs:disable */'.PHP_EOL.'function myFunction() {'.PHP_EOL.'/** @phpcs:enable */'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'function myFunction() {'.PHP_EOL.'/** @codingStandardsIgnoreEnd */'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

    }//end testSuppressScope()


    /**
     * Test suppressing a whole file.
     *
     * @return void
     */
    public function testSuppressFile()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.Commenting.Todo'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:ignoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with @ suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:ignoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process mixed case.
        $content = '<?php '.PHP_EOL.'// PHPCS:Ignorefile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process late comment.
        $content = '<?php '.PHP_EOL.'class MyClass {}'.PHP_EOL.'$foo = new MyClass()'.PHP_EOL.'// phpcs:ignoreFile';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process late comment (deprecated syntax).
        $content = '<?php '.PHP_EOL.'class MyClass {}'.PHP_EOL.'$foo = new MyClass()'.PHP_EOL.'// @codingStandardsIgnoreFile';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a block comment suppression.
        $content = '<?php '.PHP_EOL.'/* phpcs:ignoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a multi-line block comment suppression.
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.' phpcs:ignoreFile'.PHP_EOL.' */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a block comment suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/* @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a multi-line block comment suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.' @codingStandardsIgnoreFile'.PHP_EOL.' */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with docblock suppression.
        $content = '<?php '.PHP_EOL.'/** phpcs:ignoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with docblock suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

    }//end testSuppressFile()


    /**
     * Test disabling specific sniffs.
     *
     * @return void
     */
    public function testDisableSelected()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // Suppress a single sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress multiple sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress adding sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'// phpcs:disable Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a category of sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a whole standard.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress using docblocks.
        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * phpcs:disable Generic.Commenting.Todo'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * @phpcs:disable Generic.Commenting.Todo'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress wrong category using docblocks.
        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * phpcs:disable Generic.Files'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * @phpcs:disable Generic.Files'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

    }//end testDisableSelected()


    /**
     * Test re-enabling specific sniffs that have been disabled.
     *
     * @return void
     */
    public function testEnableSelected()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // Suppress a single sniff and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress multiple sniffs and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress multiple sniffs and re-enable one.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a category of sniffs and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a whole standard and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

    }//end testEnableSelected()


    /**
     * Test ignoring specific sniffs.
     *
     * @return void
     */
    public function testIgnoreSelected()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // No suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(2, $numWarnings);
        $this->assertCount(2, $warnings);

        // Suppress a single sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress multiple sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Add to supression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'// phpcs:ignore Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a category of sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic.Commenting'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a whole standard.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

    }//end testIgnoreSelected()


    /**
     * Test ignoring specific sniffs.
     *
     * @return void
     */
    public function testCommenting()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // Suppress a single sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo -- Because reasons'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a single sniff and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo --Because reasons'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo   --  Because reasons'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a single sniff using block comments.
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.'    Disable some checks'.PHP_EOL.'    phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'*/'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a single sniff with a multi-line comment.
        $content = '<?php '.PHP_EOL.'// Turn off a check for the next line of code.'.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Ignore an enable before a disable.
        $content = '<?php '.PHP_EOL.'// phpcs:enable Generic.PHP.NoSilencedErrors -- Because reasons'.PHP_EOL.'$var = @delete( $filename );'.PHP_EOL;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

    }//end testCommenting()


}//end class
