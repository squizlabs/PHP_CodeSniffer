<?php
/**
 * Tests for the PHP_CodeSniffer_File:findPrevious method.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Lena Orobei <lena.orobei@gmail.com>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tests for the PHP_CodeSniffer_File:findPrevious method.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Lena Orobei <lena.orobei@gmail.com>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_File_FindPreviousTest extends PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of this file.
     *
     * @var PHP_CodeSniffer_File
     */
    private $_phpcsFile;


    /**
     * Initialize & tokenize PHP_CodeSniffer_File with code from this file.
     *
     * Methods used for these tests can be found at the bottom of
     * this file.
     *
     * @return void
     */
    public function setUp()
    {
        $phpcs = new PHP_CodeSniffer();
        $this->_phpcsFile = new PHP_CodeSniffer_File(
            __FILE__,
            array(),
            array(),
            $phpcs
        );

        $contents = file_get_contents(__FILE__);
        $this->_phpcsFile->start($contents);

    }


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_phpcsFile);

    }


    /**
     * Find token with specific value.
     *
     * @return void
     */
    public function testSpecificValue()
    {
        $start = $this->_phpcsFile->numTokens - 1;
        $tokens = $this->_phpcsFile->getTokens();
        $comment = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testSpecificValue */'
        );
        $this->assertSame('/* testSpecificValue */', $tokens[$comment]['content']);
    }


    /**
     * Find token, which is in array of possible values.
     *
     * @return void
     */
    public function testInArrayValue()
    {
        $start = $this->_phpcsFile->numTokens - 1;
        $tokens = $this->_phpcsFile->getTokens();
        $comment = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testInArrayValue */'
        );

        $found = $this->_phpcsFile->findPrevious(
            T_STRING,
            $comment - 2,
            null,
            false,
            array('strpos', 'stripos')
        );
        $this->assertSame('stripos', $tokens[$found]['content']);
    }
}

// @codingStandardsIgnoreStart
/* testSpecificValue */
$pos = stripos($string, $part); /* testInArrayValue */
// @codingStandardsIgnoreEnd
