<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findEndOfStatement method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHPUnit\Framework\TestCase;

class FindEndOfStatementTest extends TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of the test case file.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    private $phpcsFile;


    /**
     * Initialize & tokenize \PHP_CodeSniffer\Files\File with code from the test case file.
     *
     * Methods used for these tests can be found in a test case file in the same
     * directory and with the same name, using the .inc extension.
     *
     * @return void
     */
    public function setUp()
    {
        $config            = new Config();
        $config->standards = ['Generic'];

        $ruleset = new Ruleset($config);

        $pathToTestFile  = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.inc';
        $this->phpcsFile = new DummyFile(file_get_contents($pathToTestFile), $ruleset, $config);
        $this->phpcsFile->process();

    }//end setUp()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->phpcsFile);

    }//end tearDown()


    /**
     * Test a simple assignment.
     *
     * @return void
     */
    public function testSimpleAssignment()
    {
        $start = ($this->phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testSimpleAssignment */') + 2);
        $found = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 5)], $tokens[$found]);

    }//end testSimpleAssignment()


    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = ($this->phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testControlStructure */') + 2);
        $found = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 6)], $tokens[$found]);

    }//end testControlStructure()


    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = ($this->phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testClosureAssignment */') + 2);
        $found = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 13)], $tokens[$found]);

    }//end testClosureAssignment()


    /**
     * Test using a heredoc in a function argument.
     *
     * @return void
     */
    public function testHeredocFunctionArg()
    {
        // Find the end of the function.
        $start = ($this->phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testHeredocFunctionArg */') + 2);
        $found = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 10)], $tokens[$found]);

        // Find the end of the heredoc.
        $start += 2;
        $found  = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 4)], $tokens[$found]);

        // Find the end of the last arg.
        $start = ($found + 2);
        $found = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[$start], $tokens[$found]);

    }//end testHeredocFunctionArg()


    /**
     * Test parts of a switch statement.
     *
     * @return void
     */
    public function testSwitch()
    {
        // Find the end of the switch.
        $start = ($this->phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testSwitch */') + 2);
        $found = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 28)], $tokens[$found]);

        // Find the end of the case.
        $start += 9;
        $found  = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 8)], $tokens[$found]);

        // Find the end of default case.
        $start += 11;
        $found  = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 6)], $tokens[$found]);

    }//end testSwitch()


    /**
     * Test statements that are array values.
     *
     * @return void
     */
    public function testStatementAsArrayValue()
    {
        // Test short array syntax.
        $start = ($this->phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testStatementAsArrayValue */') + 7);
        $found = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 2)], $tokens[$found]);

        // Test long array syntax.
        $start += 12;
        $found  = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 2)], $tokens[$found]);

        // Test same statement outside of array.
        $start += 10;
        $found  = $this->phpcsFile->findEndOfStatement($start);

        $tokens = $this->phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 3)], $tokens[$found]);

    }//end testStatementAsArrayValue()


}//end class
