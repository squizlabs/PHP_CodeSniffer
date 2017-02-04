<?php
/**
 * Tests for PHP_CodeSniffer error suppression tags.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tests for PHP_CodeSniffer error suppression tags.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 *
 * @group utilityMethods
 */
class Core_ErrorSuppressionTest extends PHPUnit_Framework_TestCase
{


    /**
     * Test suppressing a single error.
     *
     * @return void
     */
    public function testSuppressError()
    {
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('PEAR', array('Generic.PHP.LowerCaseConstant'));

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;';
        $file    = $phpcs->processFile('noSuppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

        // Process with inline comment suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with block comment suppression.
        $content = '<?php '.PHP_EOL.'/* @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/* @codingStandardsIgnoreEnd */';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** @codingStandardsIgnoreEnd */';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

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
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('Generic', array('Generic.PHP.LowerCaseConstant'));

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;';
        $file    = $phpcs->processFile('noSuppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertEquals(2, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd'.PHP_EOL.'$var = TRUE;';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

        // Process with a PHPDoc block suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'/** @codingStandardsIgnoreEnd */'.PHP_EOL.'$var = TRUE;';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

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
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('Generic', array('Generic.Commenting.Todo'));

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $file    = $phpcs->processFile('noSuppressionTest.php', $content);

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertEquals(1, count($warnings));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

        // Process with a PHPDoc block suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'/** @codingStandardsIgnoreEnd */';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

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
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('Generic', array('Generic.PHP.LowerCaseConstant'));

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = $phpcs->processFile('noSuppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertEquals(2, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreLine'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = FALSE;';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

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
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('Generic', array('Generic.PHP.LowerCaseConstant'));

        // Process with codingStandardsIgnore[Start|End] suppression and no single line suppression.
        $content = '<?php '.PHP_EOL.
            '// @codingStandardsIgnoreStart'.PHP_EOL.
            '$var = FALSE;'.PHP_EOL.
            '$var = TRUE;'.PHP_EOL.
            '// @codingStandardsIgnoreEnd';
        $file    = $phpcs->processFile('oneSuppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with codingStandardsIgnoreLine suppression
        // nested within codingStandardsIgnore[Start|End] suppression.
        $content = '<?php '.PHP_EOL.
            '// @codingStandardsIgnoreStart'.PHP_EOL.
            '// @codingStandardsIgnoreLine'.PHP_EOL.
            '$var = FALSE;'.PHP_EOL.
            '$var = TRUE;'.PHP_EOL.
            '// @codingStandardsIgnoreEnd';
        $file    = $phpcs->processFile('nestedSuppressionTest.php', $content);

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
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('PEAR', array('PEAR.NamingConventions.ValidVariableName'));

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'function myFunction() {'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = $phpcs->processFile('noSuppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//@codingStandardsIgnoreStart'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//@codingStandardsIgnoreEnd'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

        // Process with a PhpDoc Block suppression.
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
        $phpcs = new PHP_CodeSniffer();
        $phpcs->initStandard('Generic', array('Generic.Commenting.Todo'));

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertEquals(1, count($warnings));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

        // Process with a block comment suppression.
        $content = '<?php '.PHP_EOL.'/* @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

        // Process with docblock suppression.
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = $phpcs->processFile('suppressionTest.php', $content);

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

    }//end testSuppressFile()


}//end class

?>
