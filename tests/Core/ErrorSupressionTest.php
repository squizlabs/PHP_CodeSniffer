<?php
/**
 * Tests for PHP_CodeSniffer error supression tags.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Tests for PHP_CodeSniffer error supression tags.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_ErrorSupressionTest extends PHPUnit_Framework_TestCase
{


    /**
     * Test supressing a single error.
     *
     * @return void
     */
    public function testSupressError()
    {
        $phpcs = new PHP_CodeSniffer();
        $phpcs->setTokenListeners('Generic', array('Generic_Sniffs_PHP_LowerCaseConstantSniff'));
        $phpcs->populateTokenListeners();

        // Process without supression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;';
        $phpcs->processFile('noSupressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[0];

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

        // Process with supression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $phpcs->processFile('supressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[1];

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertEquals(0, count($errors));

    }//end testSupressError()


    /**
     * Test supressing 1 out of 2 errors.
     *
     * @return void
     */
    public function testSupressSomeErrors()
    {
        $phpcs = new PHP_CodeSniffer();
        $phpcs->setTokenListeners('Generic', array('Generic_Sniffs_PHP_LowerCaseConstantSniff'));
        $phpcs->populateTokenListeners();

        // Process without supression.
        $content = '<?php '.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;';
        $phpcs->processFile('noSupressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[0];

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(2, $numErrors);
        $this->assertEquals(2, count($errors));

        // Process with supression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// @codingStandardsIgnoreEnd'.PHP_EOL.'$var = TRUE;';
        $phpcs->processFile('supressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[1];

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(1, $numErrors);
        $this->assertEquals(1, count($errors));

    }//end testSupressSomeErrors()


    /**
     * Test supressing a single warning.
     *
     * @return void
     */
    public function testSupressWarning()
    {
        $phpcs = new PHP_CodeSniffer();
        $phpcs->setTokenListeners('Generic', array('Generic_Sniffs_Commenting_TodoSniff'));
        $phpcs->populateTokenListeners();

        // Process without supression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $phpcs->processFile('noSupressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[0];

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertEquals(1, count($warnings));

        // Process with supression.
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $phpcs->processFile('supressionTest.php', $content);

        $files = $phpcs->getFiles();
        $file  = $files[1];

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertEquals(0, count($warnings));

    }//end testSupressWarning()


}//end class

?>
