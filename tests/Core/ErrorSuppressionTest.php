<?php
/**
 * Tests for PHP_CodeSniffer error suppression tags.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Tests for PHP_CodeSniffer error suppression tags.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
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
        $phpcs->setTokenListeners('PEAR', array('Generic_Sniffs_PHP_LowerCaseConstantSniff'));
        $phpcs->populateTokenListeners();

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;';
        $phpcs->processFile('noSuppressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[0];

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $phpcs->processFile('suppressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[1];

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
        $phpcs->setTokenListeners('PEAR', array('Generic_Sniffs_PHP_LowerCaseConstantSniff'));
        $phpcs->populateTokenListeners();

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;';
        $phpcs->processFile('noSuppressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[0];

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertEquals(2, count($errors));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd'.PHP_EOL.'$var = TRUE;';
        $phpcs->processFile('suppressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[1];

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
        $phpcs->setTokenListeners('Squiz', array('Generic_Sniffs_Commenting_TodoSniff'));
        $phpcs->populateTokenListeners();

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $phpcs->processFile('noSuppressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[0];

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertEquals(1, count($warnings));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $phpcs->processFile('suppressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[1];

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

    }//end testSuppressWarning()


    /**
     * Test suppressing a whole file.
     *
     * @return void
     */
    public function testSuppressFile()
    {
        $phpcs = new PHP_CodeSniffer();
        $phpcs->setTokenListeners('Squiz', array('Squiz_Sniffs_Commenting_FileCommentSniff'));
        $phpcs->populateTokenListeners();

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;';
        $phpcs->processFile('noSuppressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[0];

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));
        $this->assertEquals(1, count($files));

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreFile'.PHP_EOL.'$var = FALSE;';
        $phpcs->processFile('suppressionTest.php', $content);

        // The file shouldn't even be added to the $files array.
        $files = $phpcs->getFiles();
        $this->assertEquals(1, count($files));

    }//end testSupressError()


}//end class

?>
