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

class ErrorSuppressionTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Test suppressing a single error.
     *
     * @return void
     */
    public function testSuppressError()
    {
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.PHP.LowerCaseConstant');

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

        // Process with inline comment suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with block comment suppression.
        $content = '<?php '.PHP_EOL.'/* @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/* @codingStandardsIgnoreEnd */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** @codingStandardsIgnoreEnd */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

    }//end testSuppressError()


    /**
     * Test suppressing 1 out of 2 errors.
     *
     * @return void
     */
    public function testSuppressSomeErrors()
    {
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.PHP.LowerCaseConstant');

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertEquals(2, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

        // Process with a PHPDoc block suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** @codingStandardsIgnoreEnd */'.PHP_EOL.'$var = TRUE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

    }//end testSuppressSomeErrors()


    /**
     * Test suppressing a single warning.
     *
     * @return void
     */
    public function testSuppressWarning()
    {
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.Commenting.Todo');

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertEquals(1, count($warnings));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'/** @codingStandardsIgnoreEnd */';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

    }//end testSuppressWarning()


    /**
     * Test suppressing a single error using a single line ignore.
     *
     * @return void
     */
    public function testSuppressLine()
    {
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.PHP.LowerCaseConstant');

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertEquals(2, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreLine'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

    }//end testSuppressLine()


    /**
     * Test that using a single line ignore does not interfere with other suppressions.
     *
     * @return void
     */
    public function testNestedSuppressLine()
    {
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.PHP.LowerCaseConstant');

        $ruleset = new Ruleset($config);

        // Process with codingStandardsIgnore[Start|End] suppression and no single line suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with codingStandardsIgnoreLine suppression
        // nested within codingStandardsIgnore[Start|End] suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'// @codingStandardsIgnoreLine'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

    }//end testNestedSuppressLine()


    /**
     * Test suppressing a scope opener.
     *
     * @return void
     */
    public function testSuppressScope()
    {
        return;
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('PEAR', array('PEAR.NamingConventions.ValidVariableName'));

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'function myFunction() {'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//@codingStandardsIgnoreStart'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//@codingStandardsIgnoreEnd'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'function myFunction() {'.PHP_EOL.'/** @codingStandardsIgnoreEnd */'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

    }//end testSuppressScope()


    /**
     * Test suppressing a whole file.
     *
     * @return void
     */
    public function testSuppressFile()
    {
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.Commenting.Todo');

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertEquals(1, count($warnings));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

        // Process with a block comment suppression.
        $content = '<?php '.PHP_EOL.'/* @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

        // Process with docblock suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

    }//end testSuppressFile()


}//end class
