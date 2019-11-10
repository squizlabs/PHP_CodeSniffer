<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findEndOfStatement method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class FindEndOfStatementTest extends AbstractMethodUnitTest
{


    /**
     * Test a simple assignment.
     *
     * @return void
     */
    public function testSimpleAssignment()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testSimpleAssignment */') + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 5)], $tokens[$found]);

    }//end testSimpleAssignment()


    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testControlStructure */') + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 6)], $tokens[$found]);

    }//end testControlStructure()


    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testClosureAssignment */') + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
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
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testHeredocFunctionArg */') + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 10)], $tokens[$found]);

        // Find the end of the heredoc.
        $start += 2;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 4)], $tokens[$found]);

        // Find the end of the last arg.
        $start = ($found + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
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
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testSwitch */') + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 28)], $tokens[$found]);

        // Find the end of the case.
        $start += 9;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 8)], $tokens[$found]);

        // Find the end of default case.
        $start += 11;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
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
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testStatementAsArrayValue */') + 7);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 2)], $tokens[$found]);

        // Test long array syntax.
        $start += 12;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 2)], $tokens[$found]);

        // Test same statement outside of array.
        $start += 10;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 3)], $tokens[$found]);

    }//end testStatementAsArrayValue()


    /**
     * Test a use group.
     *
     * @return void
     */
    public function testUseGroup()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testUseGroup */') + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 23)], $tokens[$found]);

    }//end testUseGroup()


    /**
     * Test a use group.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testArrowFunctionArrayValue */') + 7);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 9)], $tokens[$found]);

    }//end testArrowFunctionArrayValue()


}//end class
