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
        $start = $this->getTargetToken('/* testSimpleAssignment */', T_VARIABLE);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 5), $found);

    }//end testSimpleAssignment()


    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = $this->getTargetToken('/* testControlStructure */', T_WHILE);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 6), $found);

    }//end testControlStructure()


    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = $this->getTargetToken('/* testClosureAssignment */', T_VARIABLE, '$a');
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 13), $found);

    }//end testClosureAssignment()


    /**
     * Test using a heredoc in a function argument.
     *
     * @return void
     */
    public function testHeredocFunctionArg()
    {
        // Find the end of the function.
        $start = $this->getTargetToken('/* testHeredocFunctionArg */', T_STRING, 'myFunction');
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 10), $found);

        // Find the end of the heredoc.
        $start += 2;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 4), $found);

        // Find the end of the last arg.
        $start = ($found + 2);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame($start, $found);

    }//end testHeredocFunctionArg()


    /**
     * Test parts of a switch statement.
     *
     * @return void
     */
    public function testSwitch()
    {
        // Find the end of the switch.
        $start = $this->getTargetToken('/* testSwitch */', T_SWITCH);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 28), $found);

        // Find the end of the case.
        $start += 9;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 8), $found);

        // Find the end of default case.
        $start += 11;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 6), $found);

    }//end testSwitch()


    /**
     * Test statements that are array values.
     *
     * @return void
     */
    public function testStatementAsArrayValue()
    {
        // Test short array syntax.
        $start = $this->getTargetToken('/* testStatementAsArrayValue */', T_NEW);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 2), $found);

        // Test long array syntax.
        $start += 12;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 2), $found);

        // Test same statement outside of array.
        $start += 10;
        $found  = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 3), $found);

    }//end testStatementAsArrayValue()


    /**
     * Test a use group.
     *
     * @return void
     */
    public function testUseGroup()
    {
        $start = $this->getTargetToken('/* testUseGroup */', T_USE);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 23), $found);

    }//end testUseGroup()


    /**
     * Test arrow function as array value.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionArrayValue */', T_FN);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 9), $found);

    }//end testArrowFunctionArrayValue()


    /**
     * Test static arrow function.
     *
     * @return void
     */
    public function testStaticArrowFunction()
    {
        $static = $this->getTargetToken('/* testStaticArrowFunction */', T_STATIC);
        $fn     = $this->getTargetToken('/* testStaticArrowFunction */', T_FN);

        $endOfStatementStatic = self::$phpcsFile->findEndOfStatement($static);
        $endOfStatementFn     = self::$phpcsFile->findEndOfStatement($fn);

        $this->assertSame($endOfStatementFn, $endOfStatementStatic);

    }//end testStaticArrowFunction()


    /**
     * Test arrow function with return value.
     *
     * @return void
     */
    public function testArrowFunctionReturnValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionReturnValue */', T_FN);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 18), $found);

    }//end testArrowFunctionReturnValue()


    /**
     * Test arrow function used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionAsArgument()
    {
        $start = $this->getTargetToken('/* testArrowFunctionAsArgument */', T_FN);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 8), $found);

    }//end testArrowFunctionAsArgument()


    /**
     * Test arrow function with arrays used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionWithArrayAsArgument()
    {
        $start = $this->getTargetToken('/* testArrowFunctionWithArrayAsArgument */', T_FN);
        $found = self::$phpcsFile->findEndOfStatement($start);

        $this->assertSame(($start + 17), $found);

    }//end testArrowFunctionWithArrayAsArgument()


}//end class
