<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findStartOfStatement method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class FindStartOfStatementTest extends AbstractMethodUnitTest
{


    /**
     * Test a simple assignment.
     *
     * @return void
     */
    public function testSimpleAssignment()
    {
        $start = $this->getTargetToken('/* testSimpleAssignment */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 5), $found);

    }//end testSimpleAssignment()


    /**
     * Test a function call.
     *
     * @return void
     */
    public function testFunctionCall()
    {
        $start = $this->getTargetToken('/* testFunctionCall */', T_CLOSE_PARENTHESIS);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

    }//end testFunctionCall()


    /**
     * Test a function call.
     *
     * @return void
     */
    public function testFunctionCallArgument()
    {
        $start = $this->getTargetToken('/* testFunctionCallArgument */', T_VARIABLE, '$b');
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testFunctionCallArgument()


    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = $this->getTargetToken('/* testControlStructure */', T_CLOSE_CURLY_BRACKET);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

    }//end testControlStructure()


    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = $this->getTargetToken('/* testClosureAssignment */', T_CLOSE_CURLY_BRACKET);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 12), $found);

    }//end testClosureAssignment()


    /**
     * Test using a heredoc in a function argument.
     *
     * @return void
     */
    public function testHeredocFunctionArg()
    {
        // Find the start of the function.
        $start = $this->getTargetToken('/* testHeredocFunctionArg */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 10), $found);

        // Find the start of the heredoc.
        $start -= 4;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 4), $found);

        // Find the start of the last arg.
        $start += 2;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testHeredocFunctionArg()


    /**
     * Test parts of a switch statement.
     *
     * @return void
     */
    public function testSwitch()
    {
        // Find the start of the switch.
        $start = $this->getTargetToken('/* testSwitch */', T_CLOSE_CURLY_BRACKET);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 47), $found);

        // Find the start of default case.
        $start -= 5;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

        // Find the start of the second case.
        $start -= 12;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 5), $found);

        // Find the start of the first case.
        $start -= 13;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 8), $found);

        // Test inside the first case.
        $start--;
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

    }//end testSwitch()


    /**
     * Test statements that are array values.
     *
     * @return void
     */
    public function testStatementAsArrayValue()
    {
        // Test short array syntax.
        $start = $this->getTargetToken('/* testStatementAsArrayValue */', T_STRING, 'Datetime');
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 2), $found);

        // Test long array syntax.
        $start += 12;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 2), $found);

        // Test same statement outside of array.
        $start++;
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 9), $found);

        // Test with an array index.
        $start += 17;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 5), $found);

    }//end testStatementAsArrayValue()


    /**
     * Test a use group.
     *
     * @return void
     */
    public function testUseGroup()
    {
        $start = $this->getTargetToken('/* testUseGroup */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 23), $found);

    }//end testUseGroup()


    /**
     * Test arrow function as array value.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionArrayValue */', T_COMMA);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 9), $found);

    }//end testArrowFunctionArrayValue()


    /**
     * Test static arrow function.
     *
     * @return void
     */
    public function testStaticArrowFunction()
    {
        $start = $this->getTargetToken('/* testStaticArrowFunction */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 11), $found);

    }//end testStaticArrowFunction()


    /**
     * Test arrow function with return value.
     *
     * @return void
     */
    public function testArrowFunctionReturnValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionReturnValue */', T_SEMICOLON);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 18), $found);

    }//end testArrowFunctionReturnValue()


    /**
     * Test arrow function used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionAsArgument()
    {
        $start  = $this->getTargetToken('/* testArrowFunctionAsArgument */', T_FN);
        $start += 8;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 8), $found);

    }//end testArrowFunctionAsArgument()


    /**
     * Test arrow function with arrays used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionWithArrayAsArgument()
    {
        $start  = $this->getTargetToken('/* testArrowFunctionWithArrayAsArgument */', T_FN);
        $start += 17;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 17), $found);

    }//end testArrowFunctionWithArrayAsArgument()


    /**
     * Test simple match expression case.
     *
     * @return void
     */
    public function testMatchCase()
    {
        $start = $this->getTargetToken('/* testMatchCase */', T_COMMA);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

    }//end testMatchCase()


    /**
     * Test simple match expression default case.
     *
     * @return void
     */
    public function testMatchDefault()
    {
        $start = $this->getTargetToken('/* testMatchDefault */', T_CONSTANT_ENCAPSED_STRING, "'bar'");
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testMatchDefault()


    /**
     * Test multiple comma-separated match expression case values.
     *
     * @return void
     */
    public function testMatchMultipleCase()
    {
        $start = $this->getTargetToken('/* testMatchMultipleCase */', T_MATCH_ARROW);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

        $start += 6;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 4), $found);

    }//end testMatchMultipleCase()


    /**
     * Test match expression default case with trailing comma.
     *
     * @return void
     */
    public function testMatchDefaultComma()
    {
        $start = $this->getTargetToken('/* testMatchDefaultComma */', T_MATCH_ARROW);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 3), $found);

        $start += 2;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame($start, $found);

    }//end testMatchDefaultComma()


    /**
     * Test match expression with function call.
     *
     * @return void
     */
    public function testMatchFunctionCall()
    {
        $start = $this->getTargetToken('/* testMatchFunctionCall */', T_CLOSE_PARENTHESIS);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 6), $found);

    }//end testMatchFunctionCall()


    /**
     * Test match expression with function call in the arm.
     *
     * @return void
     */
    public function testMatchFunctionCallArm()
    {
        // Check the first case.
        $start = $this->getTargetToken('/* testMatchFunctionCallArm */', T_MATCH_ARROW);
        $found = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 18), $found);

        // Check the second case.
        $start += 24;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 18), $found);

    }//end testMatchFunctionCallArm()


    /**
     * Test match expression with closure.
     *
     * @return void
     */
    public function testMatchClosure()
    {
        $start  = $this->getTargetToken('/* testMatchClosure */', T_LNUMBER);
        $start += 14;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 10), $found);

        $start += 17;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 10), $found);

    }//end testMatchClosure()


    /**
     * Test match expression with array declaration.
     *
     * @return void
     */
    public function testMatchArray()
    {
        // Start of first case statement.
        $start = $this->getTargetToken('/* testMatchArray */', T_LNUMBER);
        $found = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame($start, $found);

        // Comma after first statement.
        $start += 11;
        $found  = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame(($start - 7), $found);

        // Start of second case statement.
        $start += 3;
        $found  = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame($start, $found);

        // Comma after first statement.
        $start += 30;
        $found  = self::$phpcsFile->findStartOfStatement($start);
        $this->assertSame(($start - 26), $found);

    }//end testMatchArray()


    /**
     * Test nested match expressions.
     *
     * @return void
     */
    public function testNestedMatch()
    {
        $start  = $this->getTargetToken('/* testNestedMatch */', T_LNUMBER);
        $start += 30;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 26), $found);

        $start -= 4;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 1), $found);

        $start -= 3;
        $found  = self::$phpcsFile->findStartOfStatement($start);

        $this->assertSame(($start - 2), $found);

    }//end testNestedMatch()


}//end class
