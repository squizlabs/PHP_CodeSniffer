<?php
/**
 * Tests for the PHP_CodeSniffer_File:findImplementedInterfaceNames method.
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
 * Tests for the PHP_CodeSniffer_File:findImplementedInterfaceNames method.
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
class Core_File_FindImplementedInterfaceNamesTest extends PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of the test case file.
     *
     * @var PHP_CodeSniffer_File
     */
    private $_phpcsFile;


    /**
     * Initialize & tokenize PHP_CodeSniffer_File with code from the test case file.
     *
     * Methods used for these tests can be found in a test case file in the same
     * directory and with the same name, using the .inc extension.
     *
     * @return void
     */
    public function setUp()
    {
        $pathToTestcases  = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.inc';
        $phpcs            = new PHP_CodeSniffer();
        $this->_phpcsFile = new PHP_CodeSniffer_File(
            $pathToTestcases,
            array(),
            array(),
            $phpcs
        );

        $contents = file_get_contents($pathToTestcases);
        $this->_phpcsFile->start($contents);

    }//end setUp()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_phpcsFile);

    }//end tearDown()


    /**
     * Test a class that implements a single interface.
     *
     * @return void
     */
    public function testImplementedClass()
    {
        $start = ($this->_phpcsFile->numTokens - 1);
        $class = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testImplementedClass */'
        );

        $found = $this->_phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertSame(array('testFIINInterface'), $found);

    }//end testImplementedClass()


    /**
     * Test a class that implements multiple interfaces.
     *
     * @return void
     */
    public function testMultiImplementedClass()
    {
        $start = ($this->_phpcsFile->numTokens - 1);
        $class = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testMultiImplementedClass */'
        );

        $found = $this->_phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertSame(array('testFIINInterface', 'testFIINInterface2'), $found);

    }//end testMultiImplementedClass()


    /**
     * Test a class that implements an interface, using namespaces.
     *
     * @return void
     */
    public function testNamespacedClass()
    {
        $start = ($this->_phpcsFile->numTokens - 1);
        $class = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNamespacedClass */'
        );

        $found = $this->_phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertSame(array('\testFIINInterface'), $found);

    }//end testNamespacedClass()


    /**
     * Test a class that doesn't implement an interface.
     *
     * @return void
     */
    public function testNonImplementedClass()
    {
        $start = ($this->_phpcsFile->numTokens - 1);
        $class = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNonImplementedClass */'
        );

        $found = $this->_phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertFalse($found);

    }//end testNonImplementedClass()


    /**
     * Test an interface.
     *
     * @return void
     */
    public function testInterface()
    {
        $start = ($this->_phpcsFile->numTokens - 1);
        $class = $this->_phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testInterface */'
        );

        $found = $this->_phpcsFile->findImplementedInterfaceNames(($class + 2));
        $this->assertFalse($found);

    }//end testInterface()


}//end class
